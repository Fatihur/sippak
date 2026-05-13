@extends('layouts.app')
@section('title','Pengaduan Terkirim')
@section('content')
<div class="max-w-xl mx-auto card text-center">
    <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-full bg-green-100 text-3xl text-green-700"><i class="fa-solid fa-check"></i></div>
    <h1 class="text-3xl font-bold">Pengaduan Berhasil Dikirim</h1>
    <p class="mt-3 text-slate-600">Simpan nomor tiket berikut untuk memantau status laporan.</p>
    <div class="mt-6 rounded-2xl bg-blue-50 p-5 text-3xl font-extrabold text-blue-700">{{ $pengaduan->nomor_tiket }}</div>
    <p class="mt-4 text-sm text-slate-500">Tracking membutuhkan nomor tiket dan nomor WhatsApp yang digunakan saat melapor.</p>
    <div class="mt-6 flex flex-wrap justify-center gap-3">
        <a href="{{ route('tracking.form') }}" class="btn-primary">Tracking Laporan</a>
        <a href="{{ route('tracking.publik', $pengaduan->nomor_tiket) }}" class="btn-secondary">Link Tracking Real-time</a>
    </div>
</div>
@endsection
