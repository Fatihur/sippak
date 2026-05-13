<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function beranda(): View
    {
        return view('public.beranda', $this->dataPublik());
    }

    public function tentang(): View
    {
        return view('public.tentang');
    }

    public function faq(): View
    {
        return view('public.faq');
    }

    public function statistik(): View
    {
        return view('public.statistik', $this->dataPublik());
    }

    public function edukasi(): View
    {
        return view('public.edukasi');
    }

    private function dataPublik(): array
    {
        $base = Pengaduan::query()->whereNotNull('nomor_tiket');

        return [
            'totalPengaduan' => (clone $base)->count(),
            'kasusDiproses' => (clone $base)->whereIn('status', ['diterima', 'asesmen_awal', 'dalam_penanganan', 'pendampingan', 'dirujuk'])->count(),
            'kasusSelesai' => (clone $base)->where('status', 'selesai')->count(),
            'kasusBulanIni' => (clone $base)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'trenBulanan' => (clone $base)->selectRaw('MONTH(created_at) as bulan, count(*) as total')
                ->whereYear('created_at', now()->year)
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->pluck('total', 'bulan'),
            'perJenis' => (clone $base)->selectRaw('jenis_kekerasan, count(*) as total')
                ->groupBy('jenis_kekerasan')
                ->pluck('total', 'jenis_kekerasan'),
            'perWilayah' => (clone $base)->selectRaw('coalesce(kecamatan, "Tidak Diisi") as wilayah, count(*) as total')
                ->groupBy('wilayah')
                ->pluck('total', 'wilayah'),
        ];
    }
}
