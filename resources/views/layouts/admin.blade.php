<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin SIPPAK')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            if ((savedTheme || systemTheme) === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
</head>
<body class="relative bg-gray-50 font-outfit text-gray-800 dark:bg-gray-900 dark:text-gray-200"
    x-data="{ loaded: true }"
    x-init="
        $store.sidebar.isExpanded = localStorage.getItem('sippak-sidebar-expanded') !== 'false' && window.innerWidth >= 1280;
        const checkMobile = () => {
            if (window.innerWidth < 1280) {
                $store.sidebar.setMobileOpen(false);
                $store.sidebar.isExpanded = false;
            } else {
                $store.sidebar.isMobileOpen = false;
                $store.sidebar.isExpanded = localStorage.getItem('sippak-sidebar-expanded') !== 'false';
            }
        };
        window.addEventListener('resize', checkMobile);
    ">
    <div class="min-h-screen xl:flex">
        @include('layouts.tailadmin-backdrop')
        @include('layouts.tailadmin-sidebar')

        <div class="flex-1 transition-all duration-300 ease-in-out"
            :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'xl:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
                'ml-0': $store.sidebar.isMobileOpen
            }">
            @include('layouts.tailadmin-header')

            <main class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                @if(session('success'))<div class="mb-6 rounded-xl border border-success-200 bg-success-50 p-4 text-sm text-success-700 dark:border-success-800 dark:bg-success-900/20 dark:text-success-400">{{ session('success') }}</div>@endif
                @if(session('warning'))<div class="mb-6 rounded-xl border border-warning-200 bg-warning-50 p-4 text-sm text-warning-700 dark:border-warning-800 dark:bg-warning-900/20 dark:text-warning-400">{{ session('warning') }}</div>@endif
                @if(session('error'))<div class="mb-6 rounded-xl border border-error-200 bg-error-50 p-4 text-sm text-error-700 dark:border-error-800 dark:bg-error-900/20 dark:text-error-400">{{ session('error') }}</div>@endif
                @if($errors->any())<div class="mb-6 rounded-xl border border-error-200 bg-error-50 p-4 text-sm text-error-700 dark:border-error-800 dark:bg-error-900/20 dark:text-error-400"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
