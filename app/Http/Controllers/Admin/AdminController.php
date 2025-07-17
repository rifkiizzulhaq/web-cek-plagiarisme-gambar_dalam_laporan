<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Rules\NameFormatRule;
use App\Rules\EmailUsernameFormatRule;
use App\Rules\KelasDetailFormatRule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MahasiswaExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    protected $prodiOptions = [
        'D3 - Teknik Informatika',
        'D3 - Teknik Pendingin dan Tata Udara',
        'D4 - Rekayasa Perangkat Lunak',
    ];

    protected $prodiAbbreviations = [
        'D3 - Teknik Informatika' => 'D3-TI',
        'D3 - Teknik Pendingin dan Tata Udara' => 'D3-TPTU',
        'D4 - Rekayasa Perangkat Lunak' => 'D4-RPL',
    ];

    public function Index()
    {
        return view('Admin.admin-halaman-utama.AdminHalamanUtama');
    }

    public function indexMahasiswa(Request $request)
    {
        $mahasiswaRole = Role::where('name', 'mahasiswa')->first();
        $query = User::where('role_id', $mahasiswaRole->id);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('nim', 'like', '%' . $search . '%');
            });
        }

        $mahasiswas = $query->latest()->paginate(10)->withQueryString();
        return view('Admin.mahasiswa.index', compact('mahasiswas'));
    }

    public function createMahasiswa()
    {
        return view('Admin.mahasiswa.create', [
            'prodiOptions' => $this->prodiOptions,
            'prodiAbbreviations' => $this->prodiAbbreviations,
        ]);
    }

    public function storeMahasiswa(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', new NameFormatRule],
            'nim' => ['required', 'numeric', 'digits_between:7,8', 'unique:users,nim'],
            'prodi' => ['required', 'string', Rule::in($this->prodiOptions)],
            'angkatan' => ['required', 'numeric', 'digits:4', 'min:2020'],
            'kelas_detail' => ['required', 'string', new KelasDetailFormatRule($request->prodi)],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', new EmailUsernameFormatRule],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $mahasiswaRole = Role::where('name', 'mahasiswa')->first();
        
        
        $prodiPrefix = $this->prodiAbbreviations[$request->prodi] ?? 'PRODI';
        $kelasDetailUpper = strtoupper($request->kelas_detail);
        $kelasString = "{$prodiPrefix}-{$kelasDetailUpper}";

        User::create([
            'name' => $request->name, 'email' => $request->email, 'nim' => $request->nim,
            'prodi' => $request->prodi, 'angkatan' => $request->angkatan,
            'kelas' => $kelasString, 
            'password' => Hash::make($request->password), 'role_id' => $mahasiswaRole->id,
        ]);

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Akun mahasiswa berhasil ditambahkan.');
    }

    public function showMahasiswa(User $mahasiswa)
    {
        $files = $mahasiswa->files()->orderBy('created_at', 'desc')->paginate(10);
        return view('Admin.mahasiswa.show', compact('mahasiswa', 'files'));
    }

    public function editMahasiswa(User $mahasiswa)
    {
        return view('Admin.mahasiswa.edit', [
            'mahasiswa' => $mahasiswa,
            'prodiOptions' => $this->prodiOptions,
            'prodiAbbreviations' => $this->prodiAbbreviations,
        ]);
    }

    public function updateMahasiswa(Request $request, User $mahasiswa)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', new NameFormatRule],
            'nim' => ['required', 'numeric', 'digits_between:7,8', 'unique:users,nim,' . $mahasiswa->id],
            'prodi' => ['required', 'string', Rule::in($this->prodiOptions)],
            'angkatan' => ['required', 'numeric', 'digits:4', 'min:2020'],
            'kelas_detail' => ['required', 'string', new KelasDetailFormatRule($request->prodi)],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $mahasiswa->id, new EmailUsernameFormatRule],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $prodiPrefix = $this->prodiAbbreviations[$request->prodi] ?? 'PRODI';
        $kelasDetailUpper = strtoupper($request->kelas_detail);
        $kelasString = "{$prodiPrefix}-{$kelasDetailUpper}";

        $mahasiswa->fill($request->except(['password', 'kelas']));
        $mahasiswa->kelas = $kelasString;

        if ($request->filled('password')) {
            $mahasiswa->password = Hash::make($request->password);
        }
        
        $mahasiswa->save();

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroyMahasiswa(User $mahasiswa)
    {
        $mahasiswa->delete();
        return redirect()->route('admin.mahasiswa.index')
                         ->with('success', 'Akun mahasiswa berhasil dihapus.');
    }

    public function bulkDestroyMahasiswa(Request $request)
    {
        $request->validate([
            'ids' => 'required|json'
        ]);

        $ids = json_decode($request->ids);

        // Pastikan IDs adalah array dan tidak kosong
        if (is_array($ids) && count($ids) > 0) {
            // Hapus hanya pengguna dengan peran mahasiswa untuk keamanan
            $mahasiswaRole = Role::where('name', 'mahasiswa')->first();
            User::where('role_id', $mahasiswaRole->id)->whereIn('id', $ids)->delete();

            return redirect()->route('admin.mahasiswa.index')
                            ->with('success', count($ids) . ' akun mahasiswa berhasil dihapus.');
        }

        return redirect()->route('admin.mahasiswa.index')
                        ->with('error', 'Tidak ada mahasiswa yang dipilih untuk dihapus.');
    }
    public function exportMahasiswa() 
    {
        return Excel::download(new MahasiswaExport, 'daftar-mahasiswa.xlsx');
    }
}