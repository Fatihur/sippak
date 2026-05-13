@extends('layouts.app')
@section('title','Hasil Tracking')
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="card"><p class="text-sm text-slate-500">Nomor Tiket</p><h1 class="text-3xl font-bold text-blue-700">{{ $pengaduan->nomor_tiket }}</h1>@isset($modePublik)<p class="mt-2 text-sm text-orange-700">Mode tracking real-time publik. Identitas korban dan pelapor disembunyikan.</p>@endisset<div class="mt-4 grid md:grid-cols-3 gap-4"><div><p class="text-sm text-slate-500">Status</p><span class="badge">{{ $pengaduan->status_label }}</span></div><div><p class="text-sm text-slate-500">Tanggal</p><p>{{ $pengaduan->created_at->format('d/m/Y H:i') }}</p></div><div><p class="text-sm text-slate-500">Catatan Umum</p><p>{{ $pengaduan->catatan_umum ?: '-' }}</p></div></div></div>
    <div class="card"><h2 class="text-xl font-bold mb-4">Riwayat Status</h2><div class="space-y-4">@forelse($pengaduan->riwayatStatus as $riwayat)<div class="border-l-4 border-blue-600 pl-4"><p class="font-semibold">{{ \App\Models\Pengaduan::labelStatus($riwayat->status) }}</p><p class="text-sm text-slate-500">{{ $riwayat->created_at->format('d/m/Y H:i') }}</p><p class="text-slate-600">{{ $riwayat->catatan ?: '-' }}</p></div>@empty<p>Belum ada riwayat.</p>@endforelse</div></div>
</div>
@endsection
