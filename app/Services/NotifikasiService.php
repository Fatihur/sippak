<?php

namespace App\Services;

use App\Models\LogNotifikasiWhatsApp;
use App\Models\Pengaduan;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $pesan = "Kode OTP SILAPAK Anda adalah {$otp}. Kode berlaku 15 menit. Jangan bagikan kode ini kepada siapa pun.";
        $this->kirimEmail($pengaduan->email_pelapor, 'Kode OTP SILAPAK', $pesan);
        $this->kirimWhatsApp($pengaduan, $pengaduan->nomor_whatsapp, $pesan, 'otp');
        $pengaduan->forceFill(['notifikasi_terakhir_at' => now()])->saveQuietly();
    }

    public function pengaduanBerhasil(Pengaduan $pengaduan): void
    {
        $trackingUrl = url('/tracking');
        $nama = $pengaduan->nama_pelapor ?: 'Pelapor';
        $pesan = "Yth. {$nama}, laporan pengaduan SILAPAK berhasil dikirim.\n"
            ."Nomor tiket: {$pengaduan->nomor_tiket}\n"
            ."Status: Menunggu Verifikasi. Simpan nomor tiket ini untuk tracking laporan: {$trackingUrl}";

        $this->kirimEmail($pengaduan->email_pelapor, 'Pengaduan SILAPAK Berhasil Dikirim', $pesan);
        $this->kirimWhatsApp($pengaduan, $pengaduan->nomor_whatsapp, $pesan, 'pengaduan_berhasil');
        $pengaduan->forceFill(['notifikasi_terakhir_at' => now()])->saveQuietly();
    }

    public function statusBerubah(Pengaduan $pengaduan, ?string $catatan = null): void
    {
        $labelStatus = $pengaduan->status_label;
        $pesan = "Update status pengaduan SILAPAK.\n\nNomor tiket: {$pengaduan->nomor_tiket}\nStatus saat ini: {$labelStatus}";
        if ($catatan) {
            $pesan .= "\nCatatan: {$catatan}";
        }
        $pesan .= "\n\nSilakan cek perkembangan melalui halaman tracking SILAPAK.";

        $this->kirimEmail($pengaduan->email_pelapor, 'Perubahan Status Pengaduan SILAPAK', $pesan);
        $this->kirimWhatsApp($pengaduan, $pengaduan->nomor_whatsapp, $pesan, 'status_berubah');
        $pengaduan->forceFill(['notifikasi_terakhir_at' => now()])->saveQuietly();
    }

    public function revisiDiminta(Pengaduan $pengaduan, ?string $catatan = null): void
    {
        $pesan = "Pengaduan SILAPAK {$pengaduan->nomor_tiket} memerlukan revisi data.";
        if ($catatan) {
            $pesan .= "\nCatatan: {$catatan}";
        }
        $this->kirimWhatsApp($pengaduan, $pengaduan->nomor_whatsapp, $pesan, 'revisi');
    }

    public function laporanSelesai(Pengaduan $pengaduan, ?string $catatan = null): void
    {
        $pesan = "Pengaduan SILAPAK {$pengaduan->nomor_tiket} telah selesai ditangani.";
        if ($catatan) {
            $pesan .= "\nCatatan: {$catatan}";
        }
        $this->kirimWhatsApp($pengaduan, $pengaduan->nomor_whatsapp, $pesan, 'selesai');
    }

    public function panggilKeKantor(Pengaduan $pengaduan, ?string $catatan = null): void
    {
        $nama = $pengaduan->nama_pelapor ?: 'Pelapor';
        $pesan = "Yth. {$nama}, laporan SILAPAK nomor {$pengaduan->nomor_tiket} sudah diproses.\n"
            ."Mohon segera datang ke kantor DP2KBP3A Kabupaten Sumbawa untuk tindak lanjut.";
        if ($catatan) {
            $pesan .= "\nCatatan: {$catatan}";
        }

        $this->kirimEmail($pengaduan->email_pelapor, 'Undangan Tindak Lanjut Laporan SILAPAK', $pesan);

        $dokumen = $this->generateBuktiPengaduanPdf($pengaduan);
        if ($dokumen !== null) {
            $this->kirimWhatsAppDenganDokumen(
                $pengaduan,
                $pengaduan->nomor_whatsapp,
                $pesan,
                'panggilan_kantor',
                $dokumen['base64'],
                $dokumen['nama_file'],
                $dokumen['mime_type']
            );
        } else {
            $this->kirimWhatsApp($pengaduan, $pengaduan->nomor_whatsapp, $pesan, 'panggilan_kantor');
        }

        $pengaduan->forceFill(['notifikasi_terakhir_at' => now()])->saveQuietly();
    }

    /**
     * Render bukti pengaduan sebagai PDF (nota/struk digital).
     *
     * @return array{base64:string,nama_file:string,mime_type:string}|null
     */
    private function generateBuktiPengaduanPdf(Pengaduan $pengaduan): ?array
    {
        try {
            $pdf = Pdf::loadView('pdf.bukti-pengaduan', ['pengaduan' => $pengaduan])
                ->setPaper('a5', 'portrait');

            return [
                'base64' => base64_encode($pdf->output()),
                'nama_file' => "Bukti-Pengaduan-{$pengaduan->nomor_tiket}.pdf",
                'mime_type' => 'application/pdf',
            ];
        } catch (Throwable $e) {
            Log::warning('Gagal membuat PDF bukti pengaduan', [
                'pengaduan_id' => $pengaduan->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
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
            Log::warning('Gagal mengirim email SILAPAK', ['email' => $email, 'error' => $e->getMessage()]);
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
                Log::warning('WhatsApp Gateway SILAPAK merespon gagal', ['nomor' => $nomorWhatsApp, 'response' => $result]);
            }

            return $success;
        } catch (Throwable $e) {
            $log->update(['status' => 'gagal', 'error' => $e->getMessage()]);
            Log::warning('Gagal mengirim WhatsApp SILAPAK', ['nomor' => $nomorWhatsApp, 'error' => $e->getMessage()]);

            return false;
        }
    }

    private function kirimWhatsAppDenganDokumen(
        Pengaduan $pengaduan,
        string $nomorWhatsApp,
        string $pesan,
        string $jenis,
        string $base64,
        string $namaFile,
        string $mimeType,
    ): bool {
        $log = LogNotifikasiWhatsApp::create([
            'pengaduan_id' => $pengaduan->id,
            'nomor_tujuan' => $nomorWhatsApp,
            'jenis' => $jenis,
            'status' => 'pending',
            'pesan' => $pesan."\n\n[Lampiran: {$namaFile}]",
        ]);

        try {
            $result = $this->whatsAppGatewayService->kirimDokumen(
                $nomorWhatsApp,
                $pesan,
                $base64,
                $namaFile,
                $mimeType
            );
            $success = (bool) ($result['success'] ?? false);
            $log->update([
                'status' => $success ? 'terkirim' : 'gagal',
                'response' => $result,
                'error' => $success ? null : ($result['message'] ?? 'Gateway merespon gagal.'),
                'terkirim_at' => $success ? now() : null,
            ]);

            if (! $success) {
                Log::warning('WhatsApp Gateway SILAPAK (dokumen) merespon gagal', ['nomor' => $nomorWhatsApp, 'response' => $result]);
            }

            return $success;
        } catch (Throwable $e) {
            $log->update(['status' => 'gagal', 'error' => $e->getMessage()]);
            Log::warning('Gagal mengirim WhatsApp SILAPAK (dokumen)', ['nomor' => $nomorWhatsApp, 'error' => $e->getMessage()]);

            return false;
        }
    }
}