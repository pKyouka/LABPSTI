<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMahasiswa(): bool
    {
        return (string) $this->role === 'mahasiswa' || $this->externalNim() !== null;
    }

    public function getDisplayNameAttribute(): string
    {
        $nim = $this->externalNim();

        if ($nim === null) {
            return (string) $this->name;
        }

        $connection = (string) config('services.unisa_auth.mahasiswa_connection', 'simlitabmas');
        $table = (string) config('services.unisa_auth.mahasiswa_table', 'ak_v_mahasiswa');

        try {
            $name = DB::connection($connection)
                ->table($table)
                ->where('nim', $nim)
                ->value('namalengkap');
        } catch (Throwable) {
            return (string) $this->name;
        }

        if (! is_string($name) || trim($name) === '') {
            return (string) $this->name;
        }

        return trim($name);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(\App\Models\Notification::class, 'notifiable_id')
            ->where('notifiable_type', static::class);
    }

    public function unreadNotificationCount(): int
    {
        return (int) DB::table('notifications')
            ->where('notifiable_type', static::class)
            ->where('notifiable_id', $this->id)
            ->whereNull('read_at')
            ->count();
    }

    public function externalNim(): ?string
    {
        if (Str::endsWith((string) $this->email, '@unisa-auth.local')) {
            $nimFromEmail = Str::before((string) $this->email, '@');

            if ($nimFromEmail !== '') {
                return $nimFromEmail;
            }
        }

        $name = trim((string) $this->name);

        if ($name !== '' && preg_match('/^\d+$/', $name) === 1) {
            return $name;
        }

        return null;
    }
}
