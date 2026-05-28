@extends('layouts.admin')
@section('title',$dashboardMeta['label'])
@section('content')
@php
    $statCards = [
        ['label' => 'Total Laporan', 'value' => $total, 'icon' => 'fa-solid fa-folder-open', 'tone' => 'blue', 'hint' => 'Semua tiket terverifikasi'],
        ['label' => 'Kasus Bulan Ini', 'value' => $bulanIni, 'icon' => 'fa-solid fa-calendar-days', 'tone' => 'orange', 'hint' => now()->translatedFormat('F Y')],
        ['label' => 'Kasus Selesai', 'value' => $selesai, 'icon' => 'fa-solid fa-circle-check', 'tone' => 'green', 'hint' => 'Status selesai'],
        ['label' => 'Prioritas Tinggi', 'value' => $prioritasTinggi, 'icon' => 'fa-solid fa-triangle-exclamation', 'tone' => 'red', 'hint' => 'Perlu atensi cepat'],
    ];
    $maxTren = max(1, $trenBulanan->max());
    $maxWilayah = max(1, $perWilayah->max() ?: 1);
    $chartColors = ['#465fff','#f79009','#12b76a','#f04438','#7c3aed','#06b6d4','#ec4899','#84cc16'];
@endphp

<div class="mb-6 overflow-hidden rounded-3xl bg-gradient-to-r {{ $dashboardMeta['gradient'] }} p-6 text-white shadow-theme-lg">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-white/75">SILAPAK Kabupaten Sumbawa</p>
            <h2 class="mt-2 text-3xl font-black">{{ $dashboardMeta['label'] }}</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-white/85">{{ $dashboardMeta['subtitle'] }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if($role === 'operator')
                <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-gray-900 shadow-theme-sm"><i class="fa-solid fa-folder-open"></i> Kelola Laporan</a>
            @else
                <a href="{{ route('admin.rekap.export-pdf') }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-gray-900 shadow-theme-sm"><i class="fa-solid fa-file-pdf"></i> Rekap PDF</a>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 md:gap-6">
    @foreach($statCards as $card)
        <div class="stat-card stat-card-{{ $card['tone'] }}">
            <div class="flex items-center justify-between"><div class="stat-icon"><i class="{{ $card['icon'] }}"></i></div><span class="text-xs text-gray-500 dark:text-gray-400">{{ $card['hint'] }}</span></div>
            <div class="mt-5"><span class="text-sm text-gray-500 dark:text-gray-400">{{ $card['label'] }}</span><h3 class="mt-2 text-3xl font-bold text-gray-800 dark:text-white/90">{{ number_format($card['value']) }}</h3></div>
        </div>
    @endforeach
</div>

