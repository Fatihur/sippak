@extends('layouts.admin')
@section('title', 'Riwayat Disposisi - '.$laporan->nomor_tiket)
@section('content')
<div class="mb-6">
    <a href="{{ route('admin.laporan.show', $laporan) }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali ke Detail</a>
</div>

<section class="panel">
    <div class="panel-header">
        <div>
            <h2 class="panel-title">Riwayat Disposisi</h2>
            <p class="panel-subtitle">Pengaduan: {{ $laporan->nomor_tiket }} - {{ $laporan->nama_pelapor }}</p>
        </div>
        <span class="badge">{{ $laporan->status_label }}</span>
    </div>

    <div class="mt-6 space-y-6">
        @php $riwayat = collect(); @endphp

        {{-- Verifikasi Admin --}}
        @php
            $verif = $laporan->riwayatStatus->firstWhere('status', 'menunggu_disposisi_kadis');
        @endphp
        @if($verif)
            @php $riwayat->push(['waktu' => $verif->created_at, 'aksi' => 'Admin melakukan verifikasi pengaduan', 'user' => $verif->user?->name ?: 'Sistem']) @endphp
        @endif

        {{-- Disposisi Kadis --}}
        @foreach($laporan->disposisi->where('tingkat', 'kadis') as $d)
            @php
                $riwayat->push([
                    'waktu' => $d->created_at,
                    'aksi' => 'Kepala Dinas memberikan disposisi kepada '.($d->untukUser?->name ?: 'Kepala Bidang'),
                    'user' => $d->dariUser->name,
                    'link' => route('admin.disposisi.show', $d),
                ]);
            @endphp
        @endforeach

        {{-- Disposisi Kabid --}}
        @foreach($laporan->disposisi->where('tingkat', 'kabid') as $d)
            @php
                $riwayat->push([
                    'waktu' => $d->created_at,
                    'aksi' => 'Kepala Bidang meneruskan disposisi kepada Operator. Petugas: '.($d->nama_petugas ?: '-'),
                    'user' => $d->dariUser->name,
                    'link' => route('admin.disposisi.show', $d),
                ]);
            @endphp
        @endforeach

        {{-- Tindak Lanjut --}}
        @foreach($laporan->tindakLanjut as $tl)
            @php
                $riwayat->push([
                    'waktu' => $tl->created_at,
                    'aksi' => 'Tindak lanjut: '.Str::limit($tl->hasil_penanganan, 100).' ('.$tl->status_penanganan.')',
                    'user' => $tl->user?->name ?: 'Operator',
                ]);
            @endphp
        @endforeach

        {{-- Riwayat status lain --}}
        @foreach($laporan->riwayatStatus as $rs)
            @php
                $sudah = $riwayat->contains(fn($r) => $r['waktu']->eq($rs->created_at));
                if (!$sudah) {
                    $riwayat->push([
                        'waktu' => $rs->created_at,
                        'aksi' => 'Status berubah: '.\App\Models\Pengaduan::labelStatus($rs->status),
                        'user' => $rs->user?->name ?: 'Sistem',
                    ]);
                }
            @endphp
        @endforeach

        {{-- Sort by time --}}
        @php $riwayat = $riwayat->sortByDesc('waktu'); @endphp

        @forelse($riwayat as $r)
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div>
                    <p class="font-semibold text-gray-800 dark:text-white/90">{{ $r['aksi'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $r['waktu']->format('d/m/Y H:i') }} • {{ $r['user'] }}</p>
                    @if(isset($r['link']))
                        <a href="{{ $r['link'] }}" class="mt-1 inline-block text-xs font-medium text-brand-500 hover:underline">Lihat disposisi</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">Belum ada riwayat disposisi.</div>
        @endforelse
    </div>
</section>
@endsection
