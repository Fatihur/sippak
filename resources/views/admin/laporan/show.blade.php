@extends('layouts.admin')
@section('title','Detail Laporan '.$laporan->nomor_tiket)
@section('content')
<div class="mb-6 flex flex-col gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03] md:flex-row md:items-center md:justify-between">
    <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nomor Tiket</p>
        <h2 class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $laporan->nomor_tiket }}</h2>
    </div>
    <div class="flex flex-wrap gap-2">
        <span class="badge">{{ $laporan->status_label }}</span>
        <span class="urgency urgency-{{ $laporan->tingkat_urgensi }}">Urgensi {{ ucfirst($laporan->tingkat_urgensi) }}</span>
        <a href="{{ route('admin.laporan.cetak', $laporan) }}" class="btn-secondary" target="_blank"><i class="fa-solid fa-print"></i> Cetak Laporan</a>
        <a href="{{ route('admin.laporan.cetak-disposisi', $laporan) }}" class="btn-secondary" target="_blank"><i class="fa-solid fa-file-lines"></i> Cetak Disposisi</a>
        @if(auth()->user()->canManageLaporan())
            <a href="{{ route('admin.laporan.edit', $laporan) }}" class="btn-secondary"><i class="fa-solid fa-pen"></i> Edit</a>
            <form method="POST" action="{{ route('admin.laporan.destroy', $laporan) }}" onsubmit="return confirm('Hapus tiket {{ $laporan->nomor_tiket }}? Data tidak bisa dikembalikan.')">
                @csrf @method('DELETE')
                <button class="btn-danger" type="submit"><i class="fa-solid fa-trash"></i> Hapus</button>
            </form>
        @endif
    </div>
</div>

