@extends('layouts.main')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Edit Akun Mahasiswa</h1>

    <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-xl p-6">
        <form id="data-form" action="{{ route('admin.mahasiswa.update', $mahasiswa->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                {{-- NIM --}}
                <div>
                    <label for="nim" class="block text-sm font-medium mb-2 dark:text-white">NIM</label>
                    <input type="text" name="nim" id="nim" placeholder="Contoh: 21xxx" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" value="{{ old('nim', $mahasiswa->nim) }}" required>
                    @error('nim') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                </div>
                {{-- Nama --}}
                <div>
                    <label for="name" class="block text-sm font-medium mb-2 dark:text-white">Nama</label>
                    <input type="text" name="name" id="name" placeholder="Nama anda" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" value="{{ old('name', $mahasiswa->name) }}" required>
                    @error('name') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- Program Studi (Dropdown) --}}
                <div>
                    <label for="prodi" class="block text-sm font-medium mb-2 dark:text-white">Program Studi</label>
                    <select id="prodi" name="prodi" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" required>
                        @foreach($prodiOptions as $prodi)
                            <option value="{{ $prodi }}" {{ old('prodi', $mahasiswa->prodi) == $prodi ? 'selected' : '' }}>{{ $prodi }}</option>
                        @endforeach
                    </select>
                    @error('prodi') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Angkatan --}}
                    <div>
                        <label for="angkatan" class="block text-sm font-medium mb-2 dark:text-white">Tahun Angkatan</label>
                        <input type="number" name="angkatan" id="angkatan" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" value="{{ old('angkatan', $mahasiswa->angkatan) }}" placeholder="Contoh: 2021" required>
                        @error('angkatan') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                    </div>
                    {{-- Detail Kelas --}}
                    <div>
                        <label for="kelas_detail" class="block text-sm font-medium mb-2 dark:text-white">Detail Kelas</label>
                        <div class="flex rounded-lg shadow-sm">
                            @php
                                $prodiAbbr = $prodiAbbreviations[$mahasiswa->prodi] ?? 'PRODI';
                                $kelasDetail = $mahasiswa->kelas ? str_replace($prodiAbbr . '-', '', $mahasiswa->kelas) : '';
                            @endphp
                            <div class="px-4 inline-flex items-center min-w-fit rounded-s-md border border-e-0 border-gray-200 bg-gray-50 dark:bg-neutral-700 dark:border-neutral-600">
                                <span id="prodi-prefix" class="text-sm text-gray-500 dark:text-neutral-400">{{ $prodiAbbr }}-</span>
                            </div>
                            <input type="text" name="kelas_detail" id="kelas_detail" class="py-3 px-4 block w-full border-gray-200 shadow-sm rounded-e-lg text-sm" value="{{ old('kelas_detail', $kelasDetail) }}" placeholder="Contoh: 4A" required>
                        </div>
                        @error('kelas_detail') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium mb-2 dark:text-white">Email</label>
                    
                    @if($mahasiswa->google_id)
                        <input id="email" name="email" type="email" 
                               class="py-3 px-4 block w-full border-gray-200 dark:border-neutral-700 rounded-lg text-sm bg-gray-100 dark:bg-neutral-700 dark:text-neutral-400" 
                               value="{{ $mahasiswa->email }}" 
                               readonly>
                        <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">Email tidak bisa diubah karena akun mahasiswa terhubung dengan akun Google.</p>
                    @else
                        @php
                            $emailParts = explode('@gmail.com', old('email', $mahasiswa->email));
                            $emailUsername = $emailParts[0];
                        @endphp
                        <div class="flex rounded-lg shadow-sm">
                            <input type="text" id="email_username" 
                                   class="py-3 px-4 block w-full border-gray-200 dark:border-neutral-700 rounded-tl-lg rounded-bl-lg text-sm" 
                                   value="{{ $emailUsername }}" 
                                   placeholder="nama.unik" required>
                            <div class="px-4 inline-flex items-center min-w-fit rounded-e-md border border-s-0 border-gray-200 bg-gray-50 dark:bg-neutral-700 dark:border-neutral-600">
                                <span class="text-sm text-gray-500 dark:text-neutral-400">@gmail.com</span>
                            </div>
                        </div>
                        <input type="hidden" name="email" id="full_email" value="{{ old('email', $mahasiswa->email) }}">
                    @endif

                    @error('email') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                </div>
                
                {{-- Password & Konfirmasi --}}
                @if(!$mahasiswa->google_id)
                    <div>
                        <label for="password" class="block text-sm font-medium mb-2 dark:text-white">Password Baru (Opsional)</label>
                        <input type="password" name="password" id="password" placeholder="masukan password" class="py-3 px-4 block w-full border-gray-200 dark:border-neutral-700 rounded-lg text-sm">
                        <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                        @error('password') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium mb-2 dark:text-white">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="masukan ulang password" class="py-3 px-4 block w-full border-gray-200 dark:border-neutral-700 rounded-lg text-sm">
                    </div>
                @endif
            </div>

            <div class="mt-8 flex justify-end gap-x-2">
                <a href="{{ route('admin.mahasiswa.index') }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">
                    Update
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
        if (prodiSelect && prodiPrefixSpan) {
            const selectedProdi = prodiSelect.value;
            const prefix = prodiAbbreviations[selectedProdi] || 'Pilih Prodi';
            prodiPrefixSpan.textContent = prefix + '-';
        }
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
    }
    if (prodiSelect) {
        prodiSelect.addEventListener('change', updatePrefix);
    }

    updatePrefix();
});
</script>
@endpush