

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Riwayat Unggahan: <?php echo e($mahasiswa->name); ?></h1>
            <p class="text-sm text-gray-500"><?php echo e($mahasiswa->email); ?></p>
        </div>
        <a href="<?php echo e(route('admin.mahasiswa.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-neutral-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-neutral-600">
            Kembali
        </a>
    </div>

    <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-xl overflow-hidden">
        <div class="p-4 sm:p-7">
            <?php if($files->isNotEmpty()): ?>
                <div class="space-y-4">
                    <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex justify-between items-center p-4 border rounded-lg dark:border-neutral-700">
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-white"><?php echo e($file->name); ?></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Diupload pada: <?php echo e($file->created_at->format('d M Y')); ?>

                                </p>
                            </div>
                            <div>
                                <a href="<?php echo e(route('mahasiswa.view.document', $file->id)); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                    Lihat Hasil
                                </a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="mt-6">
                    <?php echo e($files->links()); ?>

                </div>
            <?php else: ?>
                <p class="text-center text-gray-500 dark:text-gray-400">Mahasiswa ini belum pernah mengunggah dokumen.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Rifki Izzulhaq\Documents\Skripsi\Project\Web\laravel\example-app\resources\views/Admin/mahasiswa/show.blade.php ENDPATH**/ ?>