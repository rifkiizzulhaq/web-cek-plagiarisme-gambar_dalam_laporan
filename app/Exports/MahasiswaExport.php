<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Role;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MahasiswaExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Ambil ID peran mahasiswa
        $mahasiswaRoleId = Role::where('name', 'mahasiswa')->first()->id;

        // Ambil semua data mahasiswa dan pilih kolom yang ingin diekspor
        return User::where('role_id', $mahasiswaRoleId)
                    ->select('nim', 'name', 'email', 'prodi', 'angkatan', 'kelas')
                    ->orderBy('nim', 'asc')
                    ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Definisikan judul untuk setiap kolom di file Excel
        return [
            'NIM',
            'Nama Lengkap',
            'Email',
            'Program Studi',
            'Angkatan',
            'Kelas',
        ];
    }
}