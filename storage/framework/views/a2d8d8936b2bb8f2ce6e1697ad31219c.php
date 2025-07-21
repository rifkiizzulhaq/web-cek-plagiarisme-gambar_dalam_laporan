<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700 flex justify-between items-center">
            <div>
                <div class="mb-4">
                    <a href="<?php echo e(route('mahasiswa.riwayat')); ?>" class="inline-flex items-center px-4 py-2 bg-red-400 dark:bg-neutral-700 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-200 uppercase tracking-widest hover:bg-red-500 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Kembali
                    </a>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Hasil Pengecekan Plagiarisme
                </h1>
                <?php if(isset($file)): ?>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    <?php echo e($file->name); ?>

                </p>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="p-6">
                <div class="bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900/10 rounded-lg p-4">
                    <p class="text-sm text-red-700 dark:text-red-400"><?php echo e($error); ?></p>
                </div>
            </div>
        <?php elseif(isset($file)): ?>
            <div id="stats-cards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 p-6 hidden">
                
                <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/10 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-400">Total Kalimat</h3>
                    <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-500"><?php echo e($file->total_sentences ?? 0); ?></p>
                </div>
                
                <div class="bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/10 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-purple-800 dark:text-purple-400">Total Gambar</h3>
                    <p class="mt-2 text-3xl font-bold text-purple-600 dark:text-purple-500"><?php echo e($file->total_images ?? 0); ?></p>
                </div>
                
                <div class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-900/10 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-400">Kalimat Terindikasi</h3>
                    <p class="mt-2 text-3xl font-bold text-yellow-600 dark:text-yellow-500"><?php echo e($file->plagiarized_sentences ?? 0); ?></p>
                </div>
                
                <div class="bg-pink-50 dark:bg-pink-900/10 border border-pink-200 dark:border-pink-900/10 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-pink-800 dark:text-pink-400">Gambar Terindikasi</h3>
                    <p class="mt-2 text-3xl font-bold text-pink-600 dark:text-pink-500"><?php echo e($file->indicated_images ?? 0); ?></p>
                </div>
                
                <div class="bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-900/10 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-green-800 dark:text-green-400">Persentase Kemiripan</h3>
                    <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-500"><?php echo e($file->similarity_percentage ?? 0); ?>%</p>
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
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
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
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contentDiv = document.getElementById('document-content');
            
            <?php if(isset($file)): ?>
                const imagePlagiarismReport = <?php echo json_encode($image_plagiarism_report ?? [], 15, 512) ?>;
                
                if (contentDiv) {
                    fetch("<?php echo e(route('mahasiswa.get.file.content', ['file_id' => $file->id])); ?>")
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
            <?php endif; ?>
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Rifki Izzulhaq\Documents\Skripsi\Project\Web\laravel\example-app\resources\views/user/previewDocument/index.blade.php ENDPATH**/ ?>