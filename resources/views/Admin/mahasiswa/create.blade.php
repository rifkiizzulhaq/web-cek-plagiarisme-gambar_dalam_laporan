@extends('layouts.main')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Tambah Akun Mahasiswa Baru</h1>

    <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-xl p-6">
        <form id="data-form" action="{{ route('admin.mahasiswa.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                {{-- NIM --}}
                <div>
                    <label for="nim" class="block text-sm font-medium mb-2 dark:text-white">NIM</label>
                    <input type="text" name="nim" id="nim" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" value="{{ old('nim') }}" placeholder="Contoh: 21030..." required>
                    @error('nim') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- Nama --}}
                <div>
                    <label for="name" class="block text-sm font-medium mb-2 dark:text-white">Nama</label>
                    <input type="text" name="name" id="name" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                    @error('name') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                </div>
                
                {{-- Program Studi (Dropdown) --}}
                <div>
                    <label for="prodi" class="block text-sm font-medium mb-2 dark:text-white">Program Studi</label>
                    <select id="prodi" name="prodi" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" required>
                        <option value="" disabled selected>Pilih Program Studi</option>
                        @foreach($prodiOptions as $prodi)
                            <option value="{{ $prodi }}" {{ old('prodi') == $prodi ? 'selected' : '' }}>{{ $prodi }}</option>
                        @endforeach
                    </select>
                    @error('prodi') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Angkatan --}}
                    <div>
                        <label for="angkatan" class="block text-sm font-medium mb-2 dark:text-white">Tahun Angkatan</label>
                        <input type="number" name="angkatan" id="angkatan" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" value="{{ old('angkatan') }}" placeholder="Contoh: 2021" required>
                        @error('angkatan') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="kelas_detail" class="block text-sm font-medium mb-2 dark:text-white">Detail Kelas</label>
                        <div class="flex rounded-lg shadow-sm">
                            <div class="px-4 inline-flex items-center min-w-fit rounded-s-md border border-e-0 border-gray-200 bg-gray-50 dark:bg-neutral-700 dark:border-neutral-600">
                                <span id="prodi-prefix" class="text-sm text-gray-500 dark:text-neutral-400">Pilih Prodi</span>
                            </div>
                            <input type="text" name="kelas_detail" id="kelas_detail" class="py-3 px-4 block w-full border-gray-200 shadow-sm rounded-e-lg text-sm" value="{{ old('kelas_detail') }}" placeholder="Contoh: 4A" required>
                        </div>
                        @error('kelas_detail') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label for="email_username" class="block text-sm font-medium mb-2 dark:text-white">Email</label>
                    <div class="flex rounded-lg shadow-sm">
                        <input type="text" id="email_username" class="py-3 px-4 block w-full border-gray-200 shadow-sm rounded-s-lg text-sm" placeholder="Nama Email" required>
                        <div class="px-4 inline-flex items-center min-w-fit rounded-e-md border border-s-0 border-gray-200 bg-gray-50 dark:bg-neutral-700 dark:border-neutral-600">
                            <span class="text-sm text-gray-500 dark:text-neutral-400">@gmail.com</span>
                        </div>
                    </div>
                    <input type="hidden" name="email" id="full_email">
                    @error('email') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- Password & Konfirmasi --}}
                <div>
                    <label for="password" class="block text-sm font-medium mb-2 dark:text-white">Password</label>
                    <input type="password" name="password" id="password" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" placeholder="Masukkan password" required>
                     @error('password') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-2 dark:text-white">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" placeholder="Ketik ulang password" required>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-x-2">
                <a href="{{ route('admin.mahasiswa.index') }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const prodiSelect = document.getElementById('prodi');
    const prodiPrefixSpan = document.getElementById('prodi-prefix');
    const emailUsernameInput = document.getElementById('email_username');
    const fullEmailInput = document.getElementById('full_email');
    const form = document.getElementById('data-form');
    
    const prodiAbbreviations = @json($prodiAbbreviations);

    if (!form) return;

    let isFormDirty = false;

    form.addEventListener('input', function() {
        isFormDirty = true;
    });
    form.addEventListener('submit', function() {
        isFormDirty = false;
    });

    window.addEventListener('beforeunload', function (e) {
        if (isFormDirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    function updatePrefix() {
        const selectedProdi = prodiSelect.value;
        const prefix = prodiAbbreviations[selectedProdi] || 'Pilih Prodi';
        prodiPrefixSpan.textContent = prefix + '-';
    }

    function updateFullEmail() {
        if(emailUsernameInput && fullEmailInput) {
            let username = emailUsernameInput.value;
            if (username.includes('@gmail.com')) {
                fullEmailInput.value = username;
            } else {
                fullEmailInput.value = username + '@gmail.com';
            }
        }
    }

    if(emailUsernameInput) {
        emailUsernameInput.addEventListener('input', updateFullEmail);
        updateFullEmail(); 
    }

    updatePrefix();
    prodiSelect.addEventListener('change', updatePrefix);
});
</script>
@endpush