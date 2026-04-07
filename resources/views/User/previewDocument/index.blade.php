@extends('layouts.main')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700 flex justify-between items-center">
            <div>
                <div class="mb-4">
                    <a href="{{ route('mahasiswa.riwayat') }}" class="inline-flex items-center px-4 py-2 bg-red-400 dark:bg-neutral-700 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-200 uppercase tracking-widest hover:bg-red-500 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
                
                <!-- Toggle untuk menampilkan highlighting -->
                <div class="mb-4 flex items-center space-x-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="highlight-toggle" class="form-checkbox h-5 w-5 text-yellow-500" checked>
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Tampilkan Highlighting Kuning & Sitasi [1]</span>
                    </label>
                    <button id="show-citations" class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
                        Lihat Daftar Sitasi
                    </button>
                </div>
                
                <div id="document-content" class="prose max-w-none dark:prose-invert">
                    <p>Memuat pratinjau dokumen...</p>
                </div>
                
                <!-- Loading indicator untuk plagiarism results -->
                <div id="plagiarism-loading" class="hidden mt-4">
                    <div class="flex items-center justify-center p-4">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
                        <span class="ml-2 text-sm text-gray-600">Memuat hasil deteksi plagiarisme...</span>
                    </div>
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
        .plagiarized-image-container { position: relative; display: inline-block; }
        .plagiarism-badge {
            position: absolute !important;
            top: 5px !important;
            right: 5px !important;
            width: 1.3rem !important;
            height: 1.3rem !important;
            font-size: 0.75rem !important;
            cursor: pointer !important;
            background-color: #ef4444 !important;
            color: white !important;
            border-radius: 9999px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            z-index: 10 !important;
            border: 2px solid white !important;
        }
        .plagiarism-badge:hover {
            background-color: #dc2626;
        }
        
        /* Styles untuk highlighting teks plagiarisme - EXACT format referensi */
        .highlight {
            background-color: #ffff99 !important;
            display: inline !important;
        }
        
        .highlight:hover {
            background-color: #ffeb3b !important;
        }
        
        /* Citation links - mengikuti style referensi */
        a[href='#'] {
            color: #3498db !important;
            text-decoration: none !important;
            font-weight: normal !important;
            margin-left: 2px !important;
            cursor: pointer !important;
        }
        
        a[href='#']:hover {
            color: #2980b9 !important;
            text-decoration: underline !important;
        }
        
        .similarity-tooltip {
            position: absolute;
            background-color: #1f2937;
            color: white;
            padding: 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            z-index: 1000;
            display: none;
            max-width: 200px;
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
                    // Load document content
                    fetch("{{ route('mahasiswa.get.file.content', ['file_id' => $file->id]) }}")
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

                                        // Load plagiarism results for text highlighting
                                        loadPlagiarismResults({{ $file->id }});
                                        
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
                                                    
                                                    // 1. Bungkus gambar dengan div container
                                                    const container = document.createElement('div');
                                                    container.className = 'plagiarized-image-container';
                                                    img.parentNode.insertBefore(container, img);
                                                    container.appendChild(img);

                                                    // 2. Buat lencana (badge) yang bisa diklik
                                                    const badge = document.createElement('div');
                                                    badge.className = 'plagiarism-badge';
                                                    badge.textContent = `[${reportItem.source_image_index + 1}]`;
                                                    badge.style.cursor = 'pointer'; 
                                                    
                                                    // 3. Tambahkan event klik untuk menampilkan SweetAlert
                                                    badge.addEventListener('click', function() {
                                                        const similarity = (reportItem.similarity * 100).toFixed(2);
                                                        Swal.fire({
                                                            title: 'Gambar Mirip Terdeteksi!',
                                                            html: `
                                                                <div class="text-start space-y-2">
                                                                    <p><strong>Dokumen Sumber:</strong><br>${reportItem.match_doc_title}</p>
                                                                    <p><strong>Tingkat Kemiripan:</strong><br>${similarity}%</p>
                                                                </div>
                                                            `,
                                                            icon: 'warning'
                                                        });
                                                    });

                                                    // 4. Masukkan lencana ke dalam container
                                                    container.appendChild(badge);

                                                    
                                                }
                                        });
                                    }, 100);
                                });
                            };
                        reader.readAsArrayBuffer(blob);
                    });
                }
            @endif
            
            // Global variables untuk plagiarism data
            let plagiarismData = null;
            let citationsMap = {};
            
            // Function untuk load plagiarism results
            function loadPlagiarismResults(fileId) {
                const loadingDiv = document.getElementById('plagiarism-loading');
                loadingDiv.classList.remove('hidden');
                
                fetch(`{{ url('/mahasiswa/cek-plagiarisme/results') }}/${fileId}`)
                    .then(response => response.json())
                    .then(data => {
                        loadingDiv.classList.add('hidden');
                        if (data.success) {
                            plagiarismData = data.data;
                            setupCitationsMap();
                            highlightPlagiarizedText();
                        } else {
                            console.error('Error loading plagiarism results:', data.message);
                        }
                    })
                    .catch(error => {
                        loadingDiv.classList.add('hidden');
                        console.error('Error:', error);
                    });
            }
            
            // Function untuk setup citations map
            function setupCitationsMap() {
                if (!plagiarismData || !plagiarismData.citations) return;
                
                plagiarismData.citations.forEach(citation => {
                    citationsMap[citation.citation_number] = citation;
                });
            }
            
            // Function untuk highlight plagiarized text
            function highlightPlagiarizedText() {
                if (!plagiarismData || !plagiarismData.sentences) {
                    console.log('No plagiarism data available for highlighting');
                    return;
                }
                
                const contentDiv = document.getElementById('document-content');
                let contentHTML = contentDiv.innerHTML;
                
                // Clear existing citations map
                citationsMap = {};
                
                // Build citations map first
                if (plagiarismData.citations) {
                    plagiarismData.citations.forEach(citation => {
                        citationsMap[citation.citation_number] = citation;
                    });
                }
                
                // Process each sentence for highlighting
                plagiarismData.sentences.forEach(sentence => {
                    if (sentence.is_plagiarized && sentence.citations && sentence.citations.length > 0) {
                        const originalText = sentence.text.trim();
                        
                        // Create citation links
                        const citationLinks = sentence.citations.map(citationNum => {
                            const citation = citationsMap[citationNum];
                            if (!citation) return '';
                            
                            const pageNum = citation.page || 'Unknown';
                            const similarity = ((citation.similarity_score || 0) * 100).toFixed(1);
                            const docTitle = citation.title || 'Unknown Document';
                            
                            return `<a href='#' onclick="showSourceDocument(${citationNum})" title="Sumber: ${docTitle} - Halaman: ${pageNum} - Kemiripan: ${similarity}%" style="color: #3498db; text-decoration: none; font-weight: normal; margin-left: 2px;">[${citationNum}]</a>`;
                        }).join('');
                        
                        // Create highlighted text with citations
                        const highlightedText = `<span class='highlight' style="background-color: #ffeb3b; padding: 2px 4px; border-radius: 3px;">${originalText}</span>${citationLinks}`;
                        
                        // Replace text in HTML - use more precise replacement
                        try {
                            // Simple text replacement for exact matches
                            const textToReplace = originalText;
                            if (contentHTML.includes(textToReplace)) {
                                contentHTML = contentHTML.replace(textToReplace, highlightedText);
                            }
                        } catch (error) {
                            console.error('Error highlighting sentence:', error);
                        }
                    }
                });
                
                contentDiv.innerHTML = contentHTML;
                console.log('Highlighting completed. Total citations:', Object.keys(citationsMap).length);
            }
            
            // Function untuk show source document
            function showSourceDocument(citationNumber) {
                const citation = citationsMap[citationNumber];
                if (!citation) {
                    Swal.fire('Error', 'Sitasi tidak ditemukan', 'error');
                    return;
                }
                
                Swal.fire({
                    title: 'Memuat Dokumen Sumber...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                fetch(`{{ url('/mahasiswa/cek-plagiarisme/source') }}/${citation.source_doc_id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSourceDocumentModal(data.data, citation);
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Gagal memuat dokumen sumber', 'error');
                    });
            }
            
            // Function untuk show source document modal
            function showSourceDocumentModal(sourceDoc, citation) {
                const similarity = (citation.similarity_score * 100).toFixed(1);
                const pageText = sourceDoc.pages.find(p => p.page === citation.page)?.text || 'Teks tidak ditemukan';
                
                Swal.fire({
                    title: `Dokumen Sumber [${citation.citation_number}]`,
                    html: `
                        <div class="text-left space-y-4">
                            <div class="bg-gray-50 p-3 rounded">
                                <strong>Judul:</strong> ${sourceDoc.title}<br>
                                <strong>Penulis:</strong> ${sourceDoc.author || 'Tidak diketahui'}<br>
                                <strong>Halaman:</strong> ${citation.page}<br>
                                <strong>Tingkat Kemiripan:</strong> <span class="text-red-600 font-bold">${similarity}%</span>
                            </div>
                            <div>
                                <strong>Konten Halaman ${citation.page}:</strong>
                                <div class="bg-white border rounded p-3 max-h-60 overflow-y-auto text-sm">
                                    ${pageText.substring(0, 500)}${pageText.length > 500 ? '...' : ''}
                                </div>
                            </div>
                        </div>
                    `,
                    width: '600px',
                    showCancelButton: true,
                    confirmButtonText: 'Buka di Tab Baru',
                    cancelButtonText: 'Tutup',
                    confirmButtonColor: '#3b82f6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Open source document in new tab (if you have a route for it)
                        const newWindow = window.open('', '_blank');
                        newWindow.document.write(`
                            <html>
                                <head>
                                    <title>${sourceDoc.title}</title>
                                    <style>
                                        body { font-family: Arial, sans-serif; padding: 20px; line-height: 1.6; }
                                        .header { border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-bottom: 20px; }
                                        .page { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; }
                                    </style>
                                </head>
                                <body>
                                    <div class="header">
                                        <h1>${sourceDoc.title}</h1>
                                        <p><strong>Penulis:</strong> ${sourceDoc.author || 'Tidak diketahui'}</p>
                                        <p><strong>Tanggal Upload:</strong> ${sourceDoc.uploaded_at ? new Date(sourceDoc.uploaded_at).toLocaleDateString('id-ID') : 'Tidak diketahui'}</p>
                                    </div>
                                    ${sourceDoc.pages.map(page => `
                                        <div class="page">
                                            <h3>Halaman ${page.page}</h3>
                                            <p>${page.text.replace(/\n/g, '<br>')}</p>
                                        </div>
                                    `).join('')}
                                </body>
                            </html>
                        `);
                    }
                });
            }
            
            // Event listeners
            document.getElementById('highlight-toggle').addEventListener('change', function() {
                const contentDiv = document.getElementById('document-content');
                if (this.checked) {
                    highlightPlagiarizedText();
                } else {
                    // Remove highlighting - EXACT format referensi
                    const highlightedElements = contentDiv.querySelectorAll('.highlight');
                    const citationLinks = contentDiv.querySelectorAll('a[href="#"]');
                    
                    highlightedElements.forEach(el => {
                        const parent = el.parentNode;
                        parent.replaceChild(document.createTextNode(el.textContent), el);
                        parent.normalize();
                    });
                    
                    citationLinks.forEach(link => {
                        link.remove();
                    });
                }
            });
            
            document.getElementById('show-citations').addEventListener('click', function() {
                if (!plagiarismData || !plagiarismData.citations) {
                    Swal.fire('Info', 'Belum ada data sitasi yang dimuat', 'info');
                    return;
                }
                
                const citationsList = plagiarismData.citations.map(citation => {
                    const similarity = (citation.similarity_score * 100).toFixed(1);
                    return `
                        <div class="border-b pb-2 mb-2">
                            <strong>[${citation.citation_number}]</strong> ${citation.title}<br>
                            <small class="text-gray-600">Halaman ${citation.page} - Kemiripan: ${similarity}%</small>
                        </div>
                    `;
                }).join('');
                
                Swal.fire({
                    title: 'Daftar Sitasi',
                    html: `<div class="text-left max-h-60 overflow-y-auto">${citationsList}</div>`,
                    width: '500px',
                    confirmButtonText: 'Tutup'
                });
            });
        });
    </script>
@endpush