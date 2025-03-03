<!-- Sidebar -->
<div id="hs-application-sidebar" class="hs-overlay  [--auto-close:lg]
  hs-overlay-open:translate-x-0
  -translate-x-full transition-all duration-300 transform
  w-[260px] h-full
  hidden
  fixed inset-y-0 start-0 z-[60]
  bg-white border-e border-gray-200
  lg:block lg:translate-x-0 lg:end-auto lg:bottom-0
  dark:bg-neutral-800 dark:border-neutral-700" role="dialog" tabindex="-1" aria-label="Sidebar">
  <div class="relative flex flex-col h-full max-h-full">
    <div class="px-6 pt-4 flex items-center">
      <!-- Logo -->
      <a class="flex-none rounded-xl text-xl inline-block font-semibold focus:outline-none focus:opacity-80" href="#" aria-label="Preline">
        <img src="{{ asset('Image/logo_polindra.png') }}" alt="Logo" class="w-10 h-auto">
      </a>
      <!-- End Logo -->
       <div class="flex flex-col ml-2">
        <h1 class="text-xl font-semibold">Polindra</h1>
        <h1 class="text-[12px]">ImagePlag</h1>
       </div>
      <div class="hidden lg:block ms-2">

      </div>
    </div>

    <!-- Content -->
    <div class="h-full overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
      <nav class="hs-accordion-group p-3 w-full flex flex-col flex-wrap" data-hs-accordion-always-open>
        <ul class="flex flex-col space-y-1">
          <li>
            <a class="flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-gray-800 rounded-lg hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:bg-neutral-700 dark:text-white" href="#">
              <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
              Dashboard
            </a>
          </li>

          <li class="hs-accordion {{ Request::is('cek-plagiarisme') ? 'active' : '' }}" id="projects-accordion">
            <button type="button" class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-gray-800 rounded-lg dark:bg-neutral-700 focus:outline-none focus:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-200" data-hs-accordion-active-classes="bg-gray-100 dark:bg-neutral-700">
              <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <line x1="10" y1="9" x2="8" y2="9"/>
              </svg>
              Dokumen
              
              <svg class="hs-accordion-active:block ms-auto hidden size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>
              
              <svg class="hs-accordion-active:hidden ms-auto block size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </button>

            <div id="projects-accordion-child" class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ Request::is('cek-plagiarisme') ? '' : 'hidden' }}">
              <ul class="ps-8 pt-1 space-y-1">
                <li>
                  <a class="flex items-center gap-x-2 py-2 px-2.5 text-sm text-gray-800 rounded-lg {{ Request::is('cek-plagiarisme') ? 'bg-gray-200 dark:bg-neutral-700' : 'hover:bg-gray-100' }}" href="{{ route('cek-plagiarisme') }}">
                    Cek Plagiarisme
                  </a>
                </li>
                @if(Auth::user()->role->name === 'admin')
                <!-- Menu khusus admin -->
                <li>
                  <a class="flex items-center gap-x-2 py-2 px-2.5 text-sm text-gray-800 rounded-lg hover:bg-gray-100" href="#">
                    Menu Khusus Admin
                  </a>
                </li>
                @endif
              </ul>
            </div>
          </li>
        </ul>
      </nav>
    </div>

    <!-- Profile Section di bawah -->
    <div class="hs-dropdown relative inline-flex mt-auto [--placement:top-right]">
      <button id="hs-dropdown-account" type="button" class="w-full flex items-center gap-x-3.5 py-2 px-3 text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300" aria-expanded="false" aria-haspopup="menu" aria-label="Dropdown">
        <!-- Profile Icon -->
        <div class="flex justify-between w-full items-center">
          <div class="flex items-center gap-x-3">
            <div class="relative inline-block">
              @if(Auth::user()->google_id)
                <!-- Avatar Google -->
                <img class="size-9 rounded-full ring-2 ring-gray-100 dark:ring-gray-700" src="{{ Auth::user()->avatar }}" alt="Profile Picture">
              @else
                <!-- Default Profile Icon -->
                <div class="flex items-center justify-center size-9 rounded-full bg-gray-100 ring-2 ring-gray-100 dark:ring-gray-700 dark:bg-gray-700">
                  <svg class="size-4 text-gray-600 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                  </svg>
                </div>
              @endif
            </div>
            <div class="flex flex-col">
              <span class="text-sm font-semibold text-gray-800 dark:text-white leading-tight truncate max-w-[150px]">{{ Auth::user()->name }}</span>
              <span class="text-[11px] font-medium text-gray-600 dark:text-gray-400 leading-tight truncate max-w-[150px]">{{ Auth::user()->email }}</span>
            </div>
          </div>
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
        </div>
      </button>

      <div class="hs-dropdown-menu w-full transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-60 bg-white shadow-md rounded-lg p-2 dark:bg-neutral-800 dark:border dark:border-neutral-700 dark:divide-neutral-700" role="menu" aria-labelledby="hs-dropdown-account">
        <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300" href="{{ route('profile.edit') }}">
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          Profile
        </a>

        <form method="POST" action="{{ Auth::user()->role->name === 'admin' ? route('admin.logout') : route('logout') }}">
          @csrf
          <button type="submit" class="w-full flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300">
            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            Logout
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- End Sidebar -->