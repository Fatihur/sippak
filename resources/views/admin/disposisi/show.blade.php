@extends('layouts.admin')
@section('title', 'Detail Disposisi - '.$disposisi->nomor_disposisi)
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <a href="{{ route('admin.disposisi.index') }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.disposisi.cetak', $disposisi) }}" class="btn-secondary" target="_blank"><i class="fa-solid fa-print"></i> Cetak Disposisi</a>
        @if($disposisi->tingkat === 'kadis' && auth()->user()->isKabidPpa() && $disposisi->pengaduan->status === 'didisposisikan_ke_kabid')
            <a href="{{ route('admin.disposisi.kabid-form', $disposisi) }}" class="btn-primary"><i class="fa-solid fa-forward"></i> Teruskan Disposisi</a>
        @endif
        @if($disposisi->tingkat === 'kabid' && auth()->user()->isOperator() && $disposisi->pengaduan->status === 'menunggu_tindak_lanjut_operator')
            <a href="{{ route('admin.disposisi.tindak-lanjut', $disposisi->pengaduan) }}" class="btn-primary"><i class="fa-solid fa-check"></i> Tindak Lanjut</a>
        @endif
    </div>
</div>

<section class="panel max-w-4xl">
    <div class="panel-header">
        <div>
            <h2 class="panel-title">Lembar Disposisi {{ $disposisi->tingkat === 'kadis' ? 'Kepala Dinas' : 'Kepala Bidang' }}</h2>
            <p class="panel-subtitle">Nomor: {{ $disposisi->nomor_disposisi }}</p>
        </div>
        <span class="badge {{ $disposisi->prioritas === 'sangat_mendesak' ? 'bg-error-50 text-error-700' : ($disposisi->prioritas === 'penting' ? 'bg-warning-50 text-warning-700' : 'bg-gray-100 text-gray-700') }}">
            {{ $disposisi->labelPrioritas() }}
        </span>
    </div>

    <div class="mt-6 grid gap-5 sm:grid-cols-2">
        <div class="info-item"><span>Nomor Disposisi</span><strong>{{ $disposisi->nomor_disposisi }}</strong></div>
        <div class="info-item"><span>Tanggal Disposisi</span><strong>{{ $disposisi->tanggal_disposisi->format('d/m/Y') }}</strong></div>
        <div class="info-item"><span>Nomor Pengaduan</span><strong>{{ $disposisi->pengaduan->nomor_tiket }}</strong></div>
        <div class="info-item"><span>Nama Pelapor</span><strong>{{ $disposisi->pengaduan->nama_pelapor }}</strong></div>
        <div class="info-item"><span>Jenis Kasus</span><strong>{{ $disposisi->pengaduan->jenis_kekerasan }}</strong></div>
        <div class="info-item"><span>Tingkat Prioritas</span><strong>{{ $disposisi->labelPrioritas() }}</strong></div>
    </div>

    <div class="mt-5">
        <span class="text-xs text-gray-500">Ringkasan Pengaduan</span>
        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ Str::limit($disposisi->pengaduan->kronologi_kejadian, 500) }}</p>
    </div>

    @if($disposisi->instruksi)
        <div class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-900/60">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Instruksi {{ $disposisi->tingkat === 'kadis' ? 'Kepala Dinas' : 'Kepala Bidang' }}</span>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $disposisi->instruksi ?: $disposisi->arahan_pelaksanaan }}</p>
        </div>
    @endif

    @if($disposisi->nama_petugas)
        <div class="mt-5 info-item"><span>Petugas yang Ditunjuk</span><strong>{{ $disposisi->nama_petugas }}</strong></div>
    @endif

    <div class="mt-8 flex justify-end">
        <div class="w-64 text-center">
            <p class="text-sm">Sumbawa, {{ $disposisi->tanggal_disposisi->format('d F Y') }}</p>
            <div class="mt-12 border-t border-gray-300 pt-2">
                <p class="text-sm font-bold underline">{{ $disposisi->dariUser->name }}</p>
                <p class="text-xs text-gray-500">{{ $disposisi->dariUser->jabatan ?: ($disposisi->tingkat === 'kadis' ? 'Kepala Dinas P2KBP3A' : 'Kepala Bidang PPA') }}</p>
                <p class="text-xs text-gray-500">NIP. {{ $disposisi->dariUser->nip ?: '-' }}</p>
            </div>
        </div>
    </div>
</section>
@endsection
