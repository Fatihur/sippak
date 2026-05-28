@extends('layouts.app')
@section('title','Login Petugas')
@section('content')
<section class="relative overflow-hidden bg-[#fff7ea] px-4 py-16 sm:px-6 lg:px-8">
    <div class="relative mx-auto grid max-w-5xl items-center gap-8 lg:grid-cols-[.95fr_1.05fr]">
        <div class="silap-slide-up"><p class="section-kicker">Area Petugas</p><h1 class="mt-3 text-5xl font-black tracking-tight text-slate-950">Login pengelola SILAPAK</h1><p class="mt-4 text-lg leading-8 text-slate-600">Masuk untuk memverifikasi laporan, memperbarui status, dan mengirim notifikasi kepada pelapor.</p></div>
        <div class="rounded-none bg-white p-7 shadow-2xl shadow-orange-200/40 ring-1 ring-orange-100 silap-slide-up">
            <form method="POST" action="{{ route('login.proses') }}" class="space-y-5">@csrf
                <div><label class="label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="input" required autofocus></div>
                <div><label class="label">Password</label><input type="password" name="password" class="input" required></div>
                <label class="flex items-center gap-2 text-sm font-semibold text-slate-600"><input type="checkbox" name="remember" class="rounded border-orange-300 text-orange-500"> Ingat saya</label>
                <button class="w-full rounded-none bg-orange-500 px-7 py-4 text-sm font-black uppercase tracking-wide text-white shadow-xl shadow-orange-500/25 transition hover:-translate-y-1 hover:bg-orange-600">Login</button>
                <a class="block text-center text-sm font-bold text-orange-700" href="{{ route('password.request') }}">Lupa password?</a>
            </form>
        </div>
    </div>
</section>
@endsection
