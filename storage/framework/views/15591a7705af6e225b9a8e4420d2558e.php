<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="<?php echo e(asset('Image/logo_polindra.png')); ?>">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

        <title>
            <?php if(Request::routeIs('login')): ?>
                Login - Polindra ImagePlag
            <?php elseif(Request::routeIs('register')): ?>
                Register - Polindra ImagePlag
            <?php elseif(Request::routeIs('admin.login')): ?>
                Admin Login - Polindra ImagePlag
            <?php elseif(Request::routeIs('password.request')): ?>
                Reset Password - Polindra ImagePlag
            <?php else: ?>
                <?php echo e(config('app.name', 'Polindra ImagePlag')); ?>

            <?php endif; ?>
        </title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex sm:justify-center items-center pt-6 sm:pt-0 dark:bg-gray-900">
            <!-- Left side - Login Form -->
            <div class="flex-1 flex items-center justify-center p-8">
                <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                    <?php echo e($slot); ?>

                </div>
            </div>

            <!-- Right side - Image/Pattern -->
            <div class="hidden lg:block lg:w-1/2">
                <div class="h-screen relative">
                    <img src="<?php echo e(asset('Image/polindra.jpeg')); ?>" 
                         alt="Polindra" 
                         class="absolute inset-0 w-full h-full object-cover"
                    >
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </body>
</html>
<?php /**PATH C:\Users\Rifki Izzulhaq\Documents\Skripsi\Project\Web\laravel\example-app\resources\views/layouts/guest.blade.php ENDPATH**/ ?>