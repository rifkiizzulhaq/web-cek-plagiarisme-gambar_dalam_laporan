<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('Image/logo_polindra.png') }}">
    
    <title>404 - Halaman Tidak Ditemukan | {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white dark:bg-gray-900">
    <div class="min-h-screen flex flex-col">
        <!-- ========== HEADER ========== -->
        <header class="flex justify-center z-50 w-full py-4">
            <nav class="px-4 sm:px-6 lg:px-8">
                <div class="flex items-center">
                    <img src="{{ asset('Image/logo_polindra.png') }}" alt="Logo" class="h-8 w-8 mr-2">
                    <span class="flex-none text-xl font-semibold sm:text-3xl dark:text-white">Polindra ImagePlag</span>
                </div>
            </nav>
        </header>
        <!-- ========== END HEADER ========== -->

        <!-- ========== MAIN CONTENT ========== -->
        <main class="flex-grow flex items-center justify-center">
            <div class="text-center px-4 sm:px-6 lg:px-8">
                <h1 class="block text-7xl font-bold text-gray-800 sm:text-9xl dark:text-white">404</h1>
                <p class="mt-3 text-xl text-gray-600 dark:text-neutral-400">Oops, halaman tidak ditemukan.</p>
                <p class="text-gray-600 dark:text-neutral-400">Maaf, halaman yang Anda cari tidak tersedia.</p>
                <div class="mt-5 flex flex-col justify-center items-center gap-2 sm:flex-row sm:gap-3">
                    @auth
                        @if(Auth::user()->role->name === 'admin')
                            <a class="w-full sm:w-auto py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" 
                               href="{{ route('admin-halaman-utama') }}">
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                Kembali ke Beranda Admin
                            </a>
                        @else
                            <a class="w-full sm:w-auto py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" 
                               href="{{ route('halaman-utama') }}">
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                Kembali ke Beranda
                            </a>
                        @endif
                    @else
                        <a class="w-full sm:w-auto py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" 
                           href="{{ route('login') }}">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                            Kembali ke Login
                        </a>
                    @endauth
                </div>
            </div>
        </main>
        <!-- ========== END MAIN CONTENT ========== -->

        <!-- ========== FOOTER ========== -->
        <footer class="text-center py-5">
            <div class="px-4 sm:px-6 lg:px-8">
                <p class="text-sm text-gray-500 dark:text-neutral-500">© {{ date('Y') }} {{ config('app.name') }}. All Rights Reserved.</p>
            </div>
        </footer>
        <!-- ========== END FOOTER ========== -->
    </div>
</body>
</html> 