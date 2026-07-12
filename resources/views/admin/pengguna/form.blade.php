@extends('layouts.admin')
@section('title',$user->exists ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('content')
<section class="panel max-w-3xl">
    <div class="panel-header">
        <div>
            <h2 class="panel-title">{{ $user->exists ? 'Edit Data Pengguna' : 'Tambah Pengguna Baru' }}</h2>
            <p class="panel-subtitle">Atur akun, status aktif, dan role akses sistem.</p>
        </div>
    </div>
    <form method="POST" action="{{ $user->exists ? route('admin.pengguna.update',$user) : route('admin.pengguna.store') }}" class="mt-6 grid gap-4 sm:grid-cols-2">
        @csrf @if($user->exists) @method('PUT') @endif
        <div><label class="label">Nama</label><input name="name" value="{{ old('name',$user->name) }}" class="input" placeholder="Nama lengkap" required></div>
        <div><label class="label">Email</label><input type="email" name="email" value="{{ old('email',$user->email) }}" class="input" placeholder="nama@email.com" required></div>
        <div><label class="label">Role</label><select name="role" class="input">@foreach(\App\Models\User::opsiRole() as $key=>$label)<option value="{{ $key }}" @selected(old('role',$user->role ?: \App\Models\User::ROLE_OPERATOR)===$key)>{{ $label }}</option>@endforeach</select></div>
        <div><label class="label">NIP</label><input name="nip" value="{{ old('nip',$user->nip) }}" class="input" placeholder="Nomor Induk Pegawai"></div>
        <div><label class="label">Jabatan</label><input name="jabatan" value="{{ old('jabatan',$user->jabatan) }}" class="input" placeholder="Contoh: Kabid PPA"></div>
        <div class="flex items-end"><label class="check-row w-full"><input type="checkbox" name="aktif" value="1" @checked(old('aktif',$user->aktif ?? true))> Akun aktif</label></div>
        <div><label class="label">Password {{ $user->exists ? '(kosongkan jika tidak diganti)' : '' }}</label><input type="password" name="password" class="input" placeholder="Minimal 8 karakter" @required(! $user->exists)></div>
        <div><label class="label">Konfirmasi Password</label><input type="password" name="password_confirmation" class="input" placeholder="Ulangi password" @required(! $user->exists)></div>
        <div class="sm:col-span-2 flex flex-wrap gap-3 pt-2"><button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan</button><a href="{{ route('admin.pengguna.index') }}" class="btn-secondary">Kembali</a></div>
    </form>
</section>
@endsection
