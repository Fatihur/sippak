@extends('layouts.app')
@section('title','Tracking Laporan')
@section('content')
<section class="relative overflow-hidden bg-[#fff7ea] px-4 py-16 sm:px-6 lg:px-8">
    <div class="absolute -left-24 bottom-0 h-80 w-80 rounded-full bg-orange-300/25 blur-3xl"></div>
    <div class="relative mx-auto grid max-w-6xl items-center gap-10 lg:grid-cols-[1fr_.9fr]">
        <div class="silap-slide-up">
            <p class="section-kicker">Pantau Laporan</p>
            <h1 class="mt-3 text-5xl font-black tracking-tight text-slate-950">Tracking status pengaduan Anda</h1>
            <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">Masukkan nomor tiket dan nomor WhatsApp pelapor untuk melihat perkembangan laporan secara aman.</p>
            <div class="mt-8 grid max-w-xl gap-3 sm:grid-cols-2">
                <div class="rounded-none bg-white p-5 shadow-theme-sm ring-1 ring-orange-100"><i class="fa-solid fa-shield-halved text-orange-500"></i><p class="mt-3 font-black text-slate-950">Identitas sensitif dilindungi.</p></div>
                <div class="rounded-none bg-white p-5 shadow-theme-sm ring-1 ring-orange-100"><i class="fa-solid fa-clock-rotate-left text-orange-500"></i><p class="mt-3 font-black text-slate-950">Riwayat status tersimpan.</p></div>
            </div>
        </div>
        <div class="rounded-none bg-white p-7 shadow-2xl shadow-orange-200/40 ring-1 ring-orange-100 silap-slide-up">
            <form method="POST" action="{{ route('tracking.hasil') }}" class="space-y-5">@csrf
                <div><label class="label">Nomor Tiket</label><input name="nomor_tiket" class="input" placeholder="PPA-2026-0001" required></div>
                <div><label class="label">Nomor WhatsApp</label><input name="nomor_whatsapp" class="input" placeholder="081234567890" inputmode="tel" required></div>
                <button class="w-full rounded-none bg-orange-500 px-7 py-4 text-sm font-black uppercase tracking-wide text-white shadow-xl shadow-orange-500/25 transition hover:-translate-y-1 hover:bg-orange-600">Cek Status</button>
            </form>
        </div>
    </div>
</section>
@endsection
