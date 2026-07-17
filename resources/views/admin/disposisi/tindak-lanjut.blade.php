@extends('layouts.admin')
@section('title', 'Tindak Lanjut - '.$laporan->nomor_tiket)
@section('content')
<div class="mb-6">
    <a href="{{ route('admin.laporan.show', $laporan) }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>

<div class="grid gap-6 xl:grid-cols-3">
    <div class="xl:col-span-2 space-y-6">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">Form Tindak Lanjut</h2>
                    <p class="panel-subtitle">Isi laporan penanganan untuk pengaduan {{ $laporan->nomor_tiket }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.disposisi.tindak-lanjut-store', $laporan) }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label class="label">Tanggal Penanganan</label>
                    <input type="date" name="tanggal_penanganan" class="input" value="{{ date('Y-m-d') }}" required>
                </div>

                <div>
                    <label class="label">Hasil Penanganan</label>
                    <textarea name="hasil_penanganan" class="input" rows="5" required placeholder="Jelaskan hasil penanganan yang dilakukan..."></textarea>
                </div>

                <div>
                    <label class="label">Keterangan Tambahan</label>
                    <textarea name="keterangan" class="input" rows="3" placeholder="Keterangan tambahan jika diperlukan..."></textarea>
                </div>

                <div>
                    <label class="label">Upload Berita Acara</label>
                    <input type="file" name="berita_acara" class="input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                </div>

                <div>
                    <label class="label">Upload Dokumentasi</label>
                    <input type="file" name="dokumentasi" class="input" accept=".jpg,.jpeg,.png,.pdf">
                </div>

                <div>
                    <label class="label">Upload Dokumen Pendukung Lainnya</label>
                    <input type="file" name="dokumen_lain" class="input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                </div>

                <div>
                    <label class="label">Status Penanganan</label>
                    <select name="status_penanganan" class="input" required>
                        <option value="diproses">Diproses</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.laporan.show', $laporan) }}" class="btn-secondary">Batal</a>
                    <button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Tindak Lanjut</button>
                </div>
            </form>
        </section>
    </div>

    <aside class="space-y-6">
        <section class="panel">
            <h3 class="panel-title">Informasi Pengaduan</h3>
            <div class="mt-4 space-y-3 text-sm">
                <div><span class="text-xs text-gray-500">Tiket</span><p class="font-semibold">{{ $laporan->nomor_tiket }}</p></div>
                <div><span class="text-xs text-gray-500">Pelapor</span><p class="font-semibold">{{ $laporan->nama_pelapor }}</p></div>
                <div><span class="text-xs text-gray-500">Korban</span><p class="font-semibold">{{ $laporan->nama_korban }}</p></div>
                <div><span class="text-xs text-gray-500">Jenis Kasus</span><p class="font-semibold">{{ $laporan->jenis_kekerasan }}</p></div>
            </div>
        </section>

        @if($laporan->disposisi->isNotEmpty())
            @foreach($laporan->disposisi as $d)
                <section class="panel">
                    <h3 class="panel-title text-sm">{{ $d->tingkat === 'kadis' ? 'Instruksi Kepala Dinas' : 'Arahan Kabid' }}</h3>
                    <p class="mt-3 text-sm text-gray-700 dark:text-gray-300">{{ $d->instruksi ?: $d->arahan_pelaksanaan }}</p>
                    <p class="mt-2 text-xs text-gray-500">- {{ $d->dariUser->name }}</p>
                    <a href="{{ route('admin.disposisi.show', $d) }}" class="mt-2 inline-block text-xs font-medium text-brand-500 hover:underline">Lihat disposisi lengkap</a>
                </section>
            @endforeach
        @endif

        @if($laporan->tindakLanjut->isNotEmpty())
            <section class="panel">
                <h3 class="panel-title text-sm">Riwayat Tindak Lanjut</h3>
                <div class="mt-3 space-y-3">
                    @foreach($laporan->tindakLanjut as $tl)
                        <div class="rounded-lg border border-gray-100 p-3 dark:border-gray-800">
                            <p class="text-xs text-gray-500">{{ $tl->created_at->format('d/m/Y H:i') }}</p>
                            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ Str::limit($tl->hasil_penanganan, 100) }}</p>
                            <span class="mt-1 inline-block text-xs font-medium {{ $tl->status_penanganan === 'selesai' ? 'text-success-600' : 'text-warning-600' }}">
                                {{ $tl->status_penanganan === 'selesai' ? 'Selesai' : 'Diproses' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </aside>
</div>
@endsection
