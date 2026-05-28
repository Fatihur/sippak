<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SILAPAK')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#fffaf3] font-outfit text-slate-800 antialiased">
    <header class="sticky top-0 z-50 border-b border-orange-100/80 bg-white/90 shadow-sm backdrop-blur-xl">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('beranda') }}" class="flex min-w-0 items-center gap-3">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white shadow-theme-sm ring-1 ring-orange-100">
                    <img src="{{ asset('logo-sumbawa.png') }}" alt="Logo Kabupaten Sumbawa" class="h-10 w-10 object-contain">
                </span>
                <div class="min-w-0 leading-tight">
                    <span class="block truncate text-xl font-black tracking-tight text-slate-950">SILAPAK</span>
                    <span class="hidden truncate text-xs font-semibold uppercase tracking-[0.18em] text-orange-500 sm:block">Kabupaten Sumbawa</span>
                </div>
            </a>

            <nav class="hidden items-center gap-7 text-sm font-bold text-slate-600 lg:flex">
                <a class="transition hover:text-orange-600" href="{{ route('beranda') }}#cara-lapor">Cara lapor</a>
                <a class="transition hover:text-orange-600" href="{{ route('beranda') }}#kapan-lapor">Kapan Harus Lapor</a>
                <a class="transition hover:text-orange-600" href="{{ route('beranda') }}#mengapa-lapor">Mengapa Harus Lapor</a>
                <a class="transition hover:text-orange-600" href="{{ route('beranda') }}#partisipasi">Ikut Partisipasi</a>
                <a class="transition hover:text-orange-600" href="{{ route('faq') }}">FAQ</a>
            </nav>

            <div class="flex shrink-0 items-center gap-2">
                <a class="hidden rounded-full border border-orange-200 bg-white px-4 py-2.5 text-sm font-extrabold text-orange-700 shadow-theme-xs transition hover:-translate-y-0.5 hover:bg-orange-50 sm:inline-flex" href="{{ route('tracking.form') }}">Tracking</a>
                <a class="rounded-full bg-orange-500 px-5 py-2.5 text-sm font-black uppercase tracking-wide text-white shadow-lg shadow-orange-500/25 transition hover:-translate-y-0.5 hover:bg-orange-600" href="{{ route('pengaduan.buat') }}">Lapor</a>
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
