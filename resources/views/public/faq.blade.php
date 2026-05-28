@extends('layouts.app')
@section('title','FAQ SILAPAK')
@section('content')
<section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
    <h1 class="text-center text-4xl font-black text-slate-950">Pertanyaan Umum</h1>
    <div class="mt-10 space-y-4">
        @foreach([
            'Apakah laporan saya rahasia?' => 'Ya. Identitas pelapor dan korban dirahasiakan dan hanya dapat diakses petugas berwenang.',
            'Apakah saya bisa melapor tanpa datang langsung?' => 'Bisa. Pengaduan dapat dibuat secara online melalui formulir SILAPAK.',
            'Bagaimana cara mengecek laporan?' => 'Gunakan nomor tiket dan nomor WhatsApp pada halaman tracking laporan.',
            'Berapa lama laporan diproses?' => 'Waktu proses bergantung pada kelengkapan data dan tingkat urgensi kasus.',
            'Apakah saya harus memiliki bukti?' => 'Bukti sangat membantu, tetapi Anda tetap dapat menyampaikan laporan dan menjelaskan kronologi.',
            'Apakah layanan gratis?' => 'Ya. Layanan pengaduan SILAPAK tidak dipungut biaya.',
            'Bagaimana jika laporan darurat?' => 'Segera hubungi layanan darurat, call center, atau WhatsApp petugas.'
        ] as $q => $a)
            <details class="rounded-none border border-slate-200 bg-white p-5 shadow-sm"><summary class="cursor-pointer font-black text-slate-900">{{ $q }}</summary><p class="mt-3 text-slate-600">{{ $a }}</p></details>
        @endforeach
    </div>
</section>
@endsection
