@extends('layouts.admin')
@section('title','Manajemen Pengguna')
@section('content')
<section class="panel">
    <div class="panel-header">
        <div>
            <h2 class="panel-title">Daftar Pengguna</h2>
            <p class="panel-subtitle">Kelola akun Admin/Operator, Kabid PPA, dan Kepala Dinas P2KBP3A.</p>
        </div>
        <a class="btn-primary" href="{{ route('admin.pengguna.create') }}"><i class="fa-solid fa-user-plus"></i> Tambah Pengguna</a>
    </div>
    <div class="mt-5 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800">
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @forelse($pengguna as $user)
                        <tr>
                            <td class="font-medium text-gray-800 dark:text-white/90">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->labelRole() }}</td>
                            <td><span class="badge">{{ $user->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a class="table-action" href="{{ route('admin.pengguna.edit',$user) }}"><i class="fa-solid fa-pen-to-square mr-1"></i> Edit</a>
                                    <form method="POST" action="{{ route('admin.pengguna.destroy',$user) }}">@csrf @method('DELETE')<button class="table-action text-error-600" onclick="return confirm('Hapus pengguna ini?')"><i class="fa-solid fa-trash mr-1"></i> Hapus</button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-10 text-center text-gray-500 dark:text-gray-400">Belum ada pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">{{ $pengguna->links() }}</div>
</section>
@endsection
