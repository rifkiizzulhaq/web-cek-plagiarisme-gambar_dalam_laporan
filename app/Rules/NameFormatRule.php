<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NameFormatRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 1. Cek panjang minimal
        if (strlen($value) < 10) {
            $fail('Nama harus memiliki minimal 10 karakter.');
            return;
        }

        // 2. Cek karakter pertama tidak boleh angka
        if (is_numeric(substr($value, 0, 1))) {
            $fail('Nama tidak boleh diawali dengan angka.');
            return;
        }

        // 3. Cek jika ada angka, harus di akhir
        // Temukan posisi angka pertama
        preg_match('/\d/', $value, $matches, PREG_OFFSET_CAPTURE);
        if (!empty($matches)) {
            $firstDigitPos = $matches[0][1];
            // Ambil bagian setelah angka pertama
            $substringAfterFirstDigit = substr($value, $firstDigitPos);
            // Cek apakah semua karakter setelahnya adalah angka
            if (!ctype_digit($substringAfterFirstDigit)) {
                $fail('Jika ada angka, harus berada di akhir nama.');
            }
        }
    }
}