@extends('layouts.app')
@section('title','Hasil Tracking')
@section('content')
<section class="bg-[#fff7ea] px-4 py-12 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="rounded-none bg-white p-7 shadow-xl shadow-orange-200/30 ring-1 ring-orange-100 silap-slide-up">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div><p class="section-kicker">Nomor Tiket</p><h1 class="mt-2 text-4xl font-black text-slate-950">{{ $pengaduan->nomor_tiket }}</h1>@isset($modePublik)<p class="mt-2 text-sm font-semibold text-orange-700">Mode tracking real-time publik. Identitas korban dan pelapor disembunyikan.</p>@endisset</div>
                <span class="rounded-none bg-orange-100 px-4 py-2 text-sm font-black text-orange-700">{{ $pengaduan->status_label }}</span>
            </div>
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <div class="rounded-none bg-[#fff7ea] p-4"><p class="text-xs font-bold uppercase tracking-widest text-orange-500">Status</p><p class="mt-2 font-black text-slate-950">{{ $pengaduan->status_label }}</p></div>
                <div class="rounded-none bg-[#fff7ea] p-4"><p class="text-xs font-bold uppercase tracking-widest text-orange-500">Tanggal</p><p class="mt-2 font-black text-slate-950">{{ $pengaduan->created_at->format('d/m/Y H:i') }}</p></div>
                <div class="rounded-none bg-[#fff7ea] p-4"><p class="text-xs font-bold uppercase tracking-widest text-orange-500">Catatan Umum</p><p class="mt-2 font-black text-slate-950">{{ $pengaduan->catatan_umum ?: '-' }}</p></div>
            </div>
        </div>

        <div class="rounded-none bg-white p-7 shadow-xl shadow-orange-200/30 ring-1 ring-orange-100">
            <h2 class="text-2xl font-black text-slate-950">Riwayat Status</h2>
            <div class="mt-6 space-y-4">
                @forelse($pengaduan->riwayatStatus as $riwayat)
                    <div class="relative rounded-none bg-[#fffaf3] p-5 pl-16 ring-1 ring-orange-100">
                        <span class="absolute left-5 top-5 grid h-9 w-9 place-items-center rounded-none bg-orange-500 text-white"><i class="fa-solid fa-check"></i></span>
                        <p class="font-black text-slate-950">{{ \App\Models\Pengaduan::labelStatus($riwayat->status) }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-500">{{ $riwayat->created_at->format('d/m/Y H:i') }}</p>
                        <p class="mt-3 leading-7 text-slate-600">{{ $riwayat->catatan ?: '-' }}</p>
                    </div>
                @empty
                    <div class="empty-state">Belum ada riwayat.</div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
