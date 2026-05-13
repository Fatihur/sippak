@extends('layouts.app')
@section('title','Reset Password')
@section('content')
<div class="max-w-md mx-auto card"><h1 class="text-3xl font-bold">Reset Password</h1><form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">@csrf<input type="hidden" name="token" value="{{ $token }}"><div><label class="label">Email</label><input type="email" name="email" value="{{ $email }}" class="input" required></div><div><label class="label">Password Baru</label><input type="password" name="password" class="input" required></div><div><label class="label">Konfirmasi Password</label><input type="password" name="password_confirmation" class="input" required></div><button class="btn-primary w-full">Reset Password</button></form></div>
@endsection
