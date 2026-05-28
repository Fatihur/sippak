@extends('layouts.app')
@section('title','Form Pengaduan Online')
@section('content')
<section class="relative overflow-hidden bg-[#fff7ea] py-12 sm:py-16">
    <div class="pointer-events-none absolute -right-24 top-10 h-72 w-72 rounded-full bg-orange-300/25 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-blue-300/15 blur-3xl"></div>
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[.82fr_1.18fr]">
            <aside class="lg:sticky lg:top-28 lg:self-start">
                <div class="overflow-hidden rounded-none bg-slate-950 text-white shadow-2xl shadow-orange-200/30">
                    <img src="https://images.pexels.com/photos/7176229/pexels-photo-7176229.jpeg?auto=compress&cs=tinysrgb&w=900" alt="Konsultasi dan pendampingan korban" class="h-48 w-full object-cover opacity-90">
                    <div class="p-7">
                    <p class="section-kicker text-orange-300">Form Pengaduan</p>
                    <h1 class="mt-3 text-4xl font-black leading-tight">Ceritakan kejadian dengan aman dan rahasia</h1>
                    <p class="mt-4 leading-7 text-slate-300">Isi data yang Anda ketahui. Setelah dikirim, sistem akan meminta verifikasi OTP untuk memastikan laporan benar berasal dari pelapor.</p>
                    <div class="mt-7 space-y-4">
                        @foreach([['fa-lock','Data pelapor dan korban dijaga kerahasiaannya.'],['fa-clock','Form dapat diisi kapan saja selama 24 jam.'],['fa-paper-plane','Petugas akan memeriksa laporan yang telah diverifikasi.']] as [$icon,$text])
                            <div class="flex gap-3 rounded-none bg-white/10 p-4 ring-1 ring-white/10"><span class="grid h-10 w-10 shrink-0 place-items-center rounded-none bg-orange-500 text-white"><i class="fa-solid {{ $icon }}"></i></span><p class="text-sm font-semibold leading-6 text-slate-100">{{ $text }}</p></div>
                        @endforeach
                    </div>
                    <a href="{{ route('tracking.form') }}" class="mt-7 inline-flex rounded-none bg-white px-5 py-3 text-sm font-black text-slate-950 transition hover:-translate-y-1">Sudah punya tiket? Tracking</a>
                    </div>
                </div>
            </aside>

            <form method="POST" action="{{ route('pengaduan.simpan') }}" enctype="multipart/form-data" class="space-y-6 silap-slide-up">@csrf
                <section class="form-panel">
                    <div class="form-section-title"><span>01</span><div><h2>Data Pelapor</h2><p>Kontak ini dipakai untuk OTP, notifikasi, dan tracking laporan.</p></div></div>
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div><label class="label">Nama Lengkap</label><input name="nama_pelapor" value="{{ old('nama_pelapor') }}" class="input" placeholder="Contoh: Siti Aminah" required></div>
                        <div><label class="label">NIK</label><input type="number" name="nik_pelapor" value="{{ old('nik_pelapor') }}" class="input" placeholder="Contoh: 520401xxxxxxxxxx" inputmode="numeric" required></div>
                        <div><label class="label">Jenis Kelamin</label><select name="jenis_kelamin_pelapor" class="input" required><option value="">Pilih jenis kelamin</option><option @selected(old('jenis_kelamin_pelapor') === 'Perempuan')>Perempuan</option><option @selected(old('jenis_kelamin_pelapor') === 'Laki-laki')>Laki-laki</option></select></div>
                        <div><label class="label">Nomor WhatsApp</label><input type="number" name="nomor_whatsapp" value="{{ old('nomor_whatsapp') }}" class="input" placeholder="Contoh: 081234567890" inputmode="numeric" required></div>
                        <div><label class="label">Email</label><input type="email" name="email_pelapor" value="{{ old('email_pelapor') }}" class="input" placeholder="Contoh: nama@email.com"></div>
                        <div><label class="label">Kecamatan</label><select name="kecamatan" class="input"><option value="">Pilih Kecamatan</option>@foreach($opsiKecamatan as $kecamatan)<option value="{{ $kecamatan }}" @selected(old('kecamatan') === $kecamatan)>{{ $kecamatan }}</option>@endforeach</select></div>
                        <div class="md:col-span-2"><label class="label">Alamat</label><textarea name="alamat_pelapor" class="input" rows="3" placeholder="Dusun/RT/RW, desa/kelurahan, kecamatan" required>{{ old('alamat_pelapor') }}</textarea></div>
                    </div>
                </section>

                <section class="form-panel">
                    <div class="form-section-title"><span>02</span><div><h2>Data Korban</h2><p>Jika korban perlu disamarkan, tulis nama panggilan atau identitas yang aman.</p></div></div>
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div><label class="label">Nama Korban</label><input name="nama_korban" value="{{ old('nama_korban') }}" class="input" placeholder="Contoh: Bunga Samaran" required></div>
                        <div><label class="label">Umur Korban</label><input type="number" name="umur_korban" value="{{ old('umur_korban') }}" class="input" placeholder="Contoh: 12" min="0" max="120" required></div>
                        <div><label class="label">Jenis Kelamin</label><select name="jenis_kelamin_korban" class="input" required><option value="">Pilih jenis kelamin</option><option @selected(old('jenis_kelamin_korban') === 'Perempuan')>Perempuan</option><option @selected(old('jenis_kelamin_korban') === 'Laki-laki')>Laki-laki</option></select></div>
                        <div><label class="label">Hubungan Dengan Pelapor</label><input name="hubungan_dengan_pelapor" value="{{ old('hubungan_dengan_pelapor') }}" class="input" placeholder="Anak, saudara, tetangga, teman" required></div>
                    </div>
                </section>

                <section class="form-panel">
                    <div class="form-section-title"><span>03</span><div><h2>Data Kejadian</h2><p>Tuliskan kronologi sesuai yang dialami atau diketahui.</p></div></div>
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div><label class="label">Jenis Kekerasan</label><select name="jenis_kekerasan" class="input" required><option value="">Pilih jenis kekerasan</option>@foreach(['KDRT','Kekerasan seksual','Kekerasan fisik','Kekerasan verbal','Penelantaran anak','Eksploitasi anak','Penganiayaan','Pelecehan','Lainnya'] as $jenis)<option @selected(old('jenis_kekerasan') === $jenis)>{{ $jenis }}</option>@endforeach</select></div>
                        <div><label class="label">Tanggal Kejadian</label><input type="date" name="tanggal_kejadian" value="{{ old('tanggal_kejadian') }}" class="input" required></div>
                        <div class="md:col-span-2"><label class="label">Lokasi Kejadian</label><input name="lokasi_kejadian" value="{{ old('lokasi_kejadian') }}" class="input" placeholder="Rumah korban, sekolah, tempat kerja, atau alamat kejadian" required></div>
                        <div class="md:col-span-2"><label class="label">Kronologi Kejadian</label><textarea name="kronologi_kejadian" rows="6" class="input" placeholder="Ceritakan kapan kejadian terjadi, siapa yang terlibat, apa yang dialami korban, dan kondisi korban saat ini." required>{{ old('kronologi_kejadian') }}</textarea></div>
                        <div class="md:col-span-2" x-data="filePreviewer()">
                            <label class="label">Bukti Pendukung</label>
                            <input type="file" name="bukti[]" multiple class="input bg-white" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,image/jpeg,image/png,application/pdf" @change="handleFiles($event)">
                            <p class="mt-2 text-xs text-slate-500">Format jpg, png, pdf, doc, docx. Maksimal 5MB per file.</p>
                            <div x-show="files.length" x-cloak class="mt-4 grid gap-3 sm:grid-cols-2">
                                <template x-for="(file, index) in files" :key="file.id">
                                    <div class="flex gap-3 border border-orange-100 bg-[#fffaf3] p-3">
                                        <template x-if="file.isImage">
                                            <img :src="file.url" :alt="file.name" class="h-20 w-20 shrink-0 object-cover">
                                        </template>
                                        <template x-if="!file.isImage">
                                            <div class="grid h-20 w-20 shrink-0 place-items-center bg-white text-2xl text-orange-500 ring-1 ring-orange-100"><i class="fa-solid" :class="file.icon"></i></div>
                                        </template>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-black text-slate-950" x-text="file.name"></p>
                                            <p class="mt-1 text-xs font-semibold text-slate-500" x-text="file.size"></p>
                                            <a x-show="file.url" :href="file.url" target="_blank" class="mt-2 inline-flex text-xs font-black text-orange-600">Preview</a>
                                            <button type="button" class="ml-3 text-xs font-black text-red-600" @click="remove(index)">Hapus</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-none bg-white p-6 shadow-theme-sm ring-1 ring-orange-100">
                    <label class="flex gap-4"><input type="checkbox" name="persetujuan" value="1" required class="mt-1 h-5 w-5 rounded border-orange-300 text-orange-500"><span class="text-sm font-semibold leading-7 text-slate-700">Saya menyetujui data digunakan untuk proses layanan pengaduan dan memahami kerahasiaan data akan dijaga.</span></label>
                    <div class="mt-6 flex flex-wrap gap-3"><button class="rounded-none bg-orange-500 px-7 py-4 text-sm font-black uppercase tracking-wide text-white shadow-xl shadow-orange-500/25 transition hover:-translate-y-1 hover:bg-orange-600"><i class="fa-solid fa-paper-plane mr-2"></i>Kirim Pengaduan</button><a href="{{ route('beranda') }}" class="rounded-none bg-orange-50 px-7 py-4 text-sm font-black text-orange-700 transition hover:-translate-y-1 hover:bg-orange-100">Kembali</a></div>
                </section>
            </form>
        </div>
    </div>
</section>
@endsection
