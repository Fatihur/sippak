<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Disposisi;
use App\Models\Pengaduan;
use App\Models\RiwayatStatusPengaduan;
use App\Models\TindakLanjut;
use App\Services\LogAktivitasService;
use App\Services\NotifikasiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DisposisiController extends Controller
{
    public function __construct(
        private readonly NotifikasiService $notifikasiService,
        private readonly LogAktivitasService $logAktivitasService,
    ) {}

    public function kirimKeKadis(Request $request, Pengaduan $laporan): RedirectResponse
    {
        abort_unless($request->user()?->isOperator(), 403);

        $laporan->update([
            'status' => 'menunggu_disposisi_kadis',
            'dikirim_ke_kadis_at' => now(),
            'operator_id' => $request->user()->id,
        ]);
        RiwayatStatusPengaduan::create([
            'pengaduan_id' => $laporan->id,
            'status' => 'menunggu_disposisi_kadis',
            'catatan' => 'Admin mengirim pengaduan ke Kepala Dinas untuk disposisi.',
            'user_id' => $request->user()->id,
        ]);
        $this->logAktivitasService->catat('dikirim_ke_kadis', 'Nomor tiket: '.$laporan->nomor_tiket);

        return redirect()->route('admin.laporan.show', $laporan)->with('success', 'Pengaduan berhasil dikirim ke Kepala Dinas.');
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Pengaduan::with('operator')->whereNotNull('nomor_tiket');

        if ($user->isKepalaDinas()) {
            $query->where('status', 'menunggu_disposisi_kadis');
        } elseif ($user->isKabidPpa()) {
            $disposisiIds = Disposisi::where('tingkat', 'kadis')
                ->where('untuk_user_id', $user->id)
                ->pluck('pengaduan_id');
            $query->whereIn('id', $disposisiIds);
        } elseif ($user->isOperator()) {
            $disposisiIds = Disposisi::where('tingkat', 'kabid')
                ->where('untuk_user_id', $user->id)
                ->pluck('pengaduan_id');
            $tindakLanjutIds = TindakLanjut::where('user_id', $user->id)->pluck('pengaduan_id');
            $query->whereIn('id', $disposisiIds)->orWhereIn('id', $tindakLanjutIds);
        }

        return view('admin.disposisi.index', [
            'laporan' => $query->latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function createKadis(Pengaduan $laporan): View
    {
        abort_unless(auth()->user()?->isKepalaDinas(), 403);
        abort_unless($laporan->status === 'menunggu_disposisi_kadis', 404);

        $kabidList = \App\Models\User::where('role', \App\Models\User::ROLE_KEPALA_BIDANG)->get();

        return view('admin.disposisi.form-kadis', compact('laporan', 'kabidList'));
    }

    public function storeKadis(Request $request, Pengaduan $laporan): RedirectResponse
    {
        abort_unless($request->user()?->isKepalaDinas(), 403);

        $data = $request->validate([
            'tujuan_kabid' => ['required', 'exists:users,id'],
            'prioritas' => ['required', 'in:biasa, penting, sangat_mendesak'],
            'instruksi' => ['required', 'string'],
        ]);

        $nomorDisposisi = 'DSP-PPA-'.now()->format('Ymd').'-'.str_pad((Disposisi::whereDate('created_at', today())->count() + 1), 4, '0', STR_PAD_LEFT);

        $disposisi = Disposisi::create([
            'pengaduan_id' => $laporan->id,
            'nomor_disposisi' => $nomorDisposisi,
            'dari_user_id' => $request->user()->id,
            'untuk_user_id' => $data['tujuan_kabid'],
            'tingkat' => 'kadis',
            'tanggal_disposisi' => now(),
            'prioritas' => str_replace(' ', '_', $data['prioritas']),
            'instruksi' => $data['instruksi'],
        ]);

        $laporan->update(['status' => 'didisposisikan_ke_kabid']);
        RiwayatStatusPengaduan::create([
            'pengaduan_id' => $laporan->id,
            'status' => 'didisposisikan_ke_kabid',
            'catatan' => 'Kepala Dinas memberikan disposisi kepada Kabid PPA.',
            'user_id' => $request->user()->id,
        ]);
        $this->logAktivitasService->catat('disposisi_kadis', 'Nomor: '.$nomorDisposisi.', Tiket: '.$laporan->nomor_tiket);

        return redirect()->route('admin.disposisi.show', $disposisi)->with('success', 'Disposisi berhasil dikirimkan.');
    }

    public function show(Disposisi $disposisi): View
    {
        $disposisi->load(['pengaduan.bukti', 'pengaduan.riwayatStatus.user', 'dariUser', 'untukUser']);

        return view('admin.disposisi.show', compact('disposisi'));
    }

    public function createKabid(Disposisi $disposisi): View
    {
        abort_unless(auth()->user()?->isKabidPpa(), 403);
        abort_unless($disposisi->tingkat === 'kadis', 404);

        $operatorList = \App\Models\User::where('role', \App\Models\User::ROLE_OPERATOR)->get();

        return view('admin.disposisi.form-kabid', compact('disposisi', 'operatorList'));
    }

    public function storeKabid(Request $request, Disposisi $disposisi): RedirectResponse
    {
        abort_unless($request->user()?->isKabidPpa(), 403);

        $data = $request->validate([
            'nama_petugas' => ['required', 'string'],
            'arahan_pelaksanaan' => ['required', 'string'],
        ]);

        $nomorDisposisi = 'DSP-PPA-'.now()->format('Ymd').'-'.str_pad((Disposisi::whereDate('created_at', today())->count() + 1), 4, '0', STR_PAD_LEFT);

        $operator = \App\Models\User::where('role', \App\Models\User::ROLE_OPERATOR)->first();
        $disposisiKabid = Disposisi::create([
            'pengaduan_id' => $disposisi->pengaduan_id,
            'nomor_disposisi' => $nomorDisposisi,
            'dari_user_id' => $request->user()->id,
            'untuk_user_id' => $operator?->id,
            'tingkat' => 'kabid',
            'tanggal_disposisi' => now(),
            'nama_petugas' => $data['nama_petugas'],
            'arahan_pelaksanaan' => $data['arahan_pelaksanaan'],
        ]);

        $laporan = $disposisi->pengaduan;
        $laporan->update(['status' => 'menunggu_tindak_lanjut_operator']);
        RiwayatStatusPengaduan::create([
            'pengaduan_id' => $laporan->id,
            'status' => 'menunggu_tindak_lanjut_operator',
            'catatan' => 'Kabid PPA meneruskan disposisi kepada Operator. Petugas: '.$data['nama_petugas'],
            'user_id' => $request->user()->id,
        ]);
        $this->logAktivitasService->catat('disposisi_kabid', 'Nomor: '.$nomorDisposisi.', Tiket: '.$laporan->nomor_tiket);

        return redirect()->route('admin.disposisi.show', $disposisiKabid)->with('success', 'Disposisi berhasil diteruskan ke Operator.');
    }

    public function riwayat(Pengaduan $laporan): View
    {
        $laporan->load(['disposisi.dariUser', 'disposisi.untukUser', 'tindakLanjut.user', 'riwayatStatus.user']);

        return view('admin.disposisi.riwayat', compact('laporan'));
    }

    public function tindakLanjut(Pengaduan $laporan): View
    {
        abort_unless(auth()->user()?->isOperator(), 403);

        $laporan->load(['disposisi' => function ($q) {
            $q->with('dariUser')->latest();
        }, 'tindakLanjut.user']);

        return view('admin.disposisi.tindak-lanjut', compact('laporan'));
    }

    public function storeTindakLanjut(Request $request, Pengaduan $laporan): RedirectResponse
    {
        abort_unless($request->user()?->isOperator(), 403);

        $data = $request->validate([
            'tanggal_penanganan' => ['required', 'date'],
            'hasil_penanganan' => ['required', 'string'],
            'keterangan' => ['nullable', 'string'],
            'status_penanganan' => ['required', 'in:diproses,selesai'],
        ]);

        $beritaAcara = null;
        $dokumentasi = null;
        $dokumenLain = null;

        if ($request->hasFile('berita_acara')) {
            $beritaAcara = $request->file('berita_acara')->store('tindak-lanjut/berita-acara', 'public');
        }
        if ($request->hasFile('dokumentasi')) {
            $dokumentasi = $request->file('dokumentasi')->store('tindak-lanjut/dokumentasi', 'public');
        }
        if ($request->hasFile('dokumen_lain')) {
            $dokumenLain = $request->file('dokumen_lain')->store('tindak-lanjut/dokumen-lain', 'public');
        }

        TindakLanjut::create([
            'pengaduan_id' => $laporan->id,
            'user_id' => $request->user()->id,
            'tanggal_penanganan' => $data['tanggal_penanganan'],
            'hasil_penanganan' => $data['hasil_penanganan'],
            'keterangan' => $data['keterangan'],
            'status_penanganan' => $data['status_penanganan'],
            'berita_acara' => $beritaAcara,
            'dokumentasi' => $dokumentasi,
            'dokumen_lain' => $dokumenLain,
        ]);

        $statusBaru = $data['status_penanganan'] === 'selesai' ? 'selesai' : 'dalam_penanganan';
        $laporan->update(['status' => $statusBaru]);
        RiwayatStatusPengaduan::create([
            'pengaduan_id' => $laporan->id,
            'status' => $statusBaru,
            'catatan' => 'Tindak lanjut oleh Operator: '.$data['hasil_penanganan'],
            'user_id' => $request->user()->id,
        ]);
        $this->logAktivitasService->catat('tindak_lanjut_disimpan', 'Tiket: '.$laporan->nomor_tiket);

        if ($data['status_penanganan'] === 'selesai') {
            $this->notifikasiService->laporanSelesai($laporan->fresh(), $data['hasil_penanganan']);
        } else {
            $this->notifikasiService->statusBerubah($laporan->fresh(), 'Tindak lanjut: '.$data['hasil_penanganan']);
        }

        return redirect()->route('admin.laporan.show', $laporan)->with('success', 'Tindak lanjut berhasil disimpan.');
    }

    public function cetak(Disposisi $disposisi)
    {
        $disposisi->load(['pengaduan', 'dariUser', 'untukUser']);
        $pdf = Pdf::loadView('pdf.cetak-disposisi', ['disposisi' => $disposisi]);

        return $pdf->download('Disposisi-'.$disposisi->nomor_disposisi.'.pdf');
    }
}
