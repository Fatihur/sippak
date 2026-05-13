@extends('layouts.app')
@section('title','Verifikasi OTP')
@section('content')
<div class="max-w-lg mx-auto card">
    <h1 class="text-3xl font-bold">Verifikasi OTP</h1>
    <p class="mt-2 text-slate-600">Masukkan kode OTP untuk menyelesaikan pengiriman pengaduan.</p>
    @if($otpDemo)<div class="mt-4 rounded-2xl bg-orange-50 p-4 text-orange-800 ring-1 ring-orange-200">Mode demo lokal: OTP Anda <strong>{{ $otpDemo }}</strong></div>@endif
    <form class="mt-6 space-y-4" method="POST" action="{{ route('pengaduan.verifikasi-otp', $pengaduan) }}">@csrf
        <div><label class="label">Kode OTP</label><input name="otp" class="input text-center text-2xl tracking-widest" maxlength="6" required autofocus></div>
        <button class="btn-primary w-full">Verifikasi</button>
    </form>
</div>
@endsection
