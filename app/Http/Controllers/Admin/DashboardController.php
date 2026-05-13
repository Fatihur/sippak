<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $total = Pengaduan::whereNotNull('nomor_tiket')->count();
        $bulanIni = Pengaduan::whereNotNull('nomor_tiket')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $selesai = Pengaduan::where('status', 'selesai')->count();
        $prioritasTinggi = Pengaduan::where('tingkat_urgensi', 'tinggi')->count();
        $pending = Pengaduan::where('status', 'menunggu_verifikasi')->count();

        $perStatus = Pengaduan::selectRaw('status, count(*) as total')->whereNotNull('nomor_tiket')->groupBy('status')->pluck('total', 'status');
        $perJenis = Pengaduan::selectRaw('jenis_kekerasan, count(*) as total')->whereNotNull('nomor_tiket')->groupBy('jenis_kekerasan')->pluck('total', 'jenis_kekerasan');
        $perWilayah = Pengaduan::selectRaw('coalesce(kecamatan, "Tidak Diisi") as wilayah, count(*) as total')->whereNotNull('nomor_tiket')->groupBy('wilayah')->pluck('total', 'wilayah');
        $trenBulanan = Pengaduan::selectRaw('MONTH(created_at) as bulan, count(*) as total')
            ->whereNotNull('nomor_tiket')
            ->whereYear('created_at', now()->year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
        $terbaru = Pengaduan::latest()->whereNotNull('nomor_tiket')->limit(8)->get();

        return view('admin.dashboard', compact('total', 'bulanIni', 'selesai', 'prioritasTinggi', 'pending', 'perStatus', 'perJenis', 'perWilayah', 'trenBulanan', 'terbaru'));
    }
}
