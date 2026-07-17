<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Disposisi;
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
        $trenBulananRaw = Pengaduan::selectRaw('CAST(strftime(\'%m\', created_at) AS INTEGER) as bulan, count(*) as total')
            ->whereNotNull('nomor_tiket')
            ->whereYear('created_at', now()->year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
        $trenBulanan = collect(range(1, 12))->mapWithKeys(fn (int $bulan): array => [Carbon::create(now()->year, $bulan, 1)->translatedFormat('M') => (int) ($trenBulananRaw[$bulan] ?? 0)]);
        $terbaru = Pengaduan::latest()->whereNotNull('nomor_tiket')->limit(8)->get();

        $kasusAtensi = collect();
        $catatanKabid = collect();
        $disposisiMasuk = collect();
        $persentaseSelesai = 0;
        $ditolak = 0;
        $topKecamatan = collect();

        if ($role === 'kepala_bidang') {
            $kasusAtensi = Pengaduan::whereNotNull('nomor_tiket')
                ->whereIn('status', ['menunggu_verifikasi', 'diterima', 'asesmen_awal', 'dalam_penanganan', 'pendampingan', 'didisposisikan_ke_kabid'])
                ->where('tingkat_urgensi', 'tinggi')
                ->latest()
                ->limit(5)
                ->get();
            $catatanKabid = \App\Models\RiwayatStatusPengaduan::where('catatan', 'like', 'Catatan Kabid PPA: %')
                ->with('pengaduan')
                ->latest()
                ->limit(5)
                ->get();
            $disposisiMasuk = Disposisi::where('tingkat', 'kadis')
                ->where('untuk_user_id', $user->id)
                ->with('pengaduan', 'dariUser')
                ->latest()
                ->limit(5)
                ->get();
        } elseif ($role === 'kepala_dinas') {
            $persentaseSelesai = $total > 0 ? round(($selesai / $total) * 100, 1) : 0;
            $ditolak = Pengaduan::where('status', 'ditolak')->count();
            $menungguDisposisi = Pengaduan::where('status', 'menunggu_disposisi_kadis')->count();
            $disposisiMasuk = Pengaduan::where('status', 'menunggu_disposisi_kadis')
                ->latest()
                ->limit(5)
                ->get();
            $topKecamatan = Pengaduan::selectRaw('coalesce(kecamatan, "Tidak Diisi") as wilayah, count(*) as total')
                ->whereNotNull('nomor_tiket')
                ->groupBy('wilayah')
                ->orderByDesc('total')
                ->limit(3)
                ->get();
        }

        $dashboardMeta = match ($role) {
            'kepala_dinas' => [
                'label' => 'Dashboard Kepala Dinas P2KBP3A',
                'subtitle' => 'Pantauan eksekutif laporan PPA, kinerja penyelesaian kasus, dan rekap wilayah.',
                'gradient' => 'from-violet-600 via-fuchsia-600 to-rose-500',
                'accent' => 'violet',
                'showFiltersHint' => false,
            ],
            'kepala_bidang' => [
                'label' => 'Dashboard Kabid PPA',
                'subtitle' => 'Pusat pengawasan penanganan kasus, koordinasi atensi urgensi tinggi, dan pengawalan bidang PPA.',
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

        return view('admin.dashboard', compact(
            'role', 'dashboardMeta', 'total', 'bulanIni', 'selesai', 'prioritasTinggi', 'pending',
            'perStatus', 'perJenis', 'perWilayah', 'trenBulanan', 'terbaru',
            'kasusAtensi', 'catatanKabid', 'persentaseSelesai', 'ditolak', 'topKecamatan',
            'disposisiMasuk'
        ));
    }
}

