<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SIPPAK')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-outfit text-gray-800 antialiased">
    <header class="sticky top-0 z-50 border-b border-gray-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('beranda') }}" class="flex min-w-0 items-center gap-3">
                <img src="{{ asset('logo-sumbawa.png') }}" alt="Logo Kabupaten Sumbawa" class="h-10 w-10 shrink-0 object-contain">
                <div class="min-w-0">
                    <span class="block truncate text-base font-bold text-gray-900">SIPPAK</span>
                    <span class="hidden truncate text-xs text-gray-500 sm:block">Kabupaten Sumbawa</span>
                </div>
            </a>

            <nav class="hidden items-center gap-6 text-sm font-medium text-gray-600 lg:flex">
                <a class="hover:text-brand-600" href="{{ route('beranda') }}">Beranda</a>
                <a class="hover:text-brand-600" href="{{ route('tentang') }}">Tentang</a>
                <a class="hover:text-brand-600" href="{{ route('beranda') }}#cara-melapor">Cara Melapor</a>
                <a class="hover:text-brand-600" href="{{ route('edukasi') }}">Edukasi</a>
                <a class="hover:text-brand-600" href="{{ route('statistik') }}">Statistik</a>
                <a class="hover:text-brand-600" href="{{ route('faq') }}">FAQ</a>
                <a class="hover:text-brand-600" href="{{ route('beranda') }}#kontak">Kontak</a>
            </nav>

            <div class="flex shrink-0 items-center gap-2">
                <a class="hidden rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 sm:inline-flex" href="{{ route('tracking.form') }}">Tracking</a>
                <a class="rounded-lg bg-brand-500 px-3 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 sm:px-4" href="{{ route('pengaduan.buat') }}">Buat Pengaduan</a>
            </div>
        </div>
    </header>

    <main>
        @if(session('success'))<div class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8"><div class="alert-success">{{ session('success') }}</div></div>@endif
        @if(session('warning'))<div class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8"><div class="mb-6 rounded-xl border border-warning-200 bg-warning-50 p-4 text-sm text-warning-700">{{ session('warning') }}</div></div>@endif
        @if($errors->any())<div class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8"><div class="alert-danger"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>@endif
        @yield('content')
    </main>
</body>
</html>
