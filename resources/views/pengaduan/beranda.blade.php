@extends('layouts.app')
@section('title','SIPPAK - Layanan Pengaduan PPA')
@section('content')
<section class="grid lg:grid-cols-2 gap-10 items-center py-10">
    <div>
        <span class="badge bg-orange-100 text-orange-700">DP2KBP3A Kabupaten Sumbawa</span>
        <h1 class="mt-5 text-4xl md:text-6xl font-extrabold tracking-tight text-slate-950">Layanan Pengaduan Kekerasan Perempuan dan Anak</h1>
        <p class="mt-5 text-lg text-slate-600">SIPPAK membantu masyarakat melaporkan kasus secara aman, cepat, dan terpantau. Identitas korban/pelapor dijaga kerahasiaannya.</p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('pengaduan.buat') }}" class="btn-primary">Buat Pengaduan</a>
            <a href="{{ route('tracking.form') }}" class="btn-secondary">Cek Status Laporan</a>
        </div>
    </div>
    <div class="card bg-gradient-to-br from-blue-600 to-blue-800 text-white">
        <h2 class="text-2xl font-bold">Alur Layanan</h2>
        <div class="mt-6 space-y-4">
            @foreach(['Isi formulir pengaduan','Verifikasi OTP','Dapatkan nomor tiket','Pantau perkembangan laporan'] as $i => $alur)
                <div class="flex gap-4 items-start"><span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-white/15 font-bold">{{ $i+1 }}</span><p>{{ $alur }}</p></div>
            @endforeach
        </div>
    </div>
</section>
<section class="grid md:grid-cols-3 gap-5">
    <div class="card"><h3 class="font-bold text-lg">Aman</h3><p class="mt-2 text-slate-600">Data sensitif dibatasi untuk petugas berwenang.</p></div>
    <div class="card"><h3 class="font-bold text-lg">Terstruktur</h3><p class="mt-2 text-slate-600">Status dan riwayat penanganan tercatat.</p></div>
    <div class="card"><h3 class="font-bold text-lg">Responsif</h3><p class="mt-2 text-slate-600">Dapat diakses dari desktop maupun mobile.</p></div>
</section>
@endsection
