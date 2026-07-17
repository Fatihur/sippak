@extends('layouts.admin')
@section('title', 'Disposisi')
@section('content')
<section class="panel">
    <div class="panel-header">
        <div>
            <h2 class="panel-title">Daftar Disposisi</h2>
            <p class="panel-subtitle">Total: {{ $laporan->total() }}</p>
        </div>
    </div>
    <div class="mt-5 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800">
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Tiket</th>
                        <th>Pelapor</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $item)
                        <tr>
                            <td class="font-medium text-gray-800 dark:text-white/90">{{ $item->nomor_tiket }}</td>
                            <td>{{ $item->nama_pelapor }}</td>
                            <td>{{ $item->jenis_kekerasan }}</td>
                            <td><span class="badge">{{ $item->status_label }}</span></td>
                            <td>{{ $item->created_at->format('d/m/Y') }}</td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    @if(auth()->user()->isKepalaDinas() && $item->status === 'menunggu_disposisi_kadis')
                                        <a class="table-action" href="{{ route('admin.disposisi.kadis-form', $item) }}"><i class="fa-solid fa-pen"></i> Disposisi</a>
                                    @endif
                                    @if(auth()->user()->isKabidPpa())
                                        @php
                                            $dispKadis = $item->disposisi->where('tingkat', 'kadis')->first();
                                        @endphp
                                        @if($dispKadis)
                                            <a class="table-action" href="{{ route('admin.disposisi.show', $dispKadis) }}">Detail Disposisi</a>
                                            @if($item->status === 'didisposisikan_ke_kabid')
                                                <a class="table-action text-emerald-600" href="{{ route('admin.disposisi.kabid-form', $dispKadis) }}"><i class="fa-solid fa-forward"></i> Teruskan</a>
                                            @endif
                                        @endif
                                    @endif
                                    @if(auth()->user()->isOperator() && $item->status === 'menunggu_tindak_lanjut_operator')
                                        <a class="table-action" href="{{ route('admin.disposisi.tindak-lanjut', $item) }}"><i class="fa-solid fa-check"></i> Tindak Lanjut</a>
                                    @endif
                                    <a class="table-action" href="{{ route('admin.laporan.show', $item) }}">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-gray-500 dark:text-gray-400">Belum ada disposisi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">{{ $laporan->links() }}</div>
</section>
@endsection
