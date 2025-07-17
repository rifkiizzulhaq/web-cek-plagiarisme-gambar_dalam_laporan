<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class KelasDetailFormatRule implements ValidationRule
{
    protected $prodi;

    /**
     * Membuat instance aturan baru.
     *
     * @param string|null $prodi
     */
    public function __construct($prodi)
    {
        $this->prodi = $prodi;
    }

    /**
     * Jalankan aturan validasi.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^(\d)([A-Za-z])$/', $value, $matches)) {
            $fail('Format Detail Kelas harus satu angka diikuti satu huruf (contoh: 4A).');
            return;
        }

        $tahunKelas = (int) $matches[1];

        if (str_starts_with($this->prodi, 'D4')) {
            if ($tahunKelas < 1 || $tahunKelas > 4) {
                $fail('Untuk program D4, tahun kelas harus antara 1 dan 4.');
            }
        } elseif (str_starts_with($this->prodi, 'D3')) {
            if ($tahunKelas < 1 || $tahunKelas > 3) {
                $fail('Untuk program D3, tahun kelas harus antara 1 dan 3.');
            }
        } else {
            $fail('Program studi yang dipilih tidak valid.');
        }
    }
}