<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" type="image/png" href="<?php echo e(asset('Image/logo_polindra.png')); ?>">
    
    <title>419 - Page Expired | <?php echo e(config('app.name')); ?></title>
    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="bg-white dark:bg-gray-900">
    <div class="min-h-screen flex flex-col">
        <header class="flex justify-center z-50 w-full py-4">
            <nav class="px-4 sm:px-6 lg:px-8">
                <div class="flex items-center">
                    <img src="<?php echo e(asset('Image/logo_polindra.png')); ?>" alt="Logo" class="h-8 w-8 mr-2">
                    <span class="flex-none text-xl font-semibold sm:text-3xl dark:text-white">Polindra ImagePlag</span>
                </div>
            </nav>
        </header>

        <main class="flex-grow flex items-center justify-center">
            <div class="text-center px-4 sm:px-6 lg:px-8">
                <h1 class="block text-7xl font-bold text-gray-800 sm:text-9xl dark:text-white">419</h1>
                <p class="text-gray-600 dark:text-neutral-400">Maaf, Page Expired.</p>
            </div>
        </main>

        <footer class="text-center py-5">
            <div class="px-4 sm:px-6 lg:px-8">
                <p class="text-sm text-gray-500 dark:text-neutral-500">© <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. All Rights Reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html><?php /**PATH C:\Users\Rifki Izzulhaq\Documents\Skripsi\Project\Web\laravel\example-app\resources\views/errors/419.blade.php ENDPATH**/ ?>