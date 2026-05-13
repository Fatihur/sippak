@extends('layouts.app')
@section('title','Lupa Password')
@section('content')
<div class="max-w-md mx-auto card"><h1 class="text-3xl font-bold">Lupa Password</h1><p class="mt-2 text-slate-600">Masukkan email akun petugas.</p><form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">@csrf<div><label class="label">Email</label><input type="email" name="email" class="input" required></div><button class="btn-primary w-full">Kirim Link Reset</button></form></div>
@endsection
