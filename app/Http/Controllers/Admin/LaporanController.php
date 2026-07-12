<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsesmenAwal;
use App\Models\BuktiPengaduan;
use App\Models\Pengaduan;
use App\Models\RiwayatStatusPengaduan;
use App\Models\User;
use App\Services\LogAktivitasService;
use App\Services\NotifikasiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function __construct(
        private readonly NotifikasiService $notifikasiService,
        private readonly LogAktivitasService $logAktivitasService,
    ) {}

    public function index(Request $request): View
    {
        $query = Pengaduan::query()->with('operator')->whereNotNull('nomor_tiket')->latest();
        foreach (['status', 'jenis_kekerasan', 'tingkat_urgensi', 'kecamatan'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->string($filter));
            }
        }
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->date('tanggal_mulai'));
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->date('tanggal_selesai'));
        }

        return view('admin.laporan.index', [
            'laporan' => $query->paginate(15)->withQueryString(),
            'opsiStatus' => Pengaduan::opsiStatus(),
            'opsiJenisKasus' => $this->opsiJenisKasus(),
            'opsiUrgensi' => ['tinggi' => 'Tinggi', 'sedang' => 'Sedang', 'rendah' => 'Rendah'],
            'opsiKecamatan' => $this->opsiKecamatan(),
        ]);
    }

    public function show(Pengaduan $laporan): View
    {
        $laporan->load(['bukti', 'riwayatStatus.user', 'asesmenAwal', 'operator']);

        return view('admin.laporan.show', compact('laporan'));
    }

    public function edit(Pengaduan $laporan): View
    {
        return view('admin.laporan.edit', [
            'laporan' => $laporan,
            'opsiKecamatan' => $this->opsiKecamatan(),
            'opsiJenisKasus' => $this->opsiJenisKasus(),
        ]);
    }

    public function update(Request $request, Pengaduan $laporan): RedirectResponse
    {
        $data = $request->validate([
            'nama_pelapor' => ['required', 'string', 'max:255'],
            'nik_pelapor' => ['required', 'digits_between:12,20'],
            'jenis_kelamin_pelapor' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'nomor_whatsapp' => ['required', 'string', 'max:30'],
            'email_pelapor' => ['nullable', 'email', 'max:255'],
            'alamat_pelapor' => ['required', 'string'],
            'kecamatan' => ['nullable', 'string', 'max:120', Rule::in(config('sippak.kecamatan', []))],
            'nama_korban' => ['required', 'string', 'max:255'],
            'umur_korban' => ['required', 'integer', 'min:0', 'max:120'],
            'jenis_kelamin_korban' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'hubungan_dengan_pelapor' => ['required', 'string', 'max:120'],
            'jenis_kekerasan' => ['required', 'string', 'max:120'],
            'lokasi_kejadian' => ['required', 'string', 'max:255'],
            'tanggal_kejadian' => ['required', 'date', 'before_or_equal:today'],
            'kronologi_kejadian' => ['required', 'string', 'min:20'],
        ]);

        $laporan->update($data);
        $this->logAktivitasService->catat('laporan_diedit', 'Nomor tiket: '.$laporan->nomor_tiket);

        return redirect()->route('admin.laporan.show', $laporan)->with('success', 'Data tiket berhasil diperbarui.');
    }

    public function destroy(Pengaduan $laporan): RedirectResponse
    {
        $nomorTiket = $laporan->nomor_tiket;
        $laporan->delete();
        $this->logAktivitasService->catat('laporan_dihapus', 'Nomor tiket: '.$nomorTiket);

        return redirect()->route('admin.laporan.index')->with('success', 'Tiket laporan berhasil dihapus.');
    }

    public function updateStatus(Request $request, Pengaduan $laporan): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:menunggu_verifikasi,diterima,revisi,asesmen_awal,dalam_penanganan,pendampingan,dirujuk,selesai,ditolak'],
            'tingkat_urgensi' => ['required', 'in:tinggi,sedang,rendah'],
            'catatan' => ['nullable', 'string'],
        ]);

        $laporan->update([
            'status' => $data['status'],
            'tingkat_urgensi' => $data['tingkat_urgensi'],
            'catatan_umum' => $data['catatan'] ?? $laporan->catatan_umum,
            'operator_id' => $request->user()->id,
        ]);
        RiwayatStatusPengaduan::create([
            'pengaduan_id' => $laporan->id,
            'status' => $data['status'],
            'catatan' => $data['catatan'] ?? null,
            'user_id' => $request->user()->id,
        ]);
        $laporanTerbaru = $laporan->fresh();
        $this->notifikasiService->statusBerubah($laporanTerbaru, $data['catatan'] ?? null);
        if ($data['status'] === 'revisi') {
            $this->notifikasiService->revisiDiminta($laporanTerbaru, $data['catatan'] ?? null);
        }
        if ($data['status'] === 'selesai') {
            $this->notifikasiService->laporanSelesai($laporanTerbaru, $data['catatan'] ?? null);
        }
        $this->logAktivitasService->catat('status_laporan_diubah', 'Nomor tiket: '.$laporan->nomor_tiket.' menjadi '.$data['status']);

        return back()->with('success', 'Status laporan berhasil diperbarui dan notifikasi dikirim.');
    }

    public function simpanAsesmen(Request $request, Pengaduan $laporan): RedirectResponse
    {
        $data = $request->validate([
            'kondisi_korban' => ['required', 'string'],
            'tingkat_risiko' => ['required', 'in:tinggi,sedang,rendah'],
            'kebutuhan_korban' => ['nullable', 'string'],
            'pendampingan_hukum' => ['nullable', 'boolean'],
            'pendampingan_psikologis' => ['nullable', 'boolean'],
            'catatan_operator' => ['nullable', 'string'],
        ]);

        AsesmenAwal::updateOrCreate(
            ['pengaduan_id' => $laporan->id],
            $data + ['operator_id' => $request->user()->id]
        );
        $laporan->update(['status' => 'asesmen_awal', 'tingkat_urgensi' => $data['tingkat_risiko'], 'operator_id' => $request->user()->id]);
        RiwayatStatusPengaduan::create(['pengaduan_id' => $laporan->id, 'status' => 'asesmen_awal', 'catatan' => 'Asesmen awal disimpan.', 'user_id' => $request->user()->id]);
        $this->notifikasiService->statusBerubah($laporan->fresh(), 'Asesmen awal telah dilakukan.');
        $this->logAktivitasService->catat('asesmen_awal_disimpan', 'Nomor tiket: '.$laporan->nomor_tiket);

        return back()->with('success', 'Asesmen awal berhasil disimpan dan notifikasi dikirim.');
    }

    public function panggilKeKantor(Request $request, Pengaduan $laporan): RedirectResponse
    {
        $data = $request->validate([
            'catatan_panggilan' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->notifikasiService->panggilKeKantor($laporan, $data['catatan_panggilan'] ?? null);
        $this->logAktivitasService->catat('panggilan_kantor_dikirim', 'Nomor tiket: '.$laporan->nomor_tiket);

        return back()->with('success', 'Notifikasi panggilan ke kantor berhasil diproses.');
    }

    public function simpanTindakLanjutKabid(Request $request, Pengaduan $laporan): RedirectResponse
    {
        abort_unless($request->user()?->canGiveTindakLanjut(), 403);

        $data = $request->validate([
            'catatan_tindak_lanjut' => ['required', 'string', 'max:2000'],
        ]);

        RiwayatStatusPengaduan::create([
            'pengaduan_id' => $laporan->id,
            'status' => $laporan->status,
            'catatan' => 'Catatan Kabid PPA: '.$data['catatan_tindak_lanjut'],
            'user_id' => $request->user()->id,
        ]);
        $this->logAktivitasService->catat('catatan_kabid_disimpan', 'Nomor tiket: '.$laporan->nomor_tiket);

        return back()->with('success', 'Catatan tindak lanjut Kabid PPA berhasil disimpan.');
    }

    public function unduhBukti(int $id): StreamedResponse
    {
        $bukti = BuktiPengaduan::findOrFail($id);

        return Storage::download($bukti->path_file, $bukti->nama_asli);
    }

    public function previewBukti(int $id): BinaryFileResponse
    {
        $bukti = BuktiPengaduan::findOrFail($id);
        abort_unless(Storage::exists($bukti->path_file), 404);

        return response()->file(Storage::path($bukti->path_file), [
            'Content-Type' => $bukti->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.addslashes($bukti->nama_asli).'"',
        ]);
    }

    public function cetakLaporan(Pengaduan $laporan)
    {
        $kabid = User::where('role', User::ROLE_KEPALA_BIDANG)->first();
        $pdf = Pdf::loadView('pdf.cetak-laporan', ['pengaduan' => $laporan, 'kabid' => $kabid]);

        return $pdf->download('Laporan-'.$laporan->nomor_tiket.'.pdf');
    }

    public function cetakDisposisi(Pengaduan $laporan)
    {
        $kabid = User::where('role', User::ROLE_KEPALA_BIDANG)->first();
        $pdf = Pdf::loadView('pdf.cetak-disposisi', ['pengaduan' => $laporan, 'kabid' => $kabid]);

        return $pdf->download('Disposisi-'.$laporan->nomor_tiket.'.pdf');
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
