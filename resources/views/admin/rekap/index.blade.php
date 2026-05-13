@extends('layouts.admin')
@section('title','Rekap Laporan')
@section('content')
<section class="panel mb-6">
    <div class="panel-header">
        <div>
            <h2 class="panel-title">Filter Rekap</h2>
            <p class="panel-subtitle">Pilih data laporan untuk export dan evaluasi.</p>
        </div>
        <a href="{{ route('admin.rekap.index') }}" class="btn-secondary"><i class="fa-solid fa-rotate-left"></i> Reset</a>
    </div>
    <form class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
        <div><label class="label">Mulai</label><input class="input" type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"></div>
        <div><label class="label">Selesai</label><input class="input" type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"></div>
        <div><label class="label">Jenis Kasus</label><select class="input" name="jenis_kekerasan"><option value="">Semua Jenis</option>@foreach($opsiJenisKasus as $value => $label)<option value="{{ $value }}" @selected(request('jenis_kekerasan') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div><label class="label">Status</label><select class="input" name="status"><option value="">Semua Status</option>@foreach($opsiStatus as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div><label class="label">Kecamatan</label><select class="input" name="kecamatan"><option value="">Semua Kecamatan</option>@foreach($opsiKecamatan as $value => $label)<option value="{{ $value }}" @selected(request('kecamatan') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="flex items-end"><button class="btn-primary w-full"><i class="fa-solid fa-filter"></i> Filter</button></div>
    </form>
    <div class="mt-5 flex flex-wrap gap-3">
        <a class="btn-secondary" href="{{ route('admin.rekap.export-csv', request()->query()) }}"><i class="fa-solid fa-file-csv"></i> Export Excel/CSV</a>
        <a class="btn-secondary" href="{{ route('admin.rekap.export-pdf', request()->query()) }}" target="_blank"><i class="fa-solid fa-print"></i> Export PDF / Cetak</a>
        @if(auth()->user()->role === 'operator')<a class="btn-secondary" href="{{ route('admin.backup.sqlite') }}"><i class="fa-solid fa-database"></i> Backup Database</a>@endif
    </div>
</section>

<section class="panel">
    <div class="panel-header"><div><h2 class="panel-title">Data Rekap</h2><p class="panel-subtitle">{{ $laporan->total() }} laporan ditemukan.</p></div></div>
    <div class="mt-5 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800">
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead><tr><th>Tiket</th><th>Tanggal</th><th>Pelapor</th><th>Korban</th><th>Jenis</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($laporan as $item)
                        <tr><td class="font-medium text-gray-800 dark:text-white/90">{{ $item->nomor_tiket }}</td><td>{{ $item->created_at->format('d/m/Y') }}</td><td>{{ $item->nama_pelapor }}</td><td>{{ $item->nama_korban }}</td><td>{{ $item->jenis_kekerasan }}</td><td><span class="badge">{{ $item->status_label }}</span></td></tr>
                    @empty
                        <tr><td colspan="6" class="py-10 text-center text-gray-500 dark:text-gray-400">Tidak ada data rekap.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">{{ $laporan->links() }}</div>
</section>
@endsection
