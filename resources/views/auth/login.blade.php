@extends('layouts.app')
@section('title','Login Petugas')
@section('content')
<div class="max-w-md mx-auto card">
    <h1 class="text-3xl font-bold">Login Petugas</h1><p class="mt-2 text-slate-600">Masuk untuk mengelola pengaduan SIPPAK.</p>
    <form method="POST" action="{{ route('login.proses') }}" class="mt-6 space-y-4">@csrf
        <div><label class="label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="input" required autofocus></div>
        <div><label class="label">Password</label><input type="password" name="password" class="input" required></div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember"> Ingat saya</label>
        <button class="btn-primary w-full">Login</button>
        <a class="block text-center text-sm text-blue-700" href="{{ route('password.request') }}">Lupa password?</a>
    </form>
</div>
@endsection
