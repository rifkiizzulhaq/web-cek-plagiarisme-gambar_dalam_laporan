<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-xl overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                Hasil Pengecekan Plagiarisme
            </h1>
            <?php if($file): ?>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                <?php echo e($file->name); ?>

            </p>
            <?php endif; ?>
        </div>

        <?php if(isset($error)): ?>
            <!-- Error Message -->
            <div class="p-6">
                <div class="bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900/10 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="size-4 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" x2="12" y1="8" y2="12"/>
                                <line x1="12" x2="12.01" y1="16" y2="16"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700 dark:text-red-400">
                                <?php echo e($error); ?>

                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif($file && isset($statistics)): ?>
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6">
                <!-- Total Sentences -->
                <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/10 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-400">Total Kalimat</h3>
                    <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-500">
                        <?php echo e($statistics['total_sentences']); ?>

                    </p>
                </div>

                <!-- Plagiarized Sentences -->
                <div class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-900/10 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-400">Kalimat Terindikasi</h3>
                    <p class="mt-2 text-3xl font-bold text-yellow-600 dark:text-yellow-500">
                        <?php echo e($statistics['plagiarized_sentences']); ?>

                    </p>
                </div>

                <!-- Similarity Percentage -->
                <div class="bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-900/10 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-green-800 dark:text-green-400">Persentase Kemiripan</h3>
                    <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-500">
                        <?php echo e($statistics['similarity_percentage']); ?>%
                    </p>
                </div>
            </div>

            <!-- Detailed Report -->
            <?php if(isset($report_content)): ?>
            <div class="p-6 border-t border-gray-200 dark:border-neutral-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                    Laporan Detail
                </h2>
                <div class="bg-gray-50 dark:bg-neutral-900 rounded-lg p-4 overflow-auto max-h-[600px]">
                    <?php echo $report_content; ?>

                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Actions -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-neutral-900 border-t border-gray-200 dark:border-neutral-700">
            <div class="flex justify-end space-x-3">
                <?php if($file && !isset($error)): ?>
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-neutral-700 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="size-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak Laporan
                </button>
                <?php endif; ?>
                <a href="<?php echo e(route('cek-plagiarisme')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cek Dokumen Lain
                </a>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
    @media print {
        .no-print {
            display: none;
        }
        body {
            background: white;
        }
        .max-h-[600px] {
            max-height: none !important;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Rifki Izzulhaq\Documents\Skripsi\Project\Web\laravel\example-app\resources\views/ViewDocument.blade.php ENDPATH**/ ?>