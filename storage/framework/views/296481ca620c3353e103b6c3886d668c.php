<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'Laravel')); ?></title>
        <link rel="icon" type="image/png" href="<?php echo e(asset('Image/logo_polindra.png')); ?>">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="bg-white dark:bg-slate-900">
        <div class="flex">
            <?php echo $__env->make('layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            
            <!-- Content -->
            <div class="w-full lg:pl-64">
                <main class="px-4 py-2 sm:p-6 lg:p-8">
                    <?php echo $__env->yieldContent('content'); ?>
                </main>
            </div>
        </div>
        <?php echo $__env->yieldPushContent('scripts'); ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        
        <script>
            <?php if(session('success')): ?>
                Swal.fire({
                    title: 'Berhasil!',
                    text: '<?php echo e(session('success')); ?>',
                    icon: 'success',
                    timer: 3000, // Notifikasi akan hilang setelah 3 detik
                    showConfirmButton: false
                });
            <?php endif; ?>

            // Notifikasi khusus untuk update profil
            <?php if(session('status') === 'profile-updated'): ?>
                Swal.fire({
                    title: 'Tersimpan!',
                    text: 'Informasi profil Anda berhasil diperbarui.',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            <?php endif; ?>
        </script>
    </body>
</html>
<?php /**PATH C:\Users\Rifki Izzulhaq\Documents\Skripsi\Project\Web\laravel\example-app\resources\views/layouts/main.blade.php ENDPATH**/ ?>