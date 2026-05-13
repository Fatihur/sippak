@extends('layouts.app')
@section('title','SIPPAK - Layanan Pengaduan PPA Kabupaten Sumbawa')
@section('content')
<section class="relative overflow-hidden bg-gradient-to-br from-brand-25 via-white to-warning-50">
    <div class="absolute -right-24 top-24 h-72 w-72 rounded-full bg-brand-100/70 blur-3xl"></div>
    <div class="absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-warning-100/70 blur-3xl"></div>
    <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-20">
        <div class="flex flex-col justify-center">
            <span class="inline-flex w-fit items-center gap-2 rounded-full bg-brand-50 px-4 py-2 text-sm font-medium text-brand-700"><i class="fa-solid fa-shield-heart"></i> Sistem Informasi Pengaduan Kekerasan Anak dan Perempuan</span>
            <h1 class="mt-6 max-w-3xl text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl">Layanan Pengaduan Kekerasan Perempuan dan Anak yang Aman, Cepat, dan Rahasia</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-600">Laporkan kasus kekerasan terhadap perempuan dan anak secara online dengan sistem yang aman, mudah, dan terintegrasi.</p>
            <div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('pengaduan.buat') }}" class="btn-primary">Buat Pengaduan</a><a href="{{ route('tracking.form') }}" class="btn-secondary">Tracking Laporan</a></div>

        </div>
        <div class="flex items-center justify-center lg:justify-end">
            <div class="relative w-full max-w-xl">
                <div class="absolute left-8 top-10 h-48 w-48 rounded-full bg-brand-100/70 blur-3xl"></div>
                <div class="absolute bottom-8 right-6 h-40 w-40 rounded-full bg-warning-100/80 blur-3xl"></div>
                <img src="{{ asset('hero.webp') }}" alt="Ilustrasi layanan pengaduan aman dan rahasia" class="relative z-10 mx-auto w-full max-w-lg object-contain " width="1388" height="1133" loading="eager">
            </div>
        </div>
    </div>
</section>



<section id="tentang" class="mx-auto grid max-w-7xl gap-8 px-4 py-14 sm:px-6 lg:grid-cols-2 lg:px-8">
    <div class="rounded-2xl bg-brand-500 p-8 text-white shadow-theme-lg"><h2 class="text-3xl font-bold">Tentang SIPPAK</h2><p class="mt-4 leading-7 text-brand-50">SIPPAK adalah layanan digital untuk memudahkan masyarakat melaporkan kasus kekerasan terhadap perempuan dan anak secara aman, terpusat, dan dapat dipantau.</p></div>
    <div class="grid gap-4 sm:grid-cols-2">
        @foreach([['Pelaporan online','fa-file-pen'],['Tracking laporan','fa-magnifying-glass-location'],['Keamanan data','fa-shield-halved'],['Pendampingan kasus','fa-handshake-angle']] as [$title,$icon])
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs"><i class="fa-solid {{ $icon }} text-brand-500"></i><h3 class="mt-3 font-bold text-gray-900">{{ $title }}</h3><p class="mt-2 text-sm text-gray-600">Layanan dirancang agar mudah digunakan dan tetap menjaga kerahasiaan data.</p></div>
        @endforeach
    </div>
</section>

<section id="cara-melapor" class="bg-white py-14">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8"><div class="text-center"><h2 class="text-3xl font-bold text-gray-900">Alur Pelaporan</h2><p class="mt-3 text-gray-600">Lima langkah sederhana untuk membuat dan memantau laporan.</p></div><div class="mt-10 grid gap-4 md:grid-cols-5">@foreach([['Buat Pengaduan','Isi formulir pengaduan secara online.'],['Verifikasi OTP','Validasi nomor WhatsApp atau email.'],['Laporan Diverifikasi','Operator memeriksa laporan yang masuk.'],['Proses Penanganan','Kasus ditindaklanjuti sesuai urgensi.'],['Tracking Laporan','Pantau perkembangan secara real-time.']] as $i => [$title,$desc])<div class="rounded-2xl border border-gray-200 bg-gray-50 p-5 text-center"><span class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-brand-500 font-bold text-white">{{ $i+1 }}</span><h3 class="mt-4 font-bold text-gray-900">{{ $title }}</h3><p class="mt-2 text-sm text-gray-600">{{ $desc }}</p></div>@endforeach</div></div>
</section>

<section id="jenis-kekerasan" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8"><h2 class="text-center text-3xl font-bold text-gray-900">Jenis Kekerasan</h2><div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">@foreach([['Kekerasan Fisik','Tindakan yang menyebabkan luka atau rasa sakit.'],['Kekerasan Seksual','Pemaksaan atau pelecehan bernuansa seksual.'],['Kekerasan Verbal','Ucapan yang merendahkan, mengancam, atau menghina.'],['KDRT','Kekerasan dalam lingkup rumah tangga.'],['Penelantaran Anak','Pengabaian kebutuhan dasar anak.'],['Eksploitasi Anak','Pemanfaatan anak untuk keuntungan pihak lain.'],['Pelecehan','Perilaku yang mengganggu martabat dan rasa aman.'],['Cyber Bullying','Perundungan melalui media digital.']] as [$title,$desc])<div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs"><h3 class="font-bold text-gray-900">{{ $title }}</h3><p class="mt-2 text-sm text-gray-600">{{ $desc }}</p></div>@endforeach</div></section>

<section id="statistik" class="bg-gray-900 py-14 text-white"><div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8"><div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between"><div><h2 class="text-3xl font-bold">Statistik Pengaduan</h2><p class="mt-2 text-gray-300">Data sensitif tidak ditampilkan.</p></div><a href="{{ route('statistik') }}" class="rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-900">Lihat Statistik</a></div><div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">@foreach([['Total Pengaduan',$totalPengaduan],['Kasus Diproses',$kasusDiproses],['Kasus Selesai',$kasusSelesai],['Kasus Bulan Ini',$kasusBulanIni]] as [$label,$value])<div class="rounded-2xl bg-white/10 p-6"><p class="text-sm text-gray-300">{{ $label }}</p><p class="mt-2 text-4xl font-bold">{{ number_format($value) }}</p></div>@endforeach</div></div></section>

<section id="tracking" class="mx-auto grid max-w-7xl gap-8 px-4 py-14 sm:px-6 lg:grid-cols-2 lg:px-8"><div><h2 class="text-3xl font-bold text-gray-900">Tracking Laporan</h2><p class="mt-3 text-gray-600">Masukkan nomor tiket dan nomor WhatsApp untuk melihat perkembangan laporan.</p></div><div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs"><form method="POST" action="{{ route('tracking.hasil') }}" class="space-y-4">@csrf<div><label class="label">Nomor Tiket</label><input name="nomor_tiket" class="input" placeholder="PPA-2026-0001" required></div><div><label class="label">Nomor WhatsApp</label><input name="nomor_whatsapp" class="input" placeholder="081234567890" required></div><button class="btn-primary w-full">Cek Status</button></form></div></section>

<section class="bg-brand-25 py-14"><div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8"><h2 class="text-3xl font-bold text-gray-900">Privasi dan Keamanan Anda Adalah Prioritas Kami</h2><div class="mt-8 grid gap-4 md:grid-cols-5">@foreach(['Identitas pelapor dirahasiakan','Data dilindungi sistem keamanan','Hanya petugas berwenang yang dapat mengakses','Sistem menggunakan validasi OTP','Bukti laporan tersimpan aman'] as $point)<div class="rounded-2xl bg-white p-5 shadow-theme-xs"><i class="fa-solid fa-lock text-brand-500"></i><p class="mt-3 text-sm font-medium text-gray-800">{{ $point }}</p></div>@endforeach</div></div></section>

<section id="faq" class="mx-auto max-w-4xl px-4 py-14 sm:px-6 lg:px-8"><h2 class="text-center text-3xl font-bold text-gray-900">FAQ</h2><div class="mt-8 space-y-3">@foreach(['Apakah laporan saya rahasia?'=>'Ya, identitas pelapor dan korban dibatasi hanya untuk petugas berwenang.','Apakah saya bisa melapor tanpa datang langsung?'=>'Bisa, laporan dapat dibuat secara online melalui formulir SIPPAK.','Bagaimana cara mengecek laporan?'=>'Gunakan nomor tiket dan nomor WhatsApp pada halaman tracking.','Apakah layanan gratis?'=>'Ya, layanan pengaduan ini gratis.','Bagaimana jika laporan darurat?'=>'Segera hubungi WhatsApp/call center atau layanan darurat terdekat.'] as $q=>$a)<details class="rounded-2xl border border-gray-200 bg-white p-5"><summary class="cursor-pointer font-bold text-gray-900">{{ $q }}</summary><p class="mt-3 text-gray-600">{{ $a }}</p></details>@endforeach</div></section>

<section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8"><div class="rounded-[2rem] bg-brand-500 p-8 text-center text-white shadow-theme-lg"><h2 class="text-3xl font-bold">Jangan Takut untuk Melapor</h2><p class="mt-3 text-brand-50">Kami siap membantu dan menjaga kerahasiaan identitas Anda.</p><div class="mt-6 flex justify-center gap-3"><a href="{{ route('pengaduan.buat') }}" class="rounded-lg bg-white px-5 py-3 font-medium text-brand-700">Buat Pengaduan</a><a href="https://wa.me/6287720296405" class="rounded-lg border border-white/30 px-5 py-3 font-medium text-white">Hubungi Petugas</a></div></div></section>

<section id="kontak" class="bg-white py-14"><div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-2 lg:px-8"><div><h2 class="text-3xl font-bold text-gray-900">Kontak & Informasi</h2><div class="mt-6 space-y-3 text-gray-700"><p><b>Alamat:</b> DP2KBP3A Kabupaten Sumbawa</p><p><b>Email:</b> ppidkabupatensumbawa@gmail.com</p><p><b>WhatsApp:</b> +62 877-2029-6405</p><p><b>Call Center:</b> 112</p><p><b>Jam Operasional:</b> Senin sampai Jumat, 08.00 sampai 16.00 WITA</p></div></div><div class="overflow-hidden rounded-2xl border border-gray-200"><iframe class="h-80 w-full" loading="lazy" src="https://www.google.com/maps?q=Kantor%20Bupati%20Sumbawa&output=embed"></iframe></div></div></section>

<footer class="bg-gray-950 py-10 text-gray-300"><div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8"><div class="flex items-center gap-3"><img src="{{ asset('logo-sumbawa.png') }}" class="h-12 w-12 object-contain" alt="Logo"><div><p class="font-bold text-white">SIPPAK</p><p class="text-sm">Kabupaten Sumbawa</p></div></div><p class="text-sm">© {{ date('Y') }} SIPPAK. All rights reserved.</p><div class="flex flex-wrap gap-4 text-sm"><a href="{{ route('tentang') }}">Tentang</a><a href="{{ route('faq') }}">FAQ</a><a href="#">Privacy Policy</a><a href="#">Terms</a></div></div></footer>
@endsection
