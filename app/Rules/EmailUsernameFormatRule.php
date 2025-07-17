<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailUsernameFormatRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Ambil bagian sebelum @
        $username = explode('@gmail.com', $value)[0];

        // 1. Cek panjang minimal nama email
        if (strlen($username) < 10) {
            $fail('Nama email harus memiliki minimal 10 karakter.');
            return;
        }

        // 2. Gunakan Regular Expression untuk format
        // Aturan: Harus diawali huruf, setelahnya boleh huruf atau angka.
        // Ini secara efektif melarang angka di tengah.
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9._]*$/', $username)) {
            $fail('Format nama email tidak valid. Hanya boleh berisi huruf, angka (di akhir), titik, atau underscore, dan harus diawali dengan huruf.');
        }
    }
}