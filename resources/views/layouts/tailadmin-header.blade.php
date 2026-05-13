<header class="sticky top-0 z-99999 flex w-full border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 xl:border-b">
    <div class="flex grow flex-col items-center justify-between xl:flex-row xl:px-6">
        <div class="flex w-full items-center justify-between gap-2 border-b border-gray-200 px-3 py-3 dark:border-gray-800 sm:gap-4 xl:justify-normal xl:border-b-0 xl:px-0 lg:py-4">
            <button
                class="hidden h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] lg:h-11 lg:w-11 xl:flex"
                :class="{ 'bg-gray-100 dark:bg-white/[0.03]': !$store.sidebar.isExpanded }"
                @click="$store.sidebar.toggleExpanded()" aria-label="Toggle Sidebar">
                <i class="fa-solid fa-bars-staggered"></i>
            </button>

            <button
                class="flex h-10 w-10 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/[0.03] lg:h-11 lg:w-11 xl:hidden"
                :class="{ 'bg-gray-100 dark:bg-white/[0.03]': $store.sidebar.isMobileOpen }"
                @click="$store.sidebar.toggleMobileOpen()" aria-label="Toggle Mobile Menu">
                <i class="fa-solid" :class="$store.sidebar.isMobileOpen ? 'fa-xmark' : 'fa-bars'"></i>
            </button>

            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 xl:hidden">
                <img src="{{ asset('logo-sumbawa.png') }}" alt="Logo Kabupaten Sumbawa" class="h-9 w-9 rounded-xl object-contain">
                <span class="font-bold text-gray-900 dark:text-white">SIPPAK</span>
            </a>

            <div class="hidden xl:block">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sistem Informasi Pengaduan Perlindungan Anak dan Perempuan</p>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">@yield('title', 'Dashboard')</h1>
                </div>
            </div>
        </div>

        <div class="flex w-full items-center justify-between gap-4 px-5 py-4 shadow-theme-md xl:w-auto xl:justify-end xl:px-0 xl:shadow-none">
            <button
                class="relative flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                @click="$store.theme.toggle()">
                <i class="fa-solid fa-moon hidden dark:block"></i>
                <i class="fa-solid fa-sun dark:hidden"></i>
            </button>

            <div class="flex items-center gap-3">
                <div class="hidden text-right sm:block">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ auth()->user()->name }}</p>
                    <p class="text-xs capitalize text-gray-500 dark:text-gray-400">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                </div>
                <div class="grid h-11 w-11 place-items-center rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                    <i class="fa-solid fa-user"></i>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="inline-flex items-center gap-2 rounded-lg bg-error-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-error-600">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="hidden sm:inline">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
