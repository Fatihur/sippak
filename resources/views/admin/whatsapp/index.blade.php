@extends('layouts.admin')
@section('title','WhatsApp Gateway')
@section('content')
<div class="grid gap-6 xl:grid-cols-3">
    <section class="panel xl:col-span-2">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Koneksi WhatsApp Gateway</h2>
                <p class="panel-subtitle">Hubungkan SILAPAK dengan WhatsApp melalui Baileys gateway.</p>
            </div>
            <span class="badge">{{ $status['status'] ?? 'unknown' }}</span>
        </div>

        <div class="mt-6 grid gap-5 md:grid-cols-2">
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ ($status['connected'] ?? false) ? 'Terhubung' : ucfirst($status['status'] ?? 'Tidak terhubung') }}</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Nomor: {{ $status['number'] ?? '-' }}</p>
                @if(!empty($status['message']))<p class="mt-2 text-sm text-error-600">{{ $status['message'] }}</p>@endif
            </div>
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Aksi Koneksi</p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <form method="POST" action="{{ route('admin.whatsapp.restart') }}">@csrf<button class="btn-secondary"><i class="fa-solid fa-rotate"></i> Restart</button></form>
                    <form method="POST" action="{{ route('admin.whatsapp.logout') }}">@csrf<button class="btn-danger" onclick="return confirm('Logout WhatsApp dari gateway?')"><i class="fa-solid fa-right-from-bracket"></i> Logout WA</button></form>
                </div>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="font-semibold text-gray-800 dark:text-white/90">QR Login WhatsApp</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Jalankan gateway, lalu scan QR dari aplikasi WhatsApp di HP.</p>
            <div class="mt-5 flex min-h-72 items-center justify-center rounded-2xl bg-gray-50 p-6 dark:bg-gray-900">
                @if(!empty($qr['qr']))
                    <img src="{{ $qr['qr'] }}" alt="QR WhatsApp" class="h-72 w-72 rounded-xl bg-white p-3 shadow-theme-sm">
                @elseif(($status['connected'] ?? false))
                    <div class="text-center text-success-600"><i class="fa-solid fa-circle-check text-5xl"></i><p class="mt-3 font-semibold">WhatsApp sudah terhubung.</p></div>
                @else
                    <div class="text-center text-gray-500"><i class="fa-solid fa-qrcode text-5xl"></i><p class="mt-3">QR belum tersedia. Pastikan gateway berjalan.</p></div>
                @endif
            </div>
        </div>
    </section>

    <aside class="space-y-6">
        <section class="panel">
            <h3 class="panel-title">Pengaturan Gateway</h3>
            <form method="POST" action="{{ route('admin.whatsapp.simpan') }}" class="mt-5 space-y-4">
                @csrf
                <div><label class="label">Gateway URL</label><input class="input" name="wa_gateway_url" value="{{ old('wa_gateway_url', $gatewayUrl) }}" placeholder="http://127.0.0.1:3010" required></div>
                <div><label class="label">API Token</label><input class="input" name="wa_gateway_token" value="{{ old('wa_gateway_token', $gatewayToken) }}" placeholder="Token rahasia gateway" required></div>
                <button class="btn-primary w-full"><i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan</button>
            </form>
        </section>

        <section class="panel">
            <h3 class="panel-title">Test Kirim Pesan</h3>
            <form method="POST" action="{{ route('admin.whatsapp.test') }}" class="mt-5 space-y-4">
                @csrf
                <div><label class="label">Nomor Tujuan</label><input class="input" name="nomor_tujuan" placeholder="081234567890" required></div>
                <div><label class="label">Pesan</label><textarea class="input" name="pesan_test" rows="4" required>Test pesan dari SILAPAK WhatsApp Gateway.</textarea></div>
                <button class="btn-primary w-full"><i class="fa-brands fa-whatsapp"></i> Kirim Test</button>
            </form>
        </section>

        <section class="panel">
            <h3 class="panel-title">Cara Menjalankan</h3>
            <div class="mt-4 rounded-xl bg-gray-900 p-4 font-mono text-xs text-gray-100">npm run wa:gateway</div>
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Default URL: <b>http://127.0.0.1:3010</b>. Default token: <b>sippak-local-token</b>. Untuk production, jalankan dengan process manager seperti PM2.</p>
        </section>
    </aside>
</div>

<section class="panel mt-6">
    <div class="panel-header"><div><h3 class="panel-title">Log Notifikasi WhatsApp</h3><p class="panel-subtitle">Riwayat pengiriman OTP, tiket, dan update status.</p></div></div>
    <div class="mt-5 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800"><div class="overflow-x-auto"><table class="admin-table"><thead><tr><th>Waktu</th><th>Jenis</th><th>Nomor</th><th>Tiket</th><th>Status</th><th>Error</th><th class="text-right">Aksi</th></tr></thead><tbody>@forelse($logs as $log)<tr><td>{{ $log->created_at->format('d/m/Y H:i') }}</td><td>{{ $log->jenis }}</td><td>{{ $log->nomor_tujuan }}</td><td>{{ $log->pengaduan?->nomor_tiket ?: '-' }}</td><td><span class="badge">{{ $log->status }}</span></td><td class="max-w-xs truncate">{{ $log->error ?: '-' }}</td><td class="text-right">@if($log->status !== 'terkirim')<form method="POST" action="{{ route('admin.whatsapp.log.kirim-ulang', $log) }}">@csrf<button class="table-action">Kirim Ulang</button></form>@else - @endif</td></tr>@empty<tr><td colspan="7" class="py-8 text-center text-gray-500">Belum ada log WhatsApp.</td></tr>@endforelse</tbody></table></div></div>
</section>
@endsection