<div class="mt-6 grid grid-cols-12 gap-4 md:gap-6">
    <section class="panel col-span-12 xl:col-span-8">
        <div class="panel-header"><div><h3 class="panel-title">Tren Kasus Bulanan</h3><p class="panel-subtitle">Diagram batang jumlah laporan pada tahun {{ now()->year }}.</p></div><span class="badge">{{ now()->year }}</span></div>
        <div class="mt-6 flex h-72 items-end gap-3 rounded-2xl bg-gray-50 p-4 dark:bg-gray-900/60">
            @foreach($trenBulanan as $bulan=>$jumlah)
                <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $jumlah }}</span>
                    <div class="w-full rounded-t-xl bg-gradient-to-t from-brand-600 to-brand-300 shadow-theme-xs" style="height: {{ max(4, ($jumlah / $maxTren) * 210) }}px"></div>
                    <span class="text-[11px] text-gray-500 dark:text-gray-400">{{ $bulan }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="panel col-span-12 xl:col-span-4">
        <div class="panel-header"><div><h3 class="panel-title">Kasus per Kategori</h3><p class="panel-subtitle">Diagram lingkaran distribusi jenis kasus.</p></div></div>
        @if($perJenis->isNotEmpty())
            @php
                $start = 0; $segments = [];
                foreach($perJenis->values() as $i => $jumlah) { $deg = ($jumlah / max(1,$total)) * 360; $segments[] = $chartColors[$i % count($chartColors)].' '.$start.'deg '.($start+$deg).'deg'; $start += $deg; }
            @endphp
            <div class="mx-auto mt-6 h-56 w-56 rounded-full shadow-inner" style="background: conic-gradient({{ implode(',', $segments) }});"></div>
            <div class="mt-6 space-y-3">
                @foreach($perJenis as $jenis=>$jumlah)
                    <div class="flex items-center justify-between gap-3 text-sm"><span class="flex items-center gap-2 text-gray-700 dark:text-gray-300"><i class="h-3 w-3 rounded-full" style="background: {{ $chartColors[$loop->index % count($chartColors)] }}"></i>{{ $jenis }}</span><strong>{{ $jumlah }}</strong></div>
                @endforeach
            </div>
        @else
            <div class="empty-state mt-5">Belum ada kategori kasus.</div>
        @endif
    </section>
</div>

<div class="mt-6 grid grid-cols-12 gap-4 md:gap-6">
    <section class="panel col-span-12 lg:col-span-5">
        <h3 class="panel-title">Status Laporan</h3>
        <div class="mt-5 space-y-4">
            @forelse($perStatus as $status=>$totalStatus)
                <div><div class="mb-2 flex justify-between text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">{{ \App\Models\Pengaduan::labelStatus($status) }}</span><span>{{ $totalStatus }}</span></div><div class="h-2 rounded-full bg-gray-100 dark:bg-gray-800"><div class="h-2 rounded-full bg-success-500" style="width: {{ max(8, ($totalStatus / max(1,$total))*100) }}%"></div></div></div>
            @empty
                <div class="empty-state">Belum ada status.</div>
            @endforelse
        </div>
    </section>

    <section class="panel col-span-12 lg:col-span-7">
        <h3 class="panel-title">Statistik Wilayah</h3>
        <p class="panel-subtitle">Diagram garis sederhana berdasarkan kecamatan/wilayah.</p>
        <div class="mt-5 space-y-3">
            @forelse($perWilayah as $wilayah=>$jumlah)
                <div class="grid grid-cols-[120px_1fr_40px] items-center gap-3 text-sm"><span class="truncate text-gray-600 dark:text-gray-400">{{ $wilayah }}</span><div class="h-3 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800"><div class="h-full rounded-full bg-gradient-to-r from-cyan-500 to-blue-600" style="width: {{ max(6, ($jumlah / $maxWilayah)*100) }}%"></div></div><strong class="text-right">{{ $jumlah }}</strong></div>
            @empty
                <div class="empty-state">Belum ada data wilayah.</div>
            @endforelse
        </div>
    </section>
</div>

<section class="panel mt-6">
    <div class="panel-header"><div><h3 class="panel-title">{{ $role === 'operator' ? 'Laporan Terbaru' : 'Data Laporan Terbaru' }}</h3><p class="panel-subtitle">{{ $role === 'operator' ? 'Daftar laporan terverifikasi terbaru.' : 'Mode pimpinan: data laporan tanpa filter, fokus monitoring.' }}</p></div><a href="{{ route('admin.laporan.index') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600">Lihat semua</a></div>
    <div class="mt-5 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800"><div class="overflow-x-auto"><table class="admin-table"><thead><tr><th>Tiket</th><th>Pelapor</th><th>Jenis</th><th>Status</th><th class="text-right">Aksi</th></tr></thead><tbody>
        @forelse($terbaru as $item)
            <tr><td class="font-medium text-gray-800 dark:text-white/90">{{ $item->nomor_tiket }}</td><td>{{ $item->nama_pelapor }}</td><td>{{ $item->jenis_kekerasan }}</td><td><span class="badge">{{ $item->status_label }}</span></td><td class="text-right"><a class="table-action" href="{{ route('admin.laporan.show',$item) }}">Detail</a></td></tr>
        @empty
            <tr><td colspan="5" class="py-8 text-center text-gray-500 dark:text-gray-400">Belum ada laporan terbaru.</td></tr>
        @endforelse
    </tbody></table></div></div>
</section>
@endsection
