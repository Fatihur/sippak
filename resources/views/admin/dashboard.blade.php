@extends('layouts.admin')
@section('title',$dashboardMeta['label'])
@section('content')
@php
    if ($role === 'kepala_dinas') {
        $statCards = [
            ['label' => 'Total Kasus Masuk', 'value' => number_format($total), 'icon' => 'fa-solid fa-folder-open', 'tone' => 'blue', 'hint' => 'Semua tiket terdaftar'],
            ['label' => 'Kasus Selesai', 'value' => number_format($selesai), 'icon' => 'fa-solid fa-circle-check', 'tone' => 'green', 'hint' => 'Kasus ditangani tuntas'],
            ['label' => 'Rasio Penyelesaian', 'value' => $persentaseSelesai . '%', 'icon' => 'fa-solid fa-chart-line', 'tone' => 'purple', 'hint' => 'Persentase keberhasilan'],
            ['label' => 'Kasus Ditolak', 'value' => number_format($ditolak), 'icon' => 'fa-solid fa-ban', 'tone' => 'red', 'hint' => 'Laporan tidak valid'],
        ];
    } elseif ($role === 'kepala_bidang') {
        $statCards = [
            ['label' => 'Total Kasus Diawasi', 'value' => number_format($total), 'icon' => 'fa-solid fa-shield-halved', 'tone' => 'emerald', 'hint' => 'Total kasus dalam pantauan'],
            ['label' => 'Atensi Urgensi Tinggi', 'value' => number_format($prioritasTinggi), 'icon' => 'fa-solid fa-triangle-exclamation', 'tone' => 'red', 'hint' => 'Perlu tindakan cepat'],
            ['label' => 'Kasus Dalam Proses', 'value' => number_format(max(0, $total - $selesai - $ditolak)), 'icon' => 'fa-solid fa-spinner', 'tone' => 'orange', 'hint' => 'Sedang ditangani'],
            ['label' => 'Kasus Selesai', 'value' => number_format($selesai), 'icon' => 'fa-solid fa-circle-check', 'tone' => 'green', 'hint' => 'Kasus sukses ditutup'],
        ];
    } else {
        $statCards = [
            ['label' => 'Total Laporan', 'value' => number_format($total), 'icon' => 'fa-solid fa-folder-open', 'tone' => 'blue', 'hint' => 'Semua tiket terverifikasi'],
            ['label' => 'Kasus Bulan Ini', 'value' => number_format($bulanIni), 'icon' => 'fa-solid fa-calendar-days', 'tone' => 'orange', 'hint' => now()->translatedFormat('F Y')],
            ['label' => 'Kasus Selesai', 'value' => number_format($selesai), 'icon' => 'fa-solid fa-circle-check', 'tone' => 'green', 'hint' => 'Status selesai'],
            ['label' => 'Prioritas Tinggi', 'value' => number_format($prioritasTinggi), 'icon' => 'fa-solid fa-triangle-exclamation', 'tone' => 'red', 'hint' => 'Perlu atensi cepat'],
        ];
    }
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
            @elseif($role === 'kepala_dinas')
                <a href="{{ route('admin.rekap.export-pdf') }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-gray-900 shadow-theme-sm"><i class="fa-solid fa-file-pdf"></i> Rekap PDF</a>
            @else
                <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-gray-900 shadow-theme-sm"><i class="fa-solid fa-magnifying-glass"></i> Tinjau Kasus</a>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 md:gap-6">
    @foreach($statCards as $card)
        <div class="stat-card stat-card-{{ $card['tone'] }}">
            <div class="flex items-center justify-between"><div class="stat-icon"><i class="{{ $card['icon'] }}"></i></div><span class="text-xs text-gray-500 dark:text-gray-400">{{ $card['hint'] }}</span></div>
            <div class="mt-5"><span class="text-sm text-gray-500 dark:text-gray-400">{{ $card['label'] }}</span><h3 class="mt-2 text-3xl font-bold text-gray-800 dark:text-white/90">{{ $card['value'] }}</h3></div>
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

