@extends('layouts.admin')
@section('title','Edit Tiket '.$laporan->nomor_tiket)
@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Edit Tiket {{ $laporan->nomor_tiket }}</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Perbarui data pelapor, korban, dan kronologi laporan.</p>
    </div>
    <a href="{{ route('admin.laporan.show', $laporan) }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>

<form method="POST" action="{{ route('admin.laporan.update', $laporan) }}" class="space-y-6">
    @csrf @method('PUT')
    <section class="panel">
        <h3 class="panel-title">Data Pelapor</h3>
        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <div><label class="label">Nama Pelapor</label><input class="input" name="nama_pelapor" value="{{ old('nama_pelapor', $laporan->nama_pelapor) }}" required></div>
            <div><label class="label">NIK Pelapor</label><input class="input" name="nik_pelapor" value="{{ old('nik_pelapor', $laporan->nik_pelapor) }}" required></div>
            <div><label class="label">Jenis Kelamin Pelapor</label><select class="input" name="jenis_kelamin_pelapor" required><option @selected(old('jenis_kelamin_pelapor', $laporan->jenis_kelamin_pelapor)==='Perempuan')>Perempuan</option><option @selected(old('jenis_kelamin_pelapor', $laporan->jenis_kelamin_pelapor)==='Laki-laki')>Laki-laki</option></select></div>
            <div><label class="label">Nomor WhatsApp</label><input class="input" name="nomor_whatsapp" value="{{ old('nomor_whatsapp', $laporan->nomor_whatsapp) }}" required></div>
            <div><label class="label">Email</label><input type="email" class="input" name="email_pelapor" value="{{ old('email_pelapor', $laporan->email_pelapor) }}"></div>
            <div><label class="label">Kecamatan</label><select class="input" name="kecamatan"><option value="">Pilih Kecamatan</option>@foreach($opsiKecamatan as $value=>$label)<option value="{{ $value }}" @selected(old('kecamatan', $laporan->kecamatan)===$value)>{{ $label }}</option>@endforeach</select></div>
            <div class="sm:col-span-2"><label class="label">Alamat Pelapor</label><textarea class="input" name="alamat_pelapor" rows="3" required>{{ old('alamat_pelapor', $laporan->alamat_pelapor) }}</textarea></div>
        </div>
    </section>

    <section class="panel">
        <h3 class="panel-title">Data Korban & Kejadian</h3>
        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <div><label class="label">Nama Korban</label><input class="input" name="nama_korban" value="{{ old('nama_korban', $laporan->nama_korban) }}" required></div>
            <div><label class="label">Umur Korban</label><input type="number" min="0" max="120" class="input" name="umur_korban" value="{{ old('umur_korban', $laporan->umur_korban) }}" required></div>
            <div><label class="label">Jenis Kelamin Korban</label><select class="input" name="jenis_kelamin_korban" required><option @selected(old('jenis_kelamin_korban', $laporan->jenis_kelamin_korban)==='Perempuan')>Perempuan</option><option @selected(old('jenis_kelamin_korban', $laporan->jenis_kelamin_korban)==='Laki-laki')>Laki-laki</option></select></div>
            <div><label class="label">Hubungan Dengan Pelapor</label><input class="input" name="hubungan_dengan_pelapor" value="{{ old('hubungan_dengan_pelapor', $laporan->hubungan_dengan_pelapor) }}" required></div>
            <div><label class="label">Jenis Kekerasan</label><select class="input" name="jenis_kekerasan" required>@foreach($opsiJenisKasus as $value=>$label)<option value="{{ $value }}" @selected(old('jenis_kekerasan', $laporan->jenis_kekerasan)===$value)>{{ $label }}</option>@endforeach</select></div>
            <div><label class="label">Tanggal Kejadian</label><input type="date" class="input" name="tanggal_kejadian" value="{{ old('tanggal_kejadian', $laporan->tanggal_kejadian?->toDateString()) }}" required></div>
            <div class="sm:col-span-2"><label class="label">Lokasi Kejadian</label><input class="input" name="lokasi_kejadian" value="{{ old('lokasi_kejadian', $laporan->lokasi_kejadian) }}" required></div>
            <div class="sm:col-span-2"><label class="label">Kronologi Kejadian</label><textarea class="input" name="kronologi_kejadian" rows="6" required>{{ old('kronologi_kejadian', $laporan->kronologi_kejadian) }}</textarea></div>
        </div>
    </section>

    <div class="flex flex-wrap justify-end gap-3">
        <a href="{{ route('admin.laporan.show', $laporan) }}" class="btn-secondary">Batal</a>
        <button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
    </div>
</form>
@endsection
