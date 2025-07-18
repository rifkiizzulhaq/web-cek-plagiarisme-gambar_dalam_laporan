

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Riwayat Unggahan Saya</h1>

    <div class="border border-gray-200 rounded-lg dark:border-neutral-700">
        
        <div class="py-3 px-4">
            <form action="<?php echo e(route('mahasiswa.riwayat')); ?>" method="GET">
                <div class="relative max-w-xs">
                    <label for="search-input" class="sr-only">Search</label>
                    <input type="text" name="search" id="search-input" class="py-1.5 sm:py-2 px-3 ps-9 block w-full border-gray-200 shadow-sm rounded-lg sm:text-sm" placeholder="Cari nama file..." value="<?php echo e(request('search')); ?>">
                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                        <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                </div>
            </form>
        </div>

        
        <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gray-50 dark:bg-neutral-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">No.</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Nama File</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Tanggal Unggah</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Persentase</th>
                        <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                    <?php $__empty_1 = true; $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200"><?php echo e($files->firstItem() + $loop->index); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200"><?php echo e(Str::limit($file->name, 40)); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200"><?php echo e($file->created_at->format('d M Y')); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <?php if($file->status == 'completed'): ?>
                                    <span class="py-1 px-2.5 inline-flex text-base font-bold bg-teal-100 text-teal-800 rounded-full"><?php echo e($file->similarity_percentage ?? 0); ?>%</span>
                                <?php else: ?>
                                    <span class="py-1 px-2.5 inline-flex text-sm font-medium bg-yellow-100 text-yellow-800 rounded-full">Processing...</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                <a href="<?php echo e(route('mahasiswa.view.document', $file->id)); ?>" class="text-blue-600 hover:text-blue-800">Lihat Hasil</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Anda belum memiliki riwayat unggahan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if($files->hasPages()): ?>
            <div class="py-1 px-4 border-t dark:border-neutral-700">
                <?php echo e($files->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Rifki Izzulhaq\Documents\Skripsi\Project\Web\laravel\example-app\resources\views/User/riwayat/index.blade.php ENDPATH**/ ?>