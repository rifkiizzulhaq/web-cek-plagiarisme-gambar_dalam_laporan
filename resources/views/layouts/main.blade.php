<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('Image/logo_polindra.png') }}">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white dark:bg-slate-900">
        @include('layouts.sidebar')
        
        <div class="w-full lg:ps-64">
            @include('layouts.navigation') 

            <main class="p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
        @stack('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            @if(session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if(session('status') === 'profile-updated')
                Swal.fire({
                    title: 'Tersimpan!',
                    text: 'Informasi profil Anda berhasil diperbarui.',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
        </script>
    </body>
</html>
