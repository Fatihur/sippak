<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\RiwayatStatusPengaduan;
use App\Services\LogAktivitasService;
use App\Services\NotifikasiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PengaduanController extends Controller
{
    public function __construct(
        private readonly NotifikasiService $notifikasiService,
        private readonly LogAktivitasService $logAktivitasService,
    ) {}

    public function beranda(): View
    {
        return view('pengaduan.beranda');
    }

    public function buat(): View
    {
        return view('pengaduan.form', ['opsiKecamatan' => config('sippak.kecamatan', [])]);
    }

    public function simpan(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_pelapor' => ['required', 'string', 'max:255'],
            'nik_pelapor' => ['required', 'digits_between:12,20'],
            'jenis_kelamin_pelapor' => ['required', 'in:Laki-laki,Perempuan'],
            'nomor_whatsapp' => ['required', 'string', 'max:30'],
            'email_pelapor' => ['nullable', 'email', 'max:255'],
            'alamat_pelapor' => ['required', 'string'],
            'kecamatan' => ['nullable', 'string', 'max:120', 'in:'.implode(',', config('sippak.kecamatan', []))],
            'nama_korban' => ['required', 'string', 'max:255'],
            'umur_korban' => ['required', 'integer', 'min:0', 'max:120'],
            'jenis_kelamin_korban' => ['required', 'in:Laki-laki,Perempuan'],
            'hubungan_dengan_pelapor' => ['required', 'string', 'max:120'],
            'jenis_kekerasan' => ['required', 'string', 'max:120'],
            'lokasi_kejadian' => ['required', 'string', 'max:255'],
            'tanggal_kejadian' => ['required', 'date', 'before_or_equal:today'],
            'kronologi_kejadian' => ['required', 'string', 'min:20'],
            'persetujuan' => ['accepted'],
            'bukti.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
        ]);

        $otp = (string) random_int(100000, 999999);
        $pengaduan = Pengaduan::create(array_merge($data, [
            'otp_hash' => Hash::make($otp),
            'otp_kedaluwarsa_at' => now()->addMinutes(15),
            'status' => 'menunggu_otp',
        ]));

        foreach ($request->file('bukti', []) as $file) {
            $path = $file->store('bukti-pengaduan/'.$pengaduan->id, 'local');
            $pengaduan->bukti()->create([
                'nama_asli' => $file->getClientOriginalName(),
                'path_file' => $path,
                'mime_type' => $file->getClientMimeType(),
                'ukuran_file' => $file->getSize(),
            ]);
        }

        $notifikasiTerkirim = true;
        try {
            $this->notifikasiService->kirimOtp($pengaduan, $otp);
        } catch (\Throwable $e) {
            report($e);
            $notifikasiTerkirim = false;
        }

        $this->logAktivitasService->catat('pengaduan_dibuat', 'Pengaduan baru menunggu OTP: '.$pengaduan->id);
        session(['otp_demo_'.$pengaduan->id => $otp]);

        $pesan = $notifikasiTerkirim
            ? 'Kode OTP telah dikirim melalui Email/WhatsApp. Masukkan kode OTP untuk melanjutkan.'
            : 'Pengaduan tersimpan, namun pengiriman OTP melalui WhatsApp/Email gagal. Untuk demo lokal, OTP ditampilkan di halaman verifikasi.';

        return redirect()
            ->route('pengaduan.otp', $pengaduan)
            ->with($notifikasiTerkirim ? 'success' : 'warning', $pesan);
    }

    public function tampilOtp(Pengaduan $pengaduan): View
    {
        abort_if($pengaduan->terverifikasi_at, 404);

        return view('pengaduan.otp', ['pengaduan' => $pengaduan, 'otpDemo' => session('otp_demo_'.$pengaduan->id)]);
    }

    public function verifikasiOtp(Request $request, Pengaduan $pengaduan): RedirectResponse
    {
        $request->validate(['otp' => ['required', 'digits:6']]);

        if (! $pengaduan->otp_kedaluwarsa_at || $pengaduan->otp_kedaluwarsa_at->isPast() || ! Hash::check($request->otp, $pengaduan->otp_hash)) {
            return back()->withErrors(['otp' => 'OTP tidak valid atau sudah kedaluwarsa.']);
        }

        $nomorTiket = $this->buatNomorTiket();
        $pengaduan->update([
            'nomor_tiket' => $nomorTiket,
            'status' => 'menunggu_verifikasi',
            'terverifikasi_at' => now(),
            'otp_hash' => null,
            'otp_kedaluwarsa_at' => null,
        ]);
        RiwayatStatusPengaduan::create(['pengaduan_id' => $pengaduan->id, 'status' => 'menunggu_verifikasi', 'catatan' => 'Pengaduan berhasil diverifikasi pelapor.']);
        $this->notifikasiService->pengaduanBerhasil($pengaduan);
        $this->logAktivitasService->catat('pengaduan_terverifikasi', 'Nomor tiket: '.$pengaduan->nomor_tiket);

        return redirect()
            ->to(route('pengaduan.sukses', $pengaduan, false), 303)
            ->with('success', 'Pengaduan berhasil dikirim.');
    }

    public function sukses(Pengaduan $pengaduan): View
    {
        abort_unless($pengaduan->nomor_tiket, 404);

        return view('pengaduan.sukses', compact('pengaduan'));
    }

    public function formTracking(): View
    {
        return view('tracking.form');
    }

    public function trackingPublik(string $nomorTiket): View
    {
        $pengaduan = Pengaduan::with('riwayatStatus')
            ->where('nomor_tiket', $nomorTiket)
            ->firstOrFail();

        return view('tracking.hasil', ['pengaduan' => $pengaduan, 'modePublik' => true]);
    }

    public function tracking(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'nomor_tiket' => ['required', 'string'],
            'nomor_whatsapp' => ['required', 'string'],
        ]);

        $pengaduan = Pengaduan::with('riwayatStatus')
            ->where('nomor_tiket', $data['nomor_tiket'])
            ->where('nomor_whatsapp', $data['nomor_whatsapp'])
            ->first();

        if (! $pengaduan) {
            return back()->withErrors(['nomor_tiket' => 'Data pengaduan tidak ditemukan. Pastikan nomor tiket dan WhatsApp benar.']);
        }

        return view('tracking.hasil', compact('pengaduan'));
    }

    private function buatNomorTiket(): string
    {
        $tahun = now()->year;
        $jumlah = Pengaduan::whereYear('created_at', $tahun)->whereNotNull('nomor_tiket')->count() + 1;

        return 'PPA-'.$tahun.'-'.str_pad((string) $jumlah, 4, '0', STR_PAD_LEFT);
    }
}
