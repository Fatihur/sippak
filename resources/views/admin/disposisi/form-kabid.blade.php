@extends('layouts.admin')
@section('title', 'Teruskan Disposisi')
@section('content')
<div class="mb-6">
    <a href="{{ route('admin.disposisi.show', $disposisi) }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>

<section class="panel max-w-3xl">
    <div class="panel-header">
        <div>
            <h2 class="panel-title">Teruskan Disposisi ke Operator</h2>
            <p class="panel-subtitle">Disposisi dari {{ $disposisi->dariUser->name }}</p>
        </div>
    </div>

    <div class="mt-5 rounded-xl bg-gray-50 p-4 dark:bg-gray-900/60">
        <div class="grid gap-3 sm:grid-cols-2">
            <div><span class="text-xs text-gray-500">Nomor Disposisi</span><p class="font-semibold">{{ $disposisi->nomor_disposisi }}</p></div>
            <div><span class="text-xs text-gray-500">Tanggal</span><p class="font-semibold">{{ $disposisi->tanggal_disposisi->format('d/m/Y') }}</p></div>
            <div><span class="text-xs text-gray-500">Pelapor</span><p class="font-semibold">{{ $disposisi->pengaduan->nama_pelapor }}</p></div>
            <div><span class="text-xs text-gray-500">Jenis Kasus</span><p class="font-semibold">{{ $disposisi->pengaduan->jenis_kekerasan }}</p></div>
        </div>
        <div class="mt-3"><span class="text-xs text-gray-500">Instruksi Kepala Dinas</span><p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $disposisi->instruksi }}</p></div>
    </div>

    <form method="POST" action="{{ route('admin.disposisi.kabid-store', $disposisi) }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <label class="label">Nama Petugas yang Ditunjuk</label>
            <select name="nama_petugas" class="input" required>
                <option value="">-- Pilih Petugas --</option>
                @foreach($operatorList as $op)
                    <option value="{{ $op->name }}">{{ $op->name }} - {{ $op->jabatan ?: 'Admin/Operator' }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="label">Arahan Pelaksanaan</label>
            <textarea name="arahan_pelaksanaan" class="input" rows="5" required placeholder="Tulis arahan pelaksanaan untuk Operator..."></textarea>
        </div>

        <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-900/60">
            <div>
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Tanda Tangan</p>
                <p class="text-xs text-gray-500">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500">{{ auth()->user()->jabatan ?: 'Kepala Bidang PPA' }}</p>
                <p class="text-xs text-gray-500">NIP. {{ auth()->user()->nip ?: '-' }}</p>
            </div>
            <div class="text-right text-xs text-gray-400">
                <p>Sumbawa, {{ now()->format('d F Y') }}</p>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.disposisi.show', $disposisi) }}" class="btn-secondary">Batal</a>
            <button class="btn-primary"><i class="fa-solid fa-paper-plane"></i> Kirim</button>
        </div>
    </form>
</section>
@endsection
