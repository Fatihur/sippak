@extends('layouts.app')
@section('title','Verifikasi OTP')
@section('content')
<section class="relative overflow-hidden bg-[#fff7ea] px-4 py-16 sm:px-6 lg:px-8">
    <div class="absolute left-1/2 top-10 h-80 w-80 -translate-x-1/2 rounded-full bg-orange-300/20 blur-3xl"></div>
    <div class="relative mx-auto grid max-w-5xl items-center gap-8 lg:grid-cols-[.9fr_1.1fr]">
        <div class="silap-slide-up">
            <p class="section-kicker">Verifikasi Laporan</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Masukkan kode OTP</h1>
            <p class="mt-4 text-lg leading-8 text-slate-600">Kode OTP memastikan laporan dikirim oleh pelapor yang dapat dihubungi. Jangan bagikan kode kepada orang lain.</p>
        </div>
        <div class="rounded-none bg-white p-7 shadow-2xl shadow-orange-200/40 ring-1 ring-orange-100 silap-slide-up">
            <div class="mx-auto grid h-16 w-16 place-items-center rounded-none bg-orange-100 text-3xl text-orange-600"><i class="fa-solid fa-key"></i></div>
            @if($otpDemo)<div class="mt-5 rounded-none bg-orange-50 p-4 text-center text-orange-800 ring-1 ring-orange-200">Kode OTP Anda <strong>{{ $otpDemo }}</strong></div>@endif
            <form class="mt-6 space-y-5" method="POST" action="{{ route('pengaduan.verifikasi-otp', $pengaduan) }}">@csrf
                <div><label class="label text-center">Kode OTP</label><input name="otp" class="input h-16 text-center text-3xl font-black tracking-[0.5em]" maxlength="6" inputmode="numeric" required autofocus></div>
                <button class="w-full rounded-none bg-orange-500 px-7 py-4 text-sm font-black uppercase tracking-wide text-white shadow-xl shadow-orange-500/25 transition hover:-translate-y-1 hover:bg-orange-600">Verifikasi</button>
            </form>
        </div>
    </div>
</section>
@endsection
