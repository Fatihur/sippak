@extends('layouts.app')
@section('title','Statistik Pengaduan SIPPAK')
@section('content')
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <h1 class="text-4xl font-black text-slate-950">Statistik Pengaduan</h1>
    <p class="mt-3 text-slate-600">Statistik umum untuk transparansi layanan. Data sensitif tidak ditampilkan.</p>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">@foreach([['Total Pengaduan',$totalPengaduan],['Kasus Diproses',$kasusDiproses],['Kasus Selesai',$kasusSelesai],['Kasus Bulan Ini',$kasusBulanIni]] as [$label,$value])<div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200"><p class="text-sm text-slate-500">{{ $label }}</p><p class="mt-2 text-4xl font-black text-blue-700">{{ number_format($value) }}</p></div>@endforeach</div>
    <div class="mt-8 grid gap-6 lg:grid-cols-3"><div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:col-span-2"><h2 class="font-black">Tren Bulanan</h2><div class="mt-5 space-y-4">@forelse($trenBulanan as $bulan=>$jumlah)<div><div class="flex justify-between text-sm"><span>Bulan {{ $bulan }}</span><b>{{ $jumlah }}</b></div><div class="mt-2 h-3 rounded-full bg-slate-100"><div class="h-3 rounded-full bg-blue-600" style="width:{{ max(8,($jumlah/max(1,$trenBulanan->max()))*100) }}%"></div></div></div>@empty<p>Belum ada data.</p>@endforelse</div></div><div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200"><h2 class="font-black">Jenis Kekerasan</h2><div class="mt-5 space-y-3">@forelse($perJenis as $jenis=>$jumlah)<div class="flex justify-between border-b py-2"><span>{{ $jenis }}</span><b>{{ $jumlah }}</b></div>@empty<p>Belum ada data.</p>@endforelse</div></div></div>
</section>
@endsection
