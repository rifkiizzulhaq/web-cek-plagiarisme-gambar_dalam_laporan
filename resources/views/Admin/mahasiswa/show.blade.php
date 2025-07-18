@extends('layouts.main')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Riwayat Unggahan: {{ $mahasiswa->name }}</h1>
            <p class="text-sm text-gray-500">{{ $mahasiswa->email }}</p>
            @if ($mahasiswa->google_id)
                <span class="block bg-gray-300 w-14 px-2 text-xs rounded-full text-left text-blue-600 dark:text-blue-500">
                    Google
                </span>
            @endif
        </div>
        <a href="{{ route('admin.mahasiswa.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-neutral-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-neutral-600">
            Kembali
        </a>
    </div>

    {{-- Struktur Tabel Baru --}}
    <div class="border border-gray-200 rounded-lg dark:border-neutral-700">
        {{-- Header Tabel dengan Pencarian --}}
        <div class="py-3 px-4">
            <form action="{{ route('admin.mahasiswa.show', $mahasiswa->id) }}" method="GET">
                <div class="relative max-w-xs">
                    <label for="search-input" class="sr-only">Search</label>
                    <input type="text" name="search" id="search-input" class="py-1.5 sm:py-2 px-3 ps-9 block w-full border-gray-200 shadow-sm rounded-lg sm:text-sm" placeholder="Cari nama file..." value="{{ request('search') }}">
                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                        <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gray-50 dark:bg-neutral-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">No.</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Nama File</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Tanggal Unggah</th>
                        <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @forelse ($files as $file)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $files->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $file->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $file->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                @if($file->status == 'completed')
                                    <span class="py-1 px-2.5 inline-flex items-center gap-x-1 text-base font-bold bg-teal-100 text-teal-800 rounded-full dark:bg-teal-500/10 dark:text-teal-500">
                                        {{ $file->similarity_percentage ?? 0 }}%
                                    </span>
                                @else
                                    <span class="py-1 px-2.5 inline-flex items-center gap-x-1 text-sm font-medium bg-yellow-100 text-yellow-800 rounded-full dark:bg-yellow-500/10 dark:text-yellow-500">
                                        Processing...
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada riwayat unggahan untuk mahasiswa ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Paginasi --}}
        @if ($files->hasPages())
            <div class="py-1 px-4 border-t dark:border-neutral-700">
                {{ $files->links() }}
            </div>
        @endif
    </div>
</div>
@endsection