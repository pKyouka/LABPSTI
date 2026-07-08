<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AllowedDomain implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $lower = strtolower($value);

        $allowed = $this->domain === 'lab'
            ? ['@lab.com']
            : ['@unisa.ac.id', '@unisayogya.ac.id'];

        foreach ($allowed as $domain) {
            if (str_ends_with($lower, $domain)) {
                return;
            }
        }

        $hint = $this->domain === 'lab'
            ? '@lab.com (admin)'
            : '@unisa.ac.id / @unisy.ac.id (mahasiswa) atau NIM angka';

        $fail("Format email atau NIM tidak valid. Gunakan {$hint}.");
    }

    public function __construct(private string $domain = 'unisa')
    {
    }
}
