<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\NameFormatRule;
use App\Rules\EmailUsernameFormatRule;
use App\Rules\KelasDetailFormatRule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', new NameFormatRule],
            'email' => ['required', 'string', 'email', 'max:255', new EmailUsernameFormatRule, Rule::unique(User::class)->ignore($this->user()->id)],
        ];

        if ($this->user()->hasRole('mahasiswa')) {
            $prodiOptions = [
                'D3 - Teknik Informatika',
                'D3 - Teknik Pendingin dan Tata Udara',
                'D4 - Rekayasa Perangkat Lunak',
            ];

            $rules['nim'] = ['required', 'numeric', 'digits_between:7,8', Rule::unique(User::class)->ignore($this->user()->id)];
            $rules['prodi'] = ['required', 'string', Rule::in($prodiOptions)];
            $rules['angkatan'] = ['required', 'numeric', 'digits:4', 'min:2020'];
            $rules['kelas_detail'] = ['required', 'string', new KelasDetailFormatRule($this->user()->prodi)];
        }

        return $rules;
    }
    /**
     * Mendapatkan pesan error kustom untuk aturan validasi.
     */
    public function messages(): array
    {
        return [
            'nim.digits_between' => 'NIM harus terdiri dari 7 atau 8 angka.',
            'angkatan.digits' => 'Tahun angkatan harus 4 angka.',
            'angkatan.min' => 'Tahun angkatan tidak boleh kurang dari 2020.',
            'kelas_detail.regex' => 'Format Detail Kelas harus satu angka diikuti satu huruf (contoh: 4A).',
        ];
    }
}
