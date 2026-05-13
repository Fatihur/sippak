<?php

namespace App\Services;

use App\Models\LogNotifikasiWhatsApp;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NotifikasiService
{
    public function __construct(
        private readonly WhatsAppGatewayService $whatsAppGatewayService,
    ) {}

    public function kirimOtp(Pengaduan $pengaduan, string $otp): void
    {
        $pesan = "Kode OTP SIPPAK Anda adalah {$otp}. Kode berlaku 15 menit. Jangan bagikan kode ini kepada siapa pun.";
        $this->kirimEmail($pengaduan->email_pelapor, 'Kode OTP SIPPAK', $pesan);
        $this->kirimWhatsApp($pengaduan, $pengaduan->nomor_whatsapp, $pesan, 'otp');
        $pengaduan->forceFill(['notifikasi_terakhir_at' => now()])->saveQuietly();
    }

    public function pengaduanBerhasil(Pengaduan $pengaduan): void
    {
        $trackingUrl = route('tracking.form');
        $pesan = "Pengaduan SIPPAK berhasil dikirim.\n\nNomor tiket: {$pengaduan->nomor_tiket}\nStatus: Menunggu Verifikasi\n\nSimpan nomor tiket ini untuk tracking laporan: {$trackingUrl}";
        $this->kirimEmail($pengaduan->email_pelapor, 'Pengaduan SIPPAK Berhasil Dikirim', $pesan);
        $this->kirimWhatsApp($pengaduan, $pengaduan->nomor_whatsapp, $pesan, 'pengaduan_berhasil');
        $pengaduan->forceFill(['notifikasi_terakhir_at' => now()])->saveQuietly();
    }

    public function statusBerubah(Pengaduan $pengaduan, ?string $catatan = null): void
    {
        $labelStatus = $pengaduan->status_label;
        $pesan = "Update status pengaduan SIPPAK.\n\nNomor tiket: {$pengaduan->nomor_tiket}\nStatus saat ini: {$labelStatus}";
        if ($catatan) {
            $pesan .= "\nCatatan: {$catatan}";
        }
        $pesan .= "\n\nSilakan cek perkembangan melalui halaman tracking SIPPAK.";

        $this->kirimEmail($pengaduan->email_pelapor, 'Perubahan Status Pengaduan SIPPAK', $pesan);
        $this->kirimWhatsApp($pengaduan, $pengaduan->nomor_whatsapp, $pesan, 'status_berubah');
        $pengaduan->forceFill(['notifikasi_terakhir_at' => now()])->saveQuietly();
    }

    public function revisiDiminta(Pengaduan $pengaduan, ?string $catatan = null): void
    {
        $pesan = "Pengaduan SIPPAK {$pengaduan->nomor_tiket} memerlukan revisi data.";
        if ($catatan) {
            $pesan .= "\nCatatan: {$catatan}";
        }
        $this->kirimWhatsApp($pengaduan, $pengaduan->nomor_whatsapp, $pesan, 'revisi');
    }

    public function laporanSelesai(Pengaduan $pengaduan, ?string $catatan = null): void
    {
        $pesan = "Pengaduan SIPPAK {$pengaduan->nomor_tiket} telah selesai ditangani.";
        if ($catatan) {
            $pesan .= "\nCatatan: {$catatan}";
        }
        $this->kirimWhatsApp($pengaduan, $pengaduan->nomor_whatsapp, $pesan, 'selesai');
    }

    private function kirimEmail(?string $email, string $subjek, string $pesan): void
    {
        if (! $email) {
            return;
        }

        try {
            Mail::raw($pesan, function ($message) use ($email, $subjek): void {
                $message->to($email)->subject($subjek);
            });
        } catch (Throwable $e) {
            Log::warning('Gagal mengirim email SIPPAK', ['email' => $email, 'error' => $e->getMessage()]);
        }
    }

    private function kirimWhatsApp(Pengaduan $pengaduan, string $nomorWhatsApp, string $pesan, string $jenis): bool
    {
        $log = LogNotifikasiWhatsApp::create([
            'pengaduan_id' => $pengaduan->id,
            'nomor_tujuan' => $nomorWhatsApp,
            'jenis' => $jenis,
            'status' => 'pending',
            'pesan' => $pesan,
        ]);

        try {
            $result = $this->whatsAppGatewayService->kirimPesan($nomorWhatsApp, $pesan);
            $success = (bool) ($result['success'] ?? false);
            $log->update([
                'status' => $success ? 'terkirim' : 'gagal',
                'response' => $result,
                'error' => $success ? null : ($result['message'] ?? 'Gateway merespon gagal.'),
                'terkirim_at' => $success ? now() : null,
            ]);

            if (! $success) {
                Log::warning('WhatsApp Gateway SIPPAK merespon gagal', ['nomor' => $nomorWhatsApp, 'response' => $result]);
            }

            return $success;
        } catch (Throwable $e) {
            $log->update(['status' => 'gagal', 'error' => $e->getMessage()]);
            Log::warning('Gagal mengirim WhatsApp SIPPAK', ['nomor' => $nomorWhatsApp, 'error' => $e->getMessage()]);

            return false;
        }
    }
}
