<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.rekap.index', [
            'laporan' => $this->queryTersaring($request)->paginate(20)->withQueryString(),
            'opsiStatus' => Pengaduan::opsiStatus(),
            'opsiJenisKasus' => $this->opsiJenisKasus(),
            'opsiUrgensi' => ['tinggi' => 'Tinggi', 'sedang' => 'Sedang', 'rendah' => 'Rendah'],
            'opsiKecamatan' => $this->opsiKecamatan(),
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $laporan = $this->queryTersaring($request)->get();

        return response()->streamDownload(function () use ($laporan): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Nomor Tiket', 'Tanggal', 'Pelapor', 'Korban', 'Jenis Kekerasan', 'Kecamatan', 'Status', 'Urgensi']);
            foreach ($laporan as $item) {
                fputcsv($out, [$item->nomor_tiket, $item->created_at->format('Y-m-d'), $item->nama_pelapor, $item->nama_korban, $item->jenis_kekerasan, $item->kecamatan, $item->status_label, $item->tingkat_urgensi]);
            }
            fclose($out);
        }, 'rekap-pengaduan.csv');
    }

    public function exportPdf(Request $request): Response
    {
        $laporan = $this->queryTersaring($request)->get();
        $rows = $laporan->map(function (Pengaduan $item): string {
            return '<tr><td>'.e($item->nomor_tiket).'</td><td>'.$item->created_at->format('d/m/Y').'</td><td>'.e($item->nama_pelapor).'</td><td>'.e($item->jenis_kekerasan).'</td><td>'.e($item->kecamatan ?: '-').'</td><td>'.e($item->status_label).'</td></tr>';
        })->implode('');
        $html = '<!doctype html><html><head><meta charset="utf-8"><title>Rekap SILAPAK</title><style>@page{size:A4;margin:18mm}body{font-family:Arial,sans-serif;color:#0F172A}.kop{display:flex;align-items:center;gap:18px;border-bottom:4px double #0f172a;padding-bottom:14px;margin-bottom:18px}.logo{width:72px;height:72px;object-fit:contain}.kop h1{margin:0;font-size:20px;text-align:center;color:#0f172a}.kop p{margin:3px 0;text-align:center;font-size:12px}.judul{text-align:center;margin:18px 0}table{width:100%;border-collapse:collapse}th,td{border:1px solid #cbd5e1;padding:8px;font-size:12px}th{background:#2563EB;color:white}.meta{font-size:12px;margin-bottom:12px}</style></head><body><div class="kop"><img class="logo" src="'.asset('logo-sumbawa.png').'" alt="Logo"><div style="flex:1"><h1>PEMERINTAH KABUPATEN SUMBAWA<br>DINAS PENGENDALIAN PENDUDUK, KELUARGA BERENCANA,<br>PEMBERDAYAAN PEREMPUAN DAN PERLINDUNGAN ANAK</h1><p>Jl. Garuda No. 1 Sumbawa Besar</p></div></div><div class="judul"><h2>REKAP LAPORAN SILAPAK</h2></div><p class="meta">Dicetak: '.now()->format('d/m/Y H:i').'</p><table><thead><tr><th>Tiket</th><th>Tanggal</th><th>Pelapor</th><th>Jenis</th><th>Kecamatan</th><th>Status</th></tr></thead><tbody>'.$rows.'</tbody></table><script>window.print()</script></body></html>';

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="rekap-pengaduan.html"',
        ]);
    }

    private function queryTersaring(Request $request)
    {
        $query = Pengaduan::whereNotNull('nomor_tiket')->latest();
        foreach (['status', 'jenis_kekerasan', 'tingkat_urgensi', 'kecamatan'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, Str::of($request->input($filter))->trim()->toString());
            }
        }
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->date('tanggal_mulai'));
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->date('tanggal_selesai'));
        }

        return $query;
    }

    private function opsiJenisKasus(): array
    {
        return [
            'KDRT' => 'KDRT',
            'Kekerasan seksual' => 'Kekerasan seksual',
            'Kekerasan fisik' => 'Kekerasan fisik',
            'Kekerasan verbal' => 'Kekerasan verbal',
            'Penelantaran anak' => 'Penelantaran anak',
            'Eksploitasi anak' => 'Eksploitasi anak',
            'Penganiayaan' => 'Penganiayaan',
            'Pelecehan' => 'Pelecehan',
            'Lainnya' => 'Lainnya',
        ];
    }

    private function opsiKecamatan(): array
    {
        return collect(config('sippak.kecamatan', []))->mapWithKeys(fn (string $kecamatan): array => [$kecamatan => $kecamatan])->toArray();
    }
}
