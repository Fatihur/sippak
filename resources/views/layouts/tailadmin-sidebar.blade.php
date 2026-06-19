<aside id="sidebar"
    class="fixed left-0 top-0 z-99999 flex h-screen flex-col border-r border-gray-200 bg-white px-5 text-gray-900 transition-all duration-300 ease-in-out dark:border-gray-800 dark:bg-gray-900"
    :class="{
        'w-[290px]': $store.sidebar.isExpanded || $store.sidebar.isMobileOpen || $store.sidebar.isHovered,
        'w-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
        'translate-x-0': $store.sidebar.isMobileOpen,
        '-translate-x-full xl:translate-x-0': !$store.sidebar.isMobileOpen
    }"
    @mouseenter="$store.sidebar.setHovered(true)"
    @mouseleave="$store.sidebar.setHovered(false)">
    <div class="flex py-8"
        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('logo-sumbawa.png') }}" alt="Logo Kabupaten Sumbawa" class="h-10 w-10 shrink-0 rounded-xl object-contain shadow-theme-xs">
            <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="min-w-0">
                <span class="block text-xl font-bold text-gray-900 dark:text-white">SILAPAK</span>
                <span class="block text-xs text-gray-500 dark:text-gray-400">Panel {{ auth()->user()->labelRole() }}</span>
            </span>
        </a>
    </div>

    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <nav class="mb-6">
            <div class="flex flex-col gap-4">
                <div>
                    <h2 class="mb-4 flex text-xs uppercase leading-[20px] text-gray-400"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'">
                        <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">Menu</span>
                        <i x-show="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen" class="fa-solid fa-ellipsis"></i>
                    </h2>
                    @php
                        $role = auth()->user()->role;
                        $dashboardLabel = 'Dashboard';
                        $laporanLabel = 'Laporan Pengaduan';
                        $rekapLabel = 'Rekap & Statistik';

                        if ($role === \App\Models\User::ROLE_KEPALA_BIDANG) {
                            $dashboardLabel = 'Dashboard Kabid';
                            $laporanLabel = 'Pengawasan Kasus';
                            $rekapLabel = 'Statistik & Tren';
                        } elseif ($role === \App\Models\User::ROLE_KEPALA_DINAS) {
                            $dashboardLabel = 'Dashboard Kadis';
                            $laporanLabel = 'Evaluasi Laporan';
                            $rekapLabel = 'Rekapitulasi & PDF';
                        } else {
                            $dashboardLabel = 'Dashboard Admin';
                            $laporanLabel = 'Kelola Laporan';
                            $rekapLabel = 'Rekap & Export';
                        }
                    @endphp
                    <ul class="flex flex-col gap-1">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="menu-item group {{ request()->routeIs('admin.dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}"
                                :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'">
                                <span class="{{ request()->routeIs('admin.dashboard') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"><i class="fa-solid fa-gauge-high text-xl"></i></span>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">{{ $dashboardLabel }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.laporan.index') }}" class="menu-item group {{ request()->routeIs('admin.laporan.*') ? 'menu-item-active' : 'menu-item-inactive' }}"
                                :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'">
                                <span class="{{ request()->routeIs('admin.laporan.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"><i class="fa-solid fa-folder-open text-xl"></i></span>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">{{ $laporanLabel }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.rekap.index') }}" class="menu-item group {{ request()->routeIs('admin.rekap.*') ? 'menu-item-active' : 'menu-item-inactive' }}"
                                :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'">
                                <span class="{{ request()->routeIs('admin.rekap.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"><i class="fa-solid fa-chart-column text-xl"></i></span>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">{{ $rekapLabel }}</span>
                            </a>
                        </li>
                        @if(auth()->user()->role === \App\Models\User::ROLE_OPERATOR)
                            <li>
                                <a href="{{ route('admin.pengguna.index') }}" class="menu-item group {{ request()->routeIs('admin.pengguna.*') ? 'menu-item-active' : 'menu-item-inactive' }}"
                                    :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'">
                                    <span class="{{ request()->routeIs('admin.pengguna.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"><i class="fa-solid fa-users-gear text-xl"></i></span>
                                    <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">Manajemen Pengguna</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.whatsapp.index') }}" class="menu-item group {{ request()->routeIs('admin.whatsapp.*') ? 'menu-item-active' : 'menu-item-inactive' }}"
                                    :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'">
                                    <span class="{{ request()->routeIs('admin.whatsapp.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"><i class="fa-brands fa-whatsapp text-xl"></i></span>
                                    <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">WhatsApp Gateway</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>

                <div>
                    <h2 class="mb-4 flex text-xs uppercase leading-[20px] text-gray-400"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'">
                        <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">Lainnya</span>
                        <i x-show="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen" class="fa-solid fa-ellipsis"></i>
                    </h2>
                    <ul class="flex flex-col gap-1">
                        <li>
                            <a href="{{ route('beranda') }}" class="menu-item group menu-item-inactive"
                                :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'">
                                <span class="menu-item-icon-inactive"><i class="fa-solid fa-arrow-up-right-from-square text-xl"></i></span>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">Halaman Publik</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</aside>
