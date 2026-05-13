@extends('layouts.admin')
@section('title','Dashboard')
@section('content')
@php
    $statCards = [
        ['label' => 'Total Laporan', 'value' => $total, 'icon' => 'fa-solid fa-folder-open', 'tone' => 'blue', 'hint' => 'Semua tiket terverifikasi'],
        ['label' => 'Kasus Bulan Ini', 'value' => $bulanIni, 'icon' => 'fa-solid fa-calendar-days', 'tone' => 'orange', 'hint' => now()->translatedFormat('F Y')],
        ['label' => 'Kasus Selesai', 'value' => $selesai, 'icon' => 'fa-solid fa-circle-check', 'tone' => 'green', 'hint' => 'Status selesai'],
        ['label' => 'Prioritas Tinggi', 'value' => $prioritasTinggi, 'icon' => 'fa-solid fa-triangle-exclamation', 'tone' => 'red', 'hint' => 'Perlu atensi cepat'],
    ];
@endphp

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Dashboard SIPPAK</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ringkasan layanan pengaduan PPA Kabupaten Sumbawa.</p>
    </div>
    <a href="{{ route('admin.laporan.index') }}" class="btn-primary"><i class="fa-solid fa-folder-open"></i> Kelola Laporan</a>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 md:gap-6">
    @foreach($statCards as $card)
        <div class="stat-card stat-card-{{ $card['tone'] }}">
            <div class="flex items-center justify-between">
                <div class="stat-icon"><i class="{{ $card['icon'] }}"></i></div>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $card['hint'] }}</span>
            </div>
            <div class="mt-5">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $card['label'] }}</span>
                <h3 class="mt-2 text-3xl font-bold text-gray-800 dark:text-white/90">{{ number_format($card['value']) }}</h3>
            </div>
        </div>
    @endforeach
</div>

<div class="mt-6 grid grid-cols-12 gap-4 md:gap-6">
    <div class="col-span-12 xl:col-span-8">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h3 class="panel-title">Tren Kasus Bulanan</h3>
                    <p class="panel-subtitle">Jumlah laporan per bulan pada tahun berjalan.</p>
                </div>
                <span class="badge">{{ now()->year }}</span>
            </div>
            <div class="mt-6 space-y-5">
                @forelse($trenBulanan as $bulan=>$jumlah)
                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Bulan {{ $bulan }}</span>
                            <span class="text-gray-500 dark:text-gray-400">{{ $jumlah }} kasus</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-full rounded-full bg-brand-500" style="width: {{ max(8, ($jumlah / max(1,$trenBulanan->max()))*100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">Belum ada data tren bulanan.</div>
                @endforelse
            </div>
        </section>
    </div>

    <div class="col-span-12 xl:col-span-4">
        <section class="panel h-full">
            <div class="panel-header">
                <div>
                    <h3 class="panel-title">Kategori Kasus</h3>
                    <p class="panel-subtitle">Distribusi jenis kekerasan.</p>
                </div>
            </div>
            <div class="mt-5 space-y-4">
                @forelse($perJenis as $jenis=>$jumlah)
                    <div>
                        <div class="mb-2 flex justify-between gap-3 text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">{{ $jenis }}</span><strong class="text-gray-800 dark:text-white/90">{{ $jumlah }}</strong></div>
                        <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-800"><div class="h-2 rounded-full bg-brand-500" style="width: {{ max(8, ($jumlah / max(1,$total))*100) }}%"></div></div>
                    </div>
                @empty
                    <div class="empty-state">Belum ada kategori kasus.</div>
                @endforelse
            </div>
        </section>
    </div>
</div>

<div class="mt-6 grid grid-cols-12 gap-4 md:gap-6">
    <section class="panel col-span-12 lg:col-span-4">
        <h3 class="panel-title">Status Laporan</h3>
        <div class="mt-4 divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($perStatus as $status=>$totalStatus)
                <div class="flex items-center justify-between py-3"><span class="text-sm text-gray-700 dark:text-gray-300">{{ \App\Models\Pengaduan::labelStatus($status) }}</span><span class="badge">{{ $totalStatus }}</span></div>
            @empty
                <div class="empty-state">Belum ada status.</div>
            @endforelse
        </div>
    </section>

    <section class="panel col-span-12 lg:col-span-4">
        <h3 class="panel-title">Statistik Wilayah</h3>
        <div class="mt-4 divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($perWilayah as $wilayah=>$jumlah)
                <div class="flex items-center justify-between py-3"><span class="text-sm text-gray-700 dark:text-gray-300">{{ $wilayah }}</span><span class="badge">{{ $jumlah }}</span></div>
            @empty
                <div class="empty-state">Belum ada data wilayah.</div>
            @endforelse
        </div>
    </section>

    <section class="panel col-span-12 lg:col-span-4">
        <h3 class="panel-title">Aksi Cepat</h3>
        <div class="mt-4 grid gap-3">
            <a href="{{ route('admin.laporan.index', ['status' => 'menunggu_verifikasi']) }}" class="quick-action"><span>Verifikasi laporan masuk</span><strong>{{ $pending }}</strong></a>
            <a href="{{ route('admin.laporan.index', ['tingkat_urgensi' => 'tinggi']) }}" class="quick-action"><span>Lihat prioritas tinggi</span><strong>{{ $prioritasTinggi }}</strong></a>
            <a href="{{ route('admin.rekap.index') }}" class="quick-action"><span>Buka rekap laporan</span><strong><i class="fa-solid fa-arrow-up-right-from-square"></i></strong></a>
        </div>
    </section>
</div>

<section class="panel mt-6">
    <div class="panel-header">
        <div>
            <h3 class="panel-title">Laporan Terbaru</h3>
            <p class="panel-subtitle">Daftar laporan terverifikasi terbaru.</p>
        </div>
        <a href="{{ route('admin.laporan.index') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600">Lihat semua</a>
    </div>
    <div class="mt-5 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800">
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead><tr><th>Tiket</th><th>Pelapor</th><th>Jenis</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @forelse($terbaru as $item)
                        <tr><td class="font-medium text-gray-800 dark:text-white/90">{{ $item->nomor_tiket }}</td><td>{{ $item->nama_pelapor }}</td><td>{{ $item->jenis_kekerasan }}</td><td><span class="badge">{{ $item->status_label }}</span></td><td class="text-right"><a class="table-action" href="{{ route('admin.laporan.show',$item) }}">Detail</a></td></tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-gray-500 dark:text-gray-400">Belum ada laporan terbaru.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
