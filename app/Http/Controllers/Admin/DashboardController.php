<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $role = $user?->role ?? 'operator';
        $total = Pengaduan::whereNotNull('nomor_tiket')->count();
        $bulanIni = Pengaduan::whereNotNull('nomor_tiket')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $selesai = Pengaduan::where('status', 'selesai')->count();
        $prioritasTinggi = Pengaduan::where('tingkat_urgensi', 'tinggi')->count();
        $pending = Pengaduan::where('status', 'menunggu_verifikasi')->count();

        $perStatus = Pengaduan::selectRaw('status, count(*) as total')->whereNotNull('nomor_tiket')->groupBy('status')->pluck('total', 'status');
        $perJenis = Pengaduan::selectRaw('jenis_kekerasan, count(*) as total')->whereNotNull('nomor_tiket')->groupBy('jenis_kekerasan')->pluck('total', 'jenis_kekerasan');
        $perWilayah = Pengaduan::selectRaw('coalesce(kecamatan, "Tidak Diisi") as wilayah, count(*) as total')->whereNotNull('nomor_tiket')->groupBy('wilayah')->pluck('total', 'wilayah');
        $trenBulananRaw = Pengaduan::selectRaw('MONTH(created_at) as bulan, count(*) as total')
            ->whereNotNull('nomor_tiket')
            ->whereYear('created_at', now()->year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
        $trenBulanan = collect(range(1, 12))->mapWithKeys(fn (int $bulan): array => [Carbon::create(now()->year, $bulan, 1)->translatedFormat('M') => (int) ($trenBulananRaw[$bulan] ?? 0)]);
        $terbaru = Pengaduan::latest()->whereNotNull('nomor_tiket')->limit(8)->get();
        $dashboardMeta = match ($role) {
            'kepala_dinas' => [
                'label' => 'Dashboard Kepala Dinas P2KBP3A',
                'subtitle' => 'Pantauan eksekutif laporan PPA, tren kasus, dan rekap wilayah.',
                'gradient' => 'from-violet-600 via-fuchsia-600 to-rose-500',
                'accent' => 'violet',
                'showFiltersHint' => false,
            ],
            'kepala_bidang' => [
                'label' => 'Dashboard Kabid PPA',
                'subtitle' => 'Monitoring penanganan kasus, urgensi, dan distribusi layanan bidang PPA.',
                'gradient' => 'from-emerald-600 via-teal-600 to-cyan-500',
                'accent' => 'emerald',
                'showFiltersHint' => false,
            ],
            default => [
                'label' => 'Dashboard Admin/Operator',
                'subtitle' => 'Kelola verifikasi laporan, tindak lanjut tiket, notifikasi, dan rekap operasional.',
                'gradient' => 'from-blue-600 via-indigo-600 to-sky-500',
                'accent' => 'blue',
                'showFiltersHint' => true,
            ],
        };

        return view('admin.dashboard', compact('role', 'dashboardMeta', 'total', 'bulanIni', 'selesai', 'prioritasTinggi', 'pending', 'perStatus', 'perJenis', 'perWilayah', 'trenBulanan', 'terbaru'));
    }
}
