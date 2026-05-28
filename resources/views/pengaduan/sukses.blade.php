@extends('layouts.app')
@section('title','Pengaduan Terkirim')
@section('content')
<section class="relative overflow-hidden bg-[#fff7ea] px-4 py-16 sm:px-6 lg:px-8">
    <div class="absolute -right-20 top-10 h-80 w-80 rounded-full bg-green-300/20 blur-3xl"></div>
    <div class="relative mx-auto max-w-3xl rounded-none bg-white p-8 text-center shadow-2xl shadow-orange-200/40 ring-1 ring-orange-100 silap-slide-up">
        <div class="mx-auto mb-5 grid h-20 w-20 place-items-center rounded-full bg-green-100 text-4xl text-green-700"><i class="fa-solid fa-check"></i></div>
        <p class="section-kicker">Laporan Terkirim</p>
        <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-950">Pengaduan Berhasil Dikirim</h1>
        <p class="mx-auto mt-4 max-w-xl leading-7 text-slate-600">Simpan nomor tiket berikut untuk memantau perkembangan laporan. Nomor ini juga dikirim melalui notifikasi jika kontak tersedia.</p>
        <div class="mt-7 rounded-none bg-slate-950 p-6 text-4xl font-black tracking-tight text-white shadow-xl">{{ $pengaduan->nomor_tiket }}</div>
        <p class="mt-4 text-sm font-semibold text-slate-500">Tracking membutuhkan nomor tiket dan nomor WhatsApp yang digunakan saat melapor.</p>
        <div class="mt-7 flex flex-wrap justify-center gap-3">
            <a href="{{ route('tracking.form') }}" class="rounded-none bg-orange-500 px-6 py-3 font-black text-white shadow-lg shadow-orange-500/25 transition hover:-translate-y-1">Tracking Laporan</a>
            <a href="{{ route('tracking.publik', $pengaduan->nomor_tiket) }}" class="rounded-none border border-orange-200 bg-orange-50 px-6 py-3 font-black text-orange-700 transition hover:-translate-y-1">Link Tracking Real-time</a>
        </div>
    </div>
</section>
@endsection
