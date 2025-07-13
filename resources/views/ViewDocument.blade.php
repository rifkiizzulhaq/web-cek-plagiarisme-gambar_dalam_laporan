@extends('layouts.main')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-xl overflow-hidden">
    {{-- Header yang Sudah Diperbaiki --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700 flex justify-between items-center">
            <div>
                <div class="mb-4">
                    <a href="{{ route('cek-plagiarisme') }}" class="inline-flex items-center px-4 py-2 bg-red-400 dark:bg-neutral-700 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-200 uppercase tracking-widest hover:bg-red-500 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Kembali
                    </a>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Hasil Pengecekan Plagiarisme
                </h1>
                @if(isset($file))
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $file->name }}
                </p>
                @endif
            </div>
        </div>
        
        @if(isset($error))
            <div class="p-6">
                <div class="bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900/10 rounded-lg p-4">
                    <p class="text-sm text-red-700 dark:text-red-400">{{ $error }}</p>
                </div>
            </div>
        @elseif(isset($file))
            <div id="stats-cards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 p-6 hidden">
                {{-- Kartu Total Kalimat --}}
                <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/10 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-400">Total Kalimat</h3>
                    <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-500">{{ $file->total_sentences ?? 0 }}</p>
                </div>
                {{-- KARTU BARU: Total Gambar --}}
                <div class="bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/10 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-purple-800 dark:text-purple-400">Total Gambar</h3>
                    <p class="mt-2 text-3xl font-bold text-purple-600 dark:text-purple-500">{{ $file->total_images ?? 0 }}</p>
                </div>
                {{-- Kartu Kalimat Terindikasi --}}
                <div class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-900/10 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-400">Kalimat Terindikasi</h3>
                    <p class="mt-2 text-3xl font-bold text-yellow-600 dark:text-yellow-500">{{ $file->plagiarized_sentences ?? 0 }}</p>
                </div>
                {{-- KARTU BARU: Gambar Terindikasi --}}
                <div class="bg-pink-50 dark:bg-pink-900/10 border border-pink-200 dark:border-pink-900/10 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-pink-800 dark:text-pink-400">Gambar Terindikasi</h3>
                    <p class="mt-2 text-3xl font-bold text-pink-600 dark:text-pink-500">{{ $file->indicated_images ?? 0 }}</p>
                </div>
                {{-- Kartu Persentase Kemiripan --}}
                <div class="bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-900/10 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-green-800 dark:text-green-400">Persentase Kemiripan</h3>
                    <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-500">{{ $file->similarity_percentage ?? 0 }}%</p>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 dark:border-neutral-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                    Pratinjau Dokumen
                </h2>
                <div id="document-content" class="prose dark:prose-invert max-w-none bg-gray-50 dark:bg-neutral-900 rounded-lg p-4 overflow-auto max-h-[90vh] border dark:border-neutral-700">
                    <p>Memuat pratinjau dokumen...</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
    <style>
        .prose { line-height: 1.7; }
        .plagiarized-image {
            border: 4px solid #facc15 !important;
            box-shadow: 0 0 15px rgba(250, 204, 21, 0.5);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contentDiv = document.getElementById('document-content');
            
            @if(isset($file))
                const imagePlagiarismReport = @json($image_plagiarism_report ?? []);
                
                if (contentDiv) {
                    fetch("{{ route('get.file.content', ['file_id' => $file->id]) }}")
                        .then(response => {
                            if (!response.ok) throw new Error('Gagal memuat file dari server.');
                            return response.blob();
                        })
                        .then(blob => {
                            const reader = new FileReader();
                            reader.onload = function(loadEvent) {
                                mammoth.convertToHtml({ arrayBuffer: loadEvent.target.result })
                                    .then(result => {
                                        contentDiv.innerHTML = result.value;
                                        
                                        const statsCards = document.getElementById('stats-cards');
                                        if (statsCards) {
                                            statsCards.classList.remove('hidden');
                                        }

                                        setTimeout(() => {
                                            const imagesInDom = contentDiv.querySelectorAll('img');
                                            imagesInDom.forEach((img, index) => {
                                                const reportItem = imagePlagiarismReport.find(item => item.source_image_index === index);

                                                img.style.height = '400px';
                                                img.style.width = '100%';
                                                img.style.objectFit = 'contain';
                                                img.style.backgroundColor = '#f8f9fa';
                                                img.style.border = '1px solid #dee2e6';
                                                img.style.padding = '0.5rem';
                                                img.style.marginBottom = '1.5em';

                                                if (reportItem) {
                                                    img.style.border = '4px solid #facc15';
                                                    img.style.boxShadow = '0 0 15px rgba(250, 204, 21, 0.5)';
                                                }
                                        });
                                    }, 100);
                                });
                            };
                        reader.readAsArrayBuffer(blob);
                    });
                }
            @endif
        });
    </script>
@endpush