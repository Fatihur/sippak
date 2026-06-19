@extends('layouts.admin')
@section('title','Laporan Pengaduan')
@section('content')
@if(auth()->user()->role === 'operator')
<section class="panel mb-6">
    <div class="panel-header">
        <div>
            <h2 class="panel-title">Filter Laporan</h2>
            <p class="panel-subtitle">Pilih status, kategori, urgensi, wilayah, dan tanggal laporan.</p>
        </div>
        <a href="{{ route('admin.laporan.index') }}" class="btn-secondary"><i class="fa-solid fa-rotate-left"></i> Reset</a>
    </div>
    <form class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
        <div><label class="label">Status</label><select class="input" name="status"><option value="">Semua Status</option>@foreach($opsiStatus as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div><label class="label">Jenis Kasus</label><select class="input" name="jenis_kekerasan"><option value="">Semua Jenis</option>@foreach($opsiJenisKasus as $value => $label)<option value="{{ $value }}" @selected(request('jenis_kekerasan') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div><label class="label">Urgensi</label><select class="input" name="tingkat_urgensi"><option value="">Semua Urgensi</option>@foreach($opsiUrgensi as $value => $label)<option value="{{ $value }}" @selected(request('tingkat_urgensi') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div><label class="label">Kecamatan</label><select class="input" name="kecamatan"><option value="">Semua Kecamatan</option>@foreach($opsiKecamatan as $value => $label)<option value="{{ $value }}" @selected(request('kecamatan') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div><label class="label">Tanggal Mulai</label><input class="input" type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"></div>
        <div class="flex items-end"><button class="btn-primary w-full"><i class="fa-solid fa-filter"></i> Terapkan</button></div>
    </form>
</section>
@endif

<section class="panel">
    <div class="panel-header">
        <div>
            <h2 class="panel-title">Data Laporan</h2>
            <p class="panel-subtitle">Total data pada halaman ini: {{ $laporan->count() }}</p>
        </div>
    </div>
    <div class="mt-5 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800">
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead><tr><th>Tiket</th><th>Tanggal</th>@if(auth()->user()->isOperator())<th>Pelapor</th>@endif<th>Jenis</th><th>Kecamatan</th><th>Status</th><th>Urgensi</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                @forelse($laporan as $item)
                    <tr>
                        <td class="font-medium text-gray-800 dark:text-white/90">{{ $item->nomor_tiket }}</td>
                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        @if(auth()->user()->isOperator())
                            <td>{{ $item->nama_pelapor }}</td>
                        @endif
                        <td>{{ $item->jenis_kekerasan }}</td>
                        <td>{{ $item->kecamatan ?: '-' }}</td>
                        <td><span class="badge">{{ $item->status_label }}</span></td>
                        <td><span class="urgency urgency-{{ $item->tingkat_urgensi }}">{{ ucfirst($item->tingkat_urgensi) }}</span></td>
                        <td class="text-right">
                            <div class="flex justify-end gap-1">
                                <a class="table-action" href="{{ route('admin.laporan.show',$item) }}">Detail</a>
                                @if(auth()->user()->role === 'operator')
                                    <a class="table-action" href="{{ route('admin.laporan.edit',$item) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.laporan.destroy',$item) }}" onsubmit="return confirm('Hapus tiket {{ $item->nomor_tiket }}? Data tidak bisa dikembalikan.')">
                                        @csrf @method('DELETE')
                                        <button class="table-action text-error-600 hover:bg-error-50 dark:hover:bg-error-500/10" type="submit">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ auth()->user()->isOperator() ? 8 : 7 }}" class="py-10 text-center text-gray-500 dark:text-gray-400">Belum ada laporan sesuai filter.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">{{ $laporan->links() }}</div>
</section>
@endsection
