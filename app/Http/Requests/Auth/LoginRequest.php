<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'n' => ['required', 'string'],
            'p' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $username = (string) $this->input('n');
        $password = (string) $this->input('p');

        // Allow local admin login using email and password.
        if (filter_var($username, FILTER_VALIDATE_EMAIL) && Auth::attempt(['email' => $username, 'password' => $password, 'role' => 'admin'], $this->boolean('remember'))) {
            RateLimiter::clear($this->throttleKey());

            return;
        }

        $loginUrl = (string) config('services.unisa_auth.login_url');

        if ($loginUrl === '') {
            throw ValidationException::withMessages([
                'n' => 'Konfigurasi URL login eksternal belum diatur.',
            ]);
        }

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->timeout(15)
                ->post($loginUrl, [
                    'n' => $username,
                    'p' => $password,
                ]);
        } catch (Throwable) {
            throw ValidationException::withMessages([
                'n' => 'Layanan login eksternal sedang tidak dapat diakses.',
            ]);
        }

        if (! $response->successful() || ! $this->isExternalAuthAccepted($response->json(), $response->body())) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'n' => trans('auth.failed'),
            ]);
        }

        $displayName = $this->resolveMahasiswaNameByNim($username)
            ?: $this->resolveDisplayName($response->json(), $response->body())
            ?: $username;

        $user = User::updateOrCreate(
            ['email' => $username.'@unisa-auth.local'],
            [
                'name' => $displayName,
                'password' => Hash::make(Str::random(40)),
                'role' => 'mahasiswa',
            ]
        );

        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Read full name from mahasiswa table by NIM without modifying external DB.
     */
    private function resolveMahasiswaNameByNim(string $nim): ?string
    {
        $connection = (string) config('services.unisa_auth.mahasiswa_connection', 'simlitabmas');
        $table = (string) config('services.unisa_auth.mahasiswa_table', 'ak_v_mahasiswa');

        if ($connection === '' || $table === '' || $nim === '') {
            return null;
        }

        try {
            $name = DB::connection($connection)
                ->table($table)
                ->where('nim', $nim)
                ->value('namalengkap');
        } catch (Throwable) {
            return null;
        }

        if (! is_string($name) || trim($name) === '') {
            return null;
        }

        return trim($name);
    }

    /**
     * Resolve display name from external response payload.
     */
    private function resolveDisplayName(mixed $jsonBody, string $rawBody): ?string
    {
        if (is_array($jsonBody)) {
            $possibleKeys = ['nama', 'name', 'fullname', 'full_name'];

            foreach ($possibleKeys as $key) {
                $value = data_get($jsonBody, $key) ?? data_get($jsonBody, 'data.'.$key);

                if (is_string($value) && trim($value) !== '') {
                    return trim($value);
                }
            }
        }

        if (preg_match('/(?:nama|name)\s*[:=]\s*([\p{L}\s\.\'-]+)/iu', $rawBody, $matches) === 1) {
            return trim($matches[1]);
        }

        return null;
    }

    /**
     * Determine whether the external service indicates a successful login.
     */
    private function isExternalAuthAccepted(mixed $jsonBody, string $rawBody): bool
    {
        if (is_array($jsonBody)) {
            // Explicit isallowed field takes priority.
            $isAllowed = data_get($jsonBody, 'isallowed');

            if (is_bool($isAllowed)) {
                if ($isAllowed === false) {
                    return false;
                }
            } elseif (is_string($isAllowed)) {
                $normalized = Str::lower(trim($isAllowed));
                if (in_array($normalized, ['false', 'no', '0', 'n'], true)) {
                    return false;
                }
                if (in_array($normalized, ['true', 'yes', '1', 'y'], true)) {
                    // Still fall through to check status hints below.
                }
            }

            $statusHints = [
                data_get($jsonBody, 'status'),
                data_get($jsonBody, 'success'),
                data_get($jsonBody, 'authenticated'),
                data_get($jsonBody, 'data.status'),
                data_get($jsonBody, 'data.success'),
            ];

            foreach ($statusHints as $hint) {
                if (is_bool($hint)) {
                    return $hint;
                }

                if (is_string($hint)) {
                    $normalized = Str::lower(trim($hint));

                    if (in_array($normalized, ['false', 'fail', 'failed', 'error', 'invalid', '0'], true)) {
                        return false;
                    }

                    if (in_array($normalized, ['true', 'ok', 'success', 'berhasil', 'valid', '1'], true)) {
                        return true;
                    }
                }
            }
        }

        $normalizedBody = Str::lower($rawBody);

        if (preg_match('/(gagal|invalid|salah|error|tidak\s+valid|login\s+failed)/u', $normalizedBody) === 1) {
            return false;
        }

        return true;
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'n' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('n')).'|'.$this->ip());
    }
}