@if($role === 'kepala_bidang')
    <!-- KHUSUS TAMPILAN KABID PPA: PENGAWASAN & KOORDINASI -->
    <div class="mt-6 grid grid-cols-12 gap-4 md:gap-6">
        <section class="panel col-span-12 lg:col-span-5">
            <div class="panel-header">
                <div>
                    <h3 class="panel-title text-emerald-600 dark:text-emerald-400">Disposisi Masuk</h3>
                    <p class="panel-subtitle">Disposisi terbaru dari Kepala Dinas yang perlu ditindaklanjuti.</p>
                </div>
                <span class="badge bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400">{{ $disposisiMasuk->count() }} Baru</span>
            </div>
            <div class="mt-5 space-y-4">
                @forelse($disposisiMasuk as $disp)
                    <div class="rounded-xl border border-gray-150 p-4 dark:border-gray-800 bg-gray-25 dark:bg-gray-900/20">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('admin.disposisi.show', $disp) }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                {{ $disp->pengaduan->nomor_tiket }}
                            </a>
                            <small class="text-[10px] text-gray-400">{{ $disp->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">{{ $disp->pengaduan->nama_korban }}</p>
                        <p class="text-xs text-gray-500">Prioritas: <span class="font-medium {{ $disp->prioritas === 'sangat_mendesak' ? 'text-error-600' : ($disp->prioritas === 'penting' ? 'text-warning-600' : '') }}">{{ $disp->labelPrioritas() }}</span></p>
                        <p class="mt-1 text-xs text-gray-500">Dari: {{ $disp->dariUser->name }}</p>
                    </div>
                @empty
                    <div class="empty-state">Belum ada disposisi masuk.</div>
                @endforelse
                @if($disposisiMasuk->isNotEmpty())
                    <a href="{{ route('admin.disposisi.index') }}" class="text-sm font-medium text-brand-500 hover:underline">Lihat semua disposisi</a>
                @endif
            </div>
        </section>
        <section class="panel col-span-12 lg:col-span-7">
            <div class="panel-header">
                <div>
                    <h3 class="panel-title text-emerald-600 dark:text-emerald-400">Atensi Kasus Urgensi Tinggi</h3>
                    <p class="panel-subtitle">Daftar kasus aktif berstatus prioritas tinggi yang membutuhkan pengawasan.</p>
                </div>
                <span class="badge bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-400">{{ $kasusAtensi->count() }} Atensi</span>
            </div>
            <div class="mt-5 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800">
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Tiket</th>
                                <th>Korban</th>
                                <th>Jenis</th>
                                <th>Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kasusAtensi as $item)
                                <tr>
                                    <td class="font-medium text-gray-800 dark:text-white/90">{{ $item->nomor_tiket }}</td>
                                    <td>{{ $item->nama_korban }} ({{ $item->umur_korban }} Th)</td>
                                    <td>{{ $item->jenis_kekerasan }}</td>
                                    <td><span class="badge">{{ $item->status_label }}</span></td>
                                    <td class="text-right">
                                        <a class="table-action text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10" href="{{ route('admin.laporan.show',$item) }}">
                                            <i class="fa-solid fa-comment-dots"></i> Beri Catatan
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500 dark:text-gray-400">Tidak ada kasus urgensi tinggi yang butuh atensi saat ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="panel col-span-12 lg:col-span-4">
            <h3 class="panel-title text-emerald-600 dark:text-emerald-400">Catatan Koordinasi Terbaru</h3>
            <p class="panel-subtitle">Arahan pengawasan terakhir Anda kepada operator/petugas lapangan.</p>
            <div class="mt-5 space-y-4">
                @forelse($catatanKabid as $catatan)
                    <div class="rounded-xl border border-gray-150 p-4 dark:border-gray-800 bg-gray-25 dark:bg-gray-900/20">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('admin.laporan.show', $catatan->pengaduan) }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                {{ $catatan->pengaduan->nomor_tiket }}
                            </a>
                            <small class="text-[10px] text-gray-400">{{ $catatan->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mt-2 text-xs text-gray-600 dark:text-gray-300 line-clamp-3">
                            {{ str_replace('Catatan Kabid PPA: ', '', $catatan->catatan) }}
                        </p>
                    </div>
                @empty
                    <div class="empty-state">Belum ada catatan koordinasi yang Anda buat.</div>
                @endforelse
            </div>
        </section>
    </div>
@elseif($role === 'kepala_dinas')
    <!-- KHUSUS TAMPILAN KEPALA DINAS: EVALUASI & KPI EKSEKUTIF -->
    <div class="mt-6 grid grid-cols-12 gap-4 md:gap-6">
        <section class="panel col-span-12 lg:col-span-5">
            <div class="panel-header">
                <div>
                    <h3 class="panel-title text-purple-600 dark:text-purple-400">Wilayah Kasus Tertinggi (Top 3)</h3>
                    <p class="panel-subtitle">Kecamatan dengan akumulasi jumlah aduan terbanyak.</p>
                </div>
            </div>
            <div class="mt-6 space-y-4">
                @forelse($topKecamatan as $index => $wilayah)
                    <div class="flex items-center justify-between rounded-xl border border-gray-100 p-4 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/30">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg font-black text-white @if($index === 0) bg-amber-500 @elseif($index === 1) bg-slate-400 @else bg-amber-700 @endif">
                                {{ $index + 1 }}
                            </span>
                            <span class="font-semibold text-gray-750 dark:text-gray-350">{{ $wilayah->wilayah }}</span>
                        </div>
                        <span class="badge bg-purple-50 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400">{{ $wilayah->total }} Kasus</span>
                    </div>
                @empty
                    <div class="empty-state">Belum ada data wilayah.</div>
                @endforelse
            </div>
        </section>

        <section class="panel col-span-12 lg:col-span-7">
            <h3 class="panel-title text-purple-600 dark:text-purple-400">Efisiensi & Status Penanganan Laporan</h3>
            <p class="panel-subtitle">Rasio penyebaran laporan berdasarkan tahapan progres penanganan.</p>
            <div class="mt-5 space-y-4">
                @forelse($perStatus as $status=>$totalStatus)
                    <div>
                        <div class="mb-2 flex justify-between text-sm">
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ \App\Models\Pengaduan::labelStatus($status) }}</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ $totalStatus }} Laporan <small class="font-normal text-gray-500">({{ round(($totalStatus / max(1,$total))*100, 1) }}%)</small></span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-2 rounded-full @if($status === 'selesai') bg-success-500 @elseif($status === 'menunggu_verifikasi') bg-warning-500 @elseif($status === 'dalam_penanganan') bg-brand-500 @else bg-purple-500 @endif" style="width: {{ max(4, ($totalStatus / max(1,$total))*100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">Belum ada status.</div>
                @endforelse
            </div>
        </section>
    </div>
@endif

<!-- BAGIAN STATUS LAPORAN & WILAYAH STANDARD UNTUK OPERATOR -->
@if($role === 'operator')
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
@elseif($role === 'kepala_bidang')
    <!-- TAMPILAN STATUS LAPORAN & WILAYAH KECIL DI BAWAH UNTUK KABID -->
    <div class="mt-6 grid grid-cols-12 gap-4 md:gap-6">
        <section class="panel col-span-12 lg:col-span-6">
            <h3 class="panel-title">Status Penanganan Bidang PPA</h3>
            <div class="mt-5 space-y-4">
                @forelse($perStatus as $status=>$totalStatus)
                    <div><div class="mb-2 flex justify-between text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">{{ \App\Models\Pengaduan::labelStatus($status) }}</span><span>{{ $totalStatus }}</span></div><div class="h-2 rounded-full bg-gray-100 dark:bg-gray-800"><div class="h-2 rounded-full bg-emerald-500" style="width: {{ max(8, ($totalStatus / max(1,$total))*100) }}%"></div></div></div>
                @empty
                    <div class="empty-state">Belum ada status.</div>
                @endforelse
            </div>
        </section>

        <section class="panel col-span-12 lg:col-span-6">
            <h3 class="panel-title">Distribusi Wilayah Kasus</h3>
            <div class="mt-5 space-y-3">
                @forelse($perWilayah as $wilayah=>$jumlah)
                    <div class="grid grid-cols-[120px_1fr_40px] items-center gap-3 text-sm"><span class="truncate text-gray-600 dark:text-gray-400">{{ $wilayah }}</span><div class="h-3 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800"><div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-600" style="width: {{ max(6, ($jumlah / $maxWilayah)*100) }}%"></div></div><strong class="text-right">{{ $jumlah }}</strong></div>
                @empty
                    <div class="empty-state">Belum ada data wilayah.</div>
                @endforelse
            </div>
        </section>
    </div>
@endif

<section class="panel mt-6">
    <div class="panel-header"><div><h3 class="panel-title">{{ $role === 'operator' ? 'Laporan Terbaru' : 'Data Laporan Terbaru' }}</h3><p class="panel-subtitle">{{ $role === 'operator' ? 'Daftar laporan terverifikasi terbaru.' : 'Mode pimpinan: data laporan tanpa filter, fokus monitoring.' }}</p></div><a href="{{ route('admin.laporan.index') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600">Lihat semua</a></div>
    <div class="mt-5 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800"><div class="overflow-x-auto"><table class="admin-table"><thead><tr><th>Tiket</th>@if($role === 'operator')<th>Pelapor</th>@endif<th>Jenis</th><th>Status</th><th class="text-right">Aksi</th></tr></thead><tbody>
        @forelse($terbaru as $item)
            <tr><td class="font-medium text-gray-800 dark:text-white/90">{{ $item->nomor_tiket }}</td>@if($role === 'operator')<td>{{ $item->nama_pelapor }}</td>@endif<td>{{ $item->jenis_kekerasan }}</td><td><span class="badge">{{ $item->status_label }}</span></td><td class="text-right"><a class="table-action" href="{{ route('admin.laporan.show',$item) }}">Detail</a></td></tr>
        @empty
            <tr><td colspan="{{ $role === 'operator' ? 5 : 4 }}" class="py-8 text-center text-gray-500 dark:text-gray-400">Belum ada laporan terbaru.</td></tr>
        @endforelse
    </tbody></table></div></div>
</section>
@endsection
