@extends('layouts.app')
@section('title','Tracking Laporan')
@section('content')
<div class="max-w-lg mx-auto card">
    <h1 class="text-3xl font-bold">Tracking Laporan</h1>
    <p class="mt-2 text-slate-600">Masukkan nomor tiket dan nomor WhatsApp pelapor.</p>
    <form method="POST" action="{{ route('tracking.hasil') }}" class="mt-6 space-y-4">@csrf
        <div><label class="label">Nomor Tiket</label><input name="nomor_tiket" class="input" placeholder="PPA-2026-0001" required></div>
        <div><label class="label">Nomor WhatsApp</label><input name="nomor_whatsapp" class="input" required></div>
        <button class="btn-primary w-full">Cek Status</button>
    </form>
</div>
@endsection
