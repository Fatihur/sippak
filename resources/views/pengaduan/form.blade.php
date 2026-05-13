@extends('layouts.app')
@section('title','Form Pengaduan')
@section('content')
<div class="max-w-4xl mx-auto card">
<h1 class="text-3xl font-bold mb-2">Form Pengaduan Online</h1><p class="text-slate-600 mb-8">Isi data dengan benar. Setelah submit, Anda akan diminta verifikasi OTP.</p>
<form method="POST" action="{{ route('pengaduan.simpan') }}" enctype="multipart/form-data" class="space-y-8">@csrf
    <section><h2 class="text-xl font-bold mb-4">Data Pelapor</h2><div class="grid md:grid-cols-2 gap-4">
        <div><label class="label">Nama Lengkap</label><input name="nama_pelapor" value="{{ old('nama_pelapor') }}" class="input" placeholder="Contoh: Siti Aminah" required></div>
        <div><label class="label">NIK</label><input name="nik_pelapor" value="{{ old('nik_pelapor') }}" class="input" placeholder="Contoh: 520401xxxxxxxxxx" inputmode="numeric" required></div>
        <div><label class="label">Jenis Kelamin</label><select name="jenis_kelamin_pelapor" class="input" required><option value="">Pilih jenis kelamin</option><option @selected(old('jenis_kelamin_pelapor') === 'Perempuan')>Perempuan</option><option @selected(old('jenis_kelamin_pelapor') === 'Laki-laki')>Laki-laki</option></select></div>
        <div><label class="label">Nomor WhatsApp</label><input name="nomor_whatsapp" value="{{ old('nomor_whatsapp') }}" class="input" placeholder="Contoh: 081234567890" inputmode="tel" required></div>
        <div><label class="label">Email</label><input type="email" name="email_pelapor" value="{{ old('email_pelapor') }}" class="input" placeholder="Contoh: nama@email.com"></div>
        <div><label class="label">Kecamatan</label><select name="kecamatan" class="input"><option value="">Pilih Kecamatan</option>@foreach($opsiKecamatan as $kecamatan)<option value="{{ $kecamatan }}" @selected(old('kecamatan') === $kecamatan)>{{ $kecamatan }}</option>@endforeach</select></div>
        <div class="md:col-span-2"><label class="label">Alamat</label><textarea name="alamat_pelapor" class="input" rows="3" placeholder="Contoh: Dusun/RT/RW, desa/kelurahan, kecamatan" required>{{ old('alamat_pelapor') }}</textarea></div>
    </div></section>
    <section><h2 class="text-xl font-bold mb-4">Data Korban</h2><div class="grid md:grid-cols-2 gap-4">
        <div><label class="label">Nama Korban</label><input name="nama_korban" value="{{ old('nama_korban') }}" class="input" placeholder="Contoh: Bunga Samaran" required></div>
        <div><label class="label">Umur Korban</label><input type="number" name="umur_korban" value="{{ old('umur_korban') }}" class="input" placeholder="Contoh: 12" min="0" max="120" required></div>
        <div><label class="label">Jenis Kelamin</label><select name="jenis_kelamin_korban" class="input" required><option value="">Pilih jenis kelamin</option><option @selected(old('jenis_kelamin_korban') === 'Perempuan')>Perempuan</option><option @selected(old('jenis_kelamin_korban') === 'Laki-laki')>Laki-laki</option></select></div>
        <div><label class="label">Hubungan Dengan Pelapor</label><input name="hubungan_dengan_pelapor" value="{{ old('hubungan_dengan_pelapor') }}" class="input" placeholder="Contoh: Anak, saudara, tetangga, teman" required></div>
    </div></section>
    <section><h2 class="text-xl font-bold mb-4">Data Kejadian</h2><div class="grid md:grid-cols-2 gap-4">
        <div><label class="label">Jenis Kekerasan</label><select name="jenis_kekerasan" class="input" required><option value="">Pilih jenis kekerasan</option>@foreach(['KDRT','Kekerasan seksual','Kekerasan fisik','Kekerasan verbal','Penelantaran anak','Eksploitasi anak','Penganiayaan','Pelecehan','Lainnya'] as $jenis)<option @selected(old('jenis_kekerasan') === $jenis)>{{ $jenis }}</option>@endforeach</select></div>
        <div><label class="label">Tanggal Kejadian</label><input type="date" name="tanggal_kejadian" value="{{ old('tanggal_kejadian') }}" class="input" required></div>
        <div class="md:col-span-2"><label class="label">Lokasi Kejadian</label><input name="lokasi_kejadian" value="{{ old('lokasi_kejadian') }}" class="input" placeholder="Contoh: Rumah korban, sekolah, tempat kerja, atau alamat kejadian" required></div>
        <div class="md:col-span-2"><label class="label">Kronologi Kejadian</label><textarea name="kronologi_kejadian" rows="5" class="input" placeholder="Ceritakan kronologi singkat: kapan kejadian terjadi, siapa yang terlibat, apa yang dialami korban, dan kondisi korban saat ini." required>{{ old('kronologi_kejadian') }}</textarea></div>
        <div class="md:col-span-2"><label class="label">Bukti Pendukung (foto/dokumen, maks 5MB per file)</label><input type="file" name="bukti[]" multiple class="input"></div>
    </div></section>
    <label class="flex gap-3 items-start"><input type="checkbox" name="persetujuan" value="1" required class="mt-1"><span>Saya menyetujui data digunakan untuk proses layanan pengaduan dan memahami kerahasiaan data akan dijaga.</span></label>
    <button class="btn-primary w-full md:w-auto">Kirim Pengaduan</button>
</form></div>
@endsection
