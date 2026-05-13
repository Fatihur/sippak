<?php

namespace Tests\Feature;

use App\Models\LogNotifikasiWhatsApp;
use App\Models\Pengaduan;
use App\Services\NotifikasiService;
use App\Services\WhatsAppGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_otp_mencatat_log_whatsapp_terkirim(): void
    {
        $this->mock(WhatsAppGatewayService::class, function ($mock): void {
            $mock->shouldReceive('kirimPesan')->once()->andReturn(['success' => true, 'message_id' => 'test-message']);
        });

        $pengaduan = Pengaduan::create($this->dataPengaduan());
        app(NotifikasiService::class)->kirimOtp($pengaduan, '123456');

        $log = LogNotifikasiWhatsApp::firstOrFail();
        $this->assertSame('otp', $log->jenis);
        $this->assertSame('terkirim', $log->status);
        $this->assertSame($pengaduan->id, $log->pengaduan_id);
    }

    private function dataPengaduan(): array
    {
        return [
            'nama_pelapor' => 'Pelapor Uji',
            'nik_pelapor' => '1234567890123456',
            'jenis_kelamin_pelapor' => 'Perempuan',
            'nomor_whatsapp' => '08123456789',
            'alamat_pelapor' => 'Alamat pelapor',
            'kecamatan' => 'Sumbawa',
            'nama_korban' => 'Korban Uji',
            'umur_korban' => 10,
            'jenis_kelamin_korban' => 'Perempuan',
            'hubungan_dengan_pelapor' => 'Keluarga',
            'jenis_kekerasan' => 'Kekerasan fisik',
            'lokasi_kejadian' => 'Sumbawa',
            'tanggal_kejadian' => now()->toDateString(),
            'kronologi_kejadian' => 'Kronologi kejadian pengaduan uji yang cukup panjang.',
            'status' => 'menunggu_otp',
        ];
    }
}
