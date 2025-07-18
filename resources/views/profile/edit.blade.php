@extends('layouts.main')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Profil Saya</h1>

    <div class="space-y-6">
        {{-- Form untuk Informasi Profil --}}
        <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-xl p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">
                Informasi Profil
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Perbarui informasi profil dan alamat email akun Anda.
            </p>

            <form id="data-form" method="post" action="{{ route('profile.update') }}" class="space-y-6">
                @csrf
                @method('patch')

                {{-- Field khusus untuk Mahasiswa --}}
                @if(Auth::user()->hasRole('mahasiswa'))
                    <div>
                        <label for="nim" class="block text-sm font-medium mb-2 dark:text-white">NIM</label>
                        {{-- Dibuat bisa diedit --}}
                        <input id="nim" name="nim" type="text" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" value="{{ old('nim', $user->nim) }}" placeholder="Contoh: 21030..." required>
                        @error('nim') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                    </div>
                @endif
                
                <div>
                    <label for="name" class="block text-sm font-medium mb-2 dark:text-white">Nama</label>
                    <input id="name" name="name" type="text" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap" required autofocus>
                    @error('name') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium mb-2 dark:text-white">Email</label>
                    
                    @if(Auth::user()->google_id)
                        <input id="email" name="email" type="email" 
                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm bg-gray-100 dark:bg-neutral-700" 
                            value="{{ $user->email }}" 
                            readonly>
                        <p class="text-xs text-gray-500 mt-1">Email tidak bisa diubah karena terhubung dengan akun Google.</p>  
                    @else
                        @php
                            $emailParts = explode('@', old('email', $user->email));
                            $emailUsername = $emailParts[0];
                        @endphp
                        <div class="flex rounded-lg shadow-sm">
                            <input type="text" id="email_username" 
                                class="py-3 px-4 block w-full border-gray-200 shadow-sm rounded-s-lg text-sm" 
                                value="{{ $emailUsername }}" 
                                placeholder="nama.unik" required>
                            <div class="px-4 inline-flex items-center min-w-fit rounded-e-md border border-s-0 border-gray-200 bg-gray-50 dark:bg-neutral-700 dark:border-neutral-600">
                                <span class="text-sm text-gray-500 dark:text-neutral-400">@gmail.com</span>
                            </div>
                        </div>
                        <input type="hidden" name="email" id="full_email" value="{{ old('email', $user->email) }}">
                    @endif

                    @error('email') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                </div>
                
                @if(Auth::user()->hasRole('mahasiswa'))
                    <div>
                        <label for="prodi" class="block text-sm font-medium mb-2 dark:text-white">Program Studi</label>
                        {{-- Diubah menjadi dropdown --}}
                        <select id="prodi" name="prodi" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" required>
                            <option value="">Pilih Program Studi</option>
                            @foreach($prodiOptions as $prodi)
                                <option value="{{ $prodi }}" {{ old('prodi', $user->prodi) == $prodi ? 'selected' : '' }}>{{ $prodi }}</option>
                            @endforeach
                        </select>
                        @error('prodi') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="angkatan" class="block text-sm font-medium mb-2 dark:text-white">Tahun Angkatan</label>
                            <input id="angkatan" name="angkatan" type="number" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm" value="{{ old('angkatan', $user->angkatan) }}" placeholder="Contoh: 2021" required>
                            @error('angkatan') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            @php
                                $prodiAbbr = $prodiAbbreviations[$user->prodi] ?? 'PRODI';
                                $kelasDetail = $user->kelas ? str_replace($prodiAbbr . '-', '', $user->kelas) : '';
                            @endphp
                            <label for="kelas_detail" class="block text-sm font-medium mb-2 dark:text-white">Detail Kelas</label>
                            <div class="flex rounded-lg shadow-sm">
                                <div class="px-4 inline-flex items-center min-w-fit rounded-s-md border border-e-0 border-gray-200 bg-gray-50 dark:bg-neutral-700 dark:border-neutral-600">
                                    <span id="prodi-prefix" class="text-sm text-gray-500 dark:text-neutral-400">{{ $prodiAbbr }}-</span>
                                </div>
                                <input type="text" name="kelas_detail" id="kelas_detail" class="py-3 px-4 block w-full border-gray-200 shadow-sm rounded-e-lg text-sm" value="{{ old('kelas_detail', $kelasDetail) }}" placeholder="Contoh: 4A" required>
                            </div>
                            @error('kelas_detail') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                        </div>
                    </div>
                @endif

                <div class="mt-6 flex justify-between items-center">
                    <div class="flex gap-x-2">
                        <a href="{{ Auth::user()->hasRole('admin') ? route('admin.admin-halaman-utama') : route('mahasiswa.cek-plagiarisme') }}" 
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                            Batal
                        </a>
                        <button type="submit" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Form untuk Update Password --}}
        @if(!Auth::user()->google_id)
            <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-xl p-6">
                @include('profile.partials.update-password-form')
            </div>
        @endif

        {{-- Form untuk Hapus Akun --}}
        <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-xl p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('data-form');
    if (!form) return;

    let isFormDirty = false;
    form.addEventListener('input', () => { isFormDirty = true; });
    form.addEventListener('submit', () => { isFormDirty = false; });
    window.addEventListener('beforeunload', (e) => {
        if (isFormDirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    @if(Auth::user()->hasRole('mahasiswa'))
        const prodiSelect = document.getElementById('prodi');
        const prodiPrefixSpan = document.getElementById('prodi-prefix');
        const prodiAbbreviations = @json($prodiAbbreviations ?? []);

        function updatePrefix() {
            if (prodiSelect && prodiPrefixSpan) {
                const selectedProdi = prodiSelect.value;
                const prefix = prodiAbbreviations[selectedProdi] || 'Pilih Prodi';
                prodiPrefixSpan.textContent = prefix + '-';
            }
        }

        if (prodiSelect) {
            prodiSelect.addEventListener('change', updatePrefix);
            updatePrefix();
        }

        const emailUsernameInput = document.getElementById('email_username');
        const fullEmailInput = document.getElementById('full_email');
        
        function updateFullEmail() {
            if (emailUsernameInput && fullEmailInput) {
                let username = emailUsernameInput.value;
                if (username.includes('@')) {
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
    @endif
});
</script>
@endpush