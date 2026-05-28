@extends('layouts.app')
@section('title','SILAPAK - Layanan Pengaduan PPA Kabupaten Sumbawa')
@section('content')
<section class="relative isolate min-h-[680px] overflow-hidden bg-slate-950">
    <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&w=1800&q=80" alt="Anak-anak dalam lingkungan aman" class="absolute inset-0 h-full w-full object-cover">
    <div class="absolute inset-0 bg-slate-950/65"></div>
    <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(15,23,42,.25)_0%,rgba(15,23,42,.78)_100%)]"></div>
    <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-[#fff7ea] to-transparent"></div>
    <div class="relative mx-auto flex min-h-[680px] max-w-5xl flex-col items-center justify-center px-4 py-24 text-center sm:px-6 lg:px-8">
        <div class="silap-slide-up">
            <span class="inline-flex items-center gap-2 rounded-none border border-white/20 bg-white/10 px-4 py-2 text-sm font-black uppercase tracking-[0.18em] text-orange-200 shadow-theme-sm backdrop-blur"><i class="fa-solid fa-shield-heart"></i> Layanan Pengaduan SILAPAK</span>
            <h1 class="mx-auto mt-7 max-w-5xl text-5xl font-black leading-[0.95] tracking-tight text-white sm:text-6xl lg:text-7xl">Layanan Pengaduan untuk Perempuan dan Anak</h1>
            <p class="mx-auto mt-6 max-w-3xl text-lg leading-8 text-slate-100 sm:text-xl">Masyarakat bisa melaporkan kejadian kekerasan di sekitar mereka melalui formulir online. Cepat, aman, rahasia, dan ditangani petugas berwenang.</p>
            <div class="mt-9 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('pengaduan.buat') }}" class="rounded-none bg-orange-500 px-8 py-4 text-sm font-black uppercase tracking-wide text-white shadow-xl shadow-orange-500/25 transition hover:-translate-y-1 hover:bg-orange-600"><i class="fa-solid fa-paper-plane mr-2"></i>Lapor Sekarang!</a>
                <a href="{{ route('tracking.form') }}" class="rounded-none border border-white/25 bg-white/95 px-8 py-4 text-sm font-black text-slate-900 shadow-theme-sm transition hover:-translate-y-1 hover:bg-orange-50"><i class="fa-solid fa-magnifying-glass-location mr-2 text-orange-500"></i>Tracking Laporan</a>
            </div>
        </div>
    </div>
</section>

<section id="cara-lapor" class="relative overflow-hidden bg-white py-20">
    <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&w=700&q=80" alt="Anak-anak dalam lingkungan aman" class="pointer-events-none absolute -right-16 top-10 hidden h-60 w-60 rotate-6 rounded-none object-cover opacity-20 shadow-2xl lg:block">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[.9fr_1.1fr] lg:px-8">
        <div class="silap-reveal">
            <p class="section-kicker">Cara Menggunakan</p>
            <h2 class="mt-3 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Layanan SILAPAK</h2>
            <p class="mt-5 text-lg leading-8 text-slate-600">Anda bisa mengirim laporan melalui WhatsApp, call center, atau form online dengan menyertakan informasi singkat tentang insiden kekerasan yang dialami atau diketahui.</p>
            <div class="mt-7"><a href="{{ route('pengaduan.buat') }}" class="rounded-none bg-slate-950 px-6 py-3 text-sm font-black uppercase tracking-wide text-white transition hover:-translate-y-1 hover:bg-orange-600">Laporkan Sekarang.!!</a></div>
        </div>
        <div class="grid gap-5 md:grid-cols-3">
            @foreach([['fa-phone-volume','Hotline / Call Center','Hubungi layanan darurat daerah untuk melaporkan kejadian kekerasan.'],['fa-brands fa-whatsapp','WhatsApp Petugas','Kirim pesan via WhatsApp untuk mendapat arahan awal dan bantuan.'],['fa-file-lines','Form Pengaduan Online','Form selalu tersedia, jadi Anda bisa melapor kapan saja.']] as [$icon,$title,$desc])
                <article class="service-tile silap-reveal" style="animation-delay: {{ $loop->index * 120 }}ms">
                    <span class="service-icon"><i class="{{ $icon }}"></i></span>
                    <h3>{{ $title }}</h3>
                    <p>{{ $desc }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section id="kapan-lapor" class="overflow-hidden bg-[#fff7ea] py-20">
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div class="relative order-2 lg:order-1">
            <div class="absolute inset-8 rounded-full bg-orange-200/50 blur-3xl"></div>
            <img src="{{ asset('hero-transparent.png') }}" alt="Ilustrasi korban mendapat pendampingan" class="relative mx-auto max-h-[520px] object-contain silap-float">
        </div>
        <div class="order-1 lg:order-2 silap-reveal">
            <p class="section-kicker">Tips</p>
            <h2 class="mt-3 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Kapan Anda Harus Menghubungi SILAPAK?</h2>
            <p class="mt-5 text-lg leading-8 text-slate-600">Segera hubungi kami jika Anda melihat, mengalami, atau mengetahui kekerasan fisik, psikis, seksual, penelantaran, eksploitasi, atau ancaman terhadap perempuan dan anak.</p>
            <div class="mt-8 space-y-4">
                @foreach(['Ada ancaman keselamatan korban atau anak.','Korban membutuhkan pendampingan psikologis, kesehatan, atau hukum.','Anda memiliki informasi atau bukti kekerasan di sekitar Anda.'] as $point)
                    <div class="flex gap-4 rounded-none bg-white p-5 shadow-theme-sm ring-1 ring-orange-100"><span class="grid h-10 w-10 shrink-0 place-items-center rounded-none bg-orange-100 text-orange-600"><i class="fa-solid fa-check"></i></span><p class="font-bold leading-7 text-slate-700">{{ $point }}</p></div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section id="mengapa-lapor" class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl silap-reveal"><p class="section-kicker">Mengapa Harus Lapor</p><h2 class="mt-3 text-4xl font-black text-slate-950 sm:text-5xl">Mengapa Harus Menghubungi SILAPAK?</h2></div>
        <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
            @foreach([['Tindakan Cepat','Petugas merespons laporan dan memberi arahan sesuai kondisi korban.'],['Pendampingan Korban','Korban diarahkan untuk psikologis, kesehatan, visum, atau bantuan lanjutan.'],['Keamanan Terjamin','Petugas membantu memastikan korban berada pada lingkungan yang lebih aman.'],['Dukungan Hukum','Korban mendapat arahan saat berurusan dengan pihak berwenang.']] as [$title,$desc])
                <article class="reason-card silap-reveal"><span>0{{ $loop->iteration }}</span><h3>{{ $title }}</h3><p>{{ $desc }}</p></article>
            @endforeach
        </div>
    </div>
</section>

<section class="relative overflow-hidden bg-slate-950 py-20 text-white">
    <div class="absolute -left-24 top-10 h-72 w-72 rounded-none bg-orange-500/25 blur-3xl"></div>
    <div class="absolute -right-24 bottom-0 h-72 w-72 rounded-full bg-blue-500/20 blur-3xl"></div>
    <div class="relative mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[1fr_.8fr] lg:px-8">
        <div class="silap-reveal"><p class="section-kicker text-orange-300">Langkah Penting</p><h2 class="mt-3 text-4xl font-black sm:text-5xl">Jika Anda menjadi korban tindak kekerasan</h2><p class="mt-5 max-w-2xl text-lg leading-8 text-slate-300">Kekerasan adalah tanggung jawab pelaku, bukan Anda. Jangan takut untuk mencari bantuan dari keluarga, kerabat terpercaya, atau petugas.</p></div>
        <div class="space-y-4">
            @foreach(['Kumpulkan bukti jika memungkinkan, seperti rekam medis atau dokumentasi luka.','Hubungi keluarga atau kerabat yang dapat dipercaya.','Datang ke UPTD PPA, P2TP2A, atau buat laporan online melalui SILAPAK.'] as $step)
                <div class="rounded-none bg-white/10 p-5 ring-1 ring-white/10 backdrop-blur"><p class="font-bold leading-7 text-white">{{ $step }}</p></div>
            @endforeach
        </div>
    </div>
</section>

<section id="statistik" class="bg-orange-500 py-16 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([['Total Pengaduan',$totalPengaduan],['Kasus Diproses',$kasusDiproses],['Kasus Selesai',$kasusSelesai],['Kasus Bulan Ini',$kasusBulanIni]] as [$label,$value])
                <div class="rounded-none bg-white/15 p-6 ring-1 ring-white/20 backdrop-blur transition hover:-translate-y-1"><p class="text-sm font-bold uppercase tracking-widest text-orange-100">{{ $label }}</p><p class="mt-3 text-5xl font-black">{{ number_format($value) }}</p></div>
            @endforeach
        </div>
    </div>
</section>

<section id="partisipasi" class="bg-[#fff7ea] py-20">
    <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div class="silap-reveal"><p class="section-kicker">Ayo Ikut Berpartisipasi!</p><h2 class="mt-3 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Jadilah Bagian dari Gerakan Perlindungan Anak!</h2><p class="mt-5 text-lg leading-8 text-slate-600">Bantu memperluas jaringan pelaporan tindak kekerasan. Bagikan informasi SILAPAK di lingkungan, sekolah, organisasi, dan kanal digital Anda.</p></div>
        <div class="overflow-hidden rounded-none bg-white shadow-xl shadow-orange-200/40 ring-1 ring-orange-100"><img src="https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&w=900&q=80" alt="Anak-anak belajar bersama" class="h-56 w-full object-cover"><div class="p-8"><h3 class="text-2xl font-black text-slate-950">Hubungi Kami Sekarang!</h3><p class="mt-4 leading-7 text-slate-600">Jika Anda, keluarga Anda, atau seseorang di sekitar Anda menjadi korban kekerasan, jangan ragu untuk melapor.</p><div class="mt-7 flex flex-wrap gap-3"><a href="{{ route('pengaduan.buat') }}" class="rounded-none bg-orange-500 px-6 py-3 font-black text-white shadow-lg shadow-orange-500/25">Lapor Sekarang</a><a href="https://wa.me/6287720296405" class="rounded-none border border-orange-200 px-6 py-3 font-black text-orange-700">WhatsApp Petugas</a></div></div></div>
    </div>
</section>

<section id="faq" class="bg-white py-20">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8"><div class="text-center"><p class="section-kicker">FAQ</p><h2 class="mt-3 text-4xl font-black text-slate-950">Pertanyaan Umum</h2></div><div class="mt-10 space-y-3">@foreach(['Apakah laporan saya rahasia?'=>'Ya, identitas pelapor dan korban dibatasi hanya untuk petugas berwenang.','Apakah saya bisa melapor tanpa datang langsung?'=>'Bisa, laporan dapat dibuat secara online melalui formulir SILAPAK.','Bagaimana cara mengecek laporan?'=>'Gunakan nomor tiket dan nomor WhatsApp pada halaman tracking.','Apakah layanan gratis?'=>'Ya, layanan pengaduan ini gratis.','Bagaimana jika laporan darurat?'=>'Segera hubungi WhatsApp/call center atau layanan darurat terdekat.'] as $q=>$a)<details class="group rounded-none border border-orange-100 bg-[#fffaf3] p-5 shadow-theme-xs"><summary class="cursor-pointer list-none font-black text-slate-950">{{ $q }} <i class="fa-solid fa-chevron-down float-right mt-1 text-orange-500 transition group-open:rotate-180"></i></summary><p class="mt-3 leading-7 text-slate-600">{{ $a }}</p></details>@endforeach</div></div>
</section>

<section id="kontak" class="bg-slate-950 py-16 text-white"><div class="mx-auto flex max-w-7xl flex-col gap-8 px-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8"><div><h2 class="text-4xl font-black">Mari Kita Membuat Perbedaan dalam Kehidupan Orang Lain</h2><p class="mt-3 text-slate-300">DP2KBP3A Kabupaten Sumbawa, WhatsApp +62 877-2029-6405, Senin sampai Jumat 08.00 sampai 16.00 WITA.</p></div><a href="{{ route('pengaduan.buat') }}" class="shrink-0 rounded-none bg-white px-7 py-4 font-black text-slate-950 transition hover:-translate-y-1">Buat Pengaduan</a></div></section>

<footer class="bg-[#0b1020] py-10 text-slate-300"><div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8"><div class="flex items-center gap-3"><img src="{{ asset('logo-sumbawa.png') }}" class="h-12 w-12 rounded-none bg-white object-contain p-1" alt="Logo"><div><p class="font-black text-white">SILAPAK</p><p class="text-sm">Kabupaten Sumbawa</p></div></div><p class="text-sm">© {{ date('Y') }} SILAPAK. All rights reserved.</p><div class="flex flex-wrap items-center gap-4 text-sm font-bold"><a href="{{ route('tentang') }}">Tentang</a><a href="{{ route('faq') }}">FAQ</a><a href="{{ route('statistik') }}">Statistik</a><a href="{{ route('login') }}" class="rounded-none border border-white/15 bg-white/10 px-4 py-2 text-white transition hover:-translate-y-0.5 hover:bg-orange-500"><i class="fa-solid fa-user-shield mr-2"></i>Admin</a></div></div></footer>
@endsection