<div class="grid gap-6 xl:grid-cols-3">
    <div class="xl:col-span-2 space-y-6">
        <section class="panel">
            <div class="panel-header"><div><h3 class="panel-title">Informasi Pengaduan</h3><p class="panel-subtitle">Data sensitif hanya untuk petugas berwenang.</p></div></div>
            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div class="info-item"><span>Pelapor</span><strong>{{ $laporan->nama_pelapor }}</strong><small>{{ $laporan->nomor_whatsapp }}</small></div>
                <div class="info-item"><span>NIK Pelapor</span><strong>{{ $laporan->nik_pelapor }}</strong><small>{{ $laporan->email_pelapor ?: 'Email tidak diisi' }}</small></div>
                <div class="info-item"><span>Korban</span><strong>{{ $laporan->nama_korban }}</strong><small>{{ $laporan->umur_korban }} tahun, {{ $laporan->jenis_kelamin_korban }}</small></div>
                <div class="info-item"><span>Hubungan</span><strong>{{ $laporan->hubungan_dengan_pelapor }}</strong><small>Dengan pelapor</small></div>
                <div class="info-item"><span>Jenis Kekerasan</span><strong>{{ $laporan->jenis_kekerasan }}</strong><small>Klasifikasi kasus</small></div>
                <div class="info-item"><span>Tanggal Kejadian</span><strong>{{ $laporan->tanggal_kejadian->format('d/m/Y') }}</strong><small>{{ $laporan->lokasi_kejadian }}</small></div>
                <div class="info-item sm:col-span-2"><span>Alamat Pelapor</span><strong>{{ $laporan->alamat_pelapor }}</strong><small>Kecamatan: {{ $laporan->kecamatan ?: '-' }}</small></div>
            </div>
        </section>

        <section class="panel">
            <h3 class="panel-title">Kronologi Kejadian</h3>
            <p class="mt-4 leading-7 text-gray-700 dark:text-gray-300">{{ $laporan->kronologi_kejadian }}</p>
        </section>

        <section class="panel">
            <div class="panel-header"><div><h3 class="panel-title">Bukti Pendukung</h3><p class="panel-subtitle">File tersimpan private dan hanya dapat diakses setelah login.</p></div></div>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                @forelse($laporan->bukti as $bukti)
                    @php
                        $isImage = str_starts_with((string) $bukti->mime_type, 'image/');
                        $isPdf = $bukti->mime_type === 'application/pdf';
                    @endphp
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
                        @if($isImage)
                            <a href="{{ route('admin.bukti.preview',$bukti->id) }}" target="_blank"><img src="{{ route('admin.bukti.preview',$bukti->id) }}" alt="{{ $bukti->nama_asli }}" class="h-44 w-full object-cover"></a>
                        @elseif($isPdf)
                            <iframe src="{{ route('admin.bukti.preview',$bukti->id) }}" class="h-44 w-full bg-gray-50 dark:bg-gray-900"></iframe>
                        @else
                            <div class="grid h-44 place-items-center bg-gray-50 text-5xl text-brand-500 dark:bg-gray-900"><i class="fa-solid fa-file-word"></i></div>
                        @endif
                        <div class="p-4">
                            <strong class="block truncate text-sm text-gray-800 dark:text-white/90">{{ $bukti->nama_asli }}</strong>
                            <small class="mt-1 block text-xs text-gray-500 dark:text-gray-400">{{ number_format($bukti->ukuran_file / 1024, 1) }} KB</small>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @if($isImage || $isPdf)<a class="table-action" href="{{ route('admin.bukti.preview',$bukti->id) }}" target="_blank">Preview</a>@endif
                                <a class="table-action" href="{{ route('admin.bukti.unduh',$bukti->id) }}">Unduh</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state sm:col-span-2">Tidak ada bukti pendukung.</div>
                @endforelse
            </div>
        </section>

        <section class="panel">
            <h3 class="panel-title">Riwayat Status</h3>
            <div class="mt-5 space-y-4">
                @forelse($laporan->riwayatStatus as $r)
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-white/90">{{ \App\Models\Pengaduan::labelStatus($r->status) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $r->created_at->format('d/m/Y H:i') }} • {{ $r->user?->name ?: 'Sistem' }}</p>
                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $r->catatan ?: '-' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">Belum ada riwayat status.</div>
                @endforelse
            </div>
        </section>
    </div>

    <aside class="space-y-6">
        @if(auth()->user()->canManageLaporan())
            <section class="panel">
                <h3 class="panel-title">Update Status</h3>
                <form method="POST" action="{{ route('admin.laporan.status',$laporan) }}" class="mt-4 space-y-4">
                    @csrf @method('PATCH')
                    <div><label class="label">Status</label><select name="status" class="input">@foreach(\App\Models\Pengaduan::opsiStatus() as $key=>$label)<option value="{{ $key }}" @selected($laporan->status===$key)>{{ $label }}</option>@endforeach</select></div>
                    <div><label class="label">Tingkat Urgensi</label><select name="tingkat_urgensi" class="input">@foreach(['tinggi','sedang','rendah'] as $u)<option value="{{ $u }}" @selected($laporan->tingkat_urgensi===$u)>{{ ucfirst($u) }}</option>@endforeach</select></div>
                    <div><label class="label">Catatan Umum</label><textarea name="catatan" class="input" rows="4" placeholder="Tulis catatan untuk pelapor"></textarea></div>
                    <button class="btn-primary w-full"><i class="fa-solid fa-floppy-disk"></i> Simpan Status</button>
                </form>
            </section>

            <section class="panel">
                <h3 class="panel-title">Panggil Pelapor ke Kantor</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Kirim email/WhatsApp agar pelapor segera datang ke kantor saat kasus mulai diproses.</p>
                <form method="POST" action="{{ route('admin.laporan.panggil-kantor', $laporan) }}" class="mt-4 space-y-4">
                    @csrf
                    <div><label class="label">Catatan Tambahan</label><textarea name="catatan_panggilan" class="input" rows="3" placeholder="Contoh: Mohon datang Senin pukul 09.00 membawa identitas."></textarea></div>
                    <button class="btn-primary w-full"><i class="fa-solid fa-paper-plane"></i> Kirim Panggilan</button>
                </form>
            </section>

            <section class="panel">
                <h3 class="panel-title">Asesmen Awal</h3>
                <form method="POST" action="{{ route('admin.laporan.asesmen',$laporan) }}" class="mt-4 space-y-4">
                    @csrf
                    <div><label class="label">Kondisi Korban</label><textarea name="kondisi_korban" class="input" rows="3" required>{{ $laporan->asesmenAwal?->kondisi_korban }}</textarea></div>
                    <div><label class="label">Tingkat Risiko</label><select name="tingkat_risiko" class="input"><option @selected($laporan->asesmenAwal?->tingkat_risiko === 'tinggi')>tinggi</option><option @selected(($laporan->asesmenAwal?->tingkat_risiko ?? 'sedang') === 'sedang')>sedang</option><option @selected($laporan->asesmenAwal?->tingkat_risiko === 'rendah')>rendah</option></select></div>
                    <div><label class="label">Kebutuhan Korban</label><textarea name="kebutuhan_korban" class="input" rows="3">{{ $laporan->asesmenAwal?->kebutuhan_korban }}</textarea></div>
                    <label class="check-row"><input type="checkbox" name="pendampingan_hukum" value="1" @checked($laporan->asesmenAwal?->pendampingan_hukum)> Pendampingan hukum</label>
                    <label class="check-row"><input type="checkbox" name="pendampingan_psikologis" value="1" @checked($laporan->asesmenAwal?->pendampingan_psikologis)> Pendampingan psikologis</label>
                    <div><label class="label">Catatan Operator</label><textarea name="catatan_operator" class="input" rows="3">{{ $laporan->asesmenAwal?->catatan_operator }}</textarea></div>
                    <button class="btn-primary w-full"><i class="fa-solid fa-floppy-disk"></i> Simpan Asesmen</button>
                </form>
            </section>
        @elseif(auth()->user()->canGiveTindakLanjut())
            <section class="panel">
                <h3 class="panel-title">Catatan / Tindak Lanjut Kabid</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Berikan arahan pengawasan kepada petugas tanpa mengubah status resmi laporan.</p>
                <form method="POST" action="{{ route('admin.laporan.tindak-lanjut-kabid', $laporan) }}" class="mt-4 space-y-4">
                    @csrf
                    <div><label class="label">Catatan Tindak Lanjut</label><textarea name="catatan_tindak_lanjut" class="input" rows="4" required placeholder="Tulis arahan atau catatan untuk tindak lanjut petugas."></textarea></div>
                    <button class="btn-primary w-full"><i class="fa-solid fa-comment-dots"></i> Simpan Catatan</button>
                </form>
            </section>
        @else
            <section class="panel"><h3 class="panel-title">Mode Monitoring</h3><p class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-400">Akun pimpinan dapat memantau detail laporan tanpa mengubah status atau asesmen.</p></section>
        @endif
    </aside>
</div>
@endsection
