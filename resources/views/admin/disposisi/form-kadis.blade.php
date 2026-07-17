@extends('layouts.admin')
@section('title', 'Form Disposisi - '.$laporan->nomor_tiket)
@section('content')
<div class="mb-6">
    <a href="{{ route('admin.disposisi.index') }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>

<section class="panel max-w-3xl">
    <div class="panel-header">
        <div>
            <h2 class="panel-title">Form Disposisi Kepala Dinas</h2>
            <p class="panel-subtitle">Buat disposisi untuk pengaduan {{ $laporan->nomor_tiket }}</p>
        </div>
    </div>

    <div class="mt-5 rounded-xl bg-gray-50 p-4 dark:bg-gray-900/60">
        <div class="grid gap-3 sm:grid-cols-2">
            <div><span class="text-xs text-gray-500">Nomor Tiket</span><p class="font-semibold">{{ $laporan->nomor_tiket }}</p></div>
            <div><span class="text-xs text-gray-500">Pelapor</span><p class="font-semibold">{{ $laporan->nama_pelapor }}</p></div>
            <div><span class="text-xs text-gray-500">Korban</span><p class="font-semibold">{{ $laporan->nama_korban }}</p></div>
            <div><span class="text-xs text-gray-500">Jenis Kasus</span><p class="font-semibold">{{ $laporan->jenis_kekerasan }}</p></div>
        </div>
        <div class="mt-3"><span class="text-xs text-gray-500">Ringkasan Kronologi</span><p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ Str::limit($laporan->kronologi_kejadian, 300) }}</p></div>
    </div>

    <form method="POST" action="{{ route('admin.disposisi.kadis-store', $laporan) }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <label class="label">Tujuan Disposisi (Kepala Bidang PPA)</label>
            <select name="tujuan_kabid" class="input" required>
                <option value="">-- Pilih Kabid --</option>
                @foreach($kabidList as $kabid)
                    <option value="{{ $kabid->id }}">{{ $kabid->name }} - {{ $kabid->jabatan ?: 'Kabid PPA' }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="label">Tingkat Prioritas</label>
            <select name="prioritas" class="input" required>
                <option value="biasa">Biasa</option>
                <option value="penting">Penting</option>
                <option value="sangat_mendesak">Sangat Mendesak</option>
            </select>
        </div>

        <div>
            <label class="label">Instruksi Kepala Dinas</label>
            <textarea name="instruksi" class="input" rows="6" required placeholder="Tulis instruksi penanganan untuk Kabid PPA..."></textarea>
        </div>

        <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-900/60">
            <div>
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Tanda Tangan</p>
                <p class="text-xs text-gray-500">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500">{{ auth()->user()->jabatan ?: 'Kepala Dinas P2KBP3A' }}</p>
                <p class="text-xs text-gray-500">NIP. {{ auth()->user()->nip ?: '-' }}</p>
            </div>
            <div class="text-right text-xs text-gray-400">
                <p>Sumbawa, {{ now()->format('d F Y') }}</p>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.disposisi.index') }}" class="btn-secondary">Batal</a>
            <button class="btn-primary"><i class="fa-solid fa-paper-plane"></i> Kirim Disposisi</button>
        </div>
    </form>
</section>
@endsection
