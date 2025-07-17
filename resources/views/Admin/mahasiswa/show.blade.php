@extends('layouts.main')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Riwayat Unggahan: {{ $mahasiswa->name }}</h1>
            <p class="text-sm text-gray-500">{{ $mahasiswa->email }}</p>
        </div>
        <a href="{{ route('admin.mahasiswa.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-neutral-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-neutral-600">
            Kembali
        </a>
    </div>

    <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-xl overflow-hidden">
        <div class="p-4 sm:p-7">
            @if($files->isNotEmpty())
                <div class="space-y-4">
                    @foreach($files as $file)
                        <div class="flex justify-between items-center p-4 border rounded-lg dark:border-neutral-700">
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ $file->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Diupload pada: {{ $file->created_at->format('d M Y') }}
                                </p>
                            </div>
                            <div>
                                <a href="{{ route('mahasiswa.view.document', $file->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                    Lihat Hasil
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $files->links() }}
                </div>
            @else
                <p class="text-center text-gray-500 dark:text-gray-400">Mahasiswa ini belum pernah mengunggah dokumen.</p>
            @endif
        </div>
    </div>
</div>
@endsection