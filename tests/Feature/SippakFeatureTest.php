<?php

namespace Tests\Feature;

use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SippakFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_memproses_login_petugas_custom(): void
    {
        $user = User::factory()->create([
            'email' => 'operator-test@sippak.test',
            'password' => 'password',
            'role' => 'operator',
            'aktif' => true,
        ]);

        $this->post(route('login.proses'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_memvalidasi_form_pengaduan_wajib(): void
    {
        $this->post(route('pengaduan.simpan'), [])
            ->assertSessionHasErrors(['nama_pelapor', 'nik_pelapor', 'nomor_whatsapp', 'nama_korban', 'kronologi_kejadian', 'persetujuan']);
    }

    public function test_membuat_pengaduan_dan_memverifikasi_otp_menjadi_nomor_tiket(): void
    {
        $response = $this->post(route('pengaduan.simpan'), $this->dataPengaduan());

        $pengaduan = Pengaduan::firstOrFail();
        $response->assertRedirect(route('pengaduan.otp', $pengaduan));
        $this->get(route('pengaduan.otp', $pengaduan))->assertOk()->assertSee('Verifikasi OTP');

        $otp = session('otp_demo_'.$pengaduan->id);
        $this->assertNotEmpty($otp);
        $this->post(route('pengaduan.verifikasi-otp', $pengaduan), ['otp' => $otp])
            ->assertRedirect(route('pengaduan.sukses', $pengaduan));

        $pengaduan->refresh();
        $this->assertStringStartsWith('PPA-', $pengaduan->nomor_tiket);
        $this->assertSame('menunggu_verifikasi', $pengaduan->status);
        $this->assertTrue(Hash::needsRehash($pengaduan->otp_hash ?? ''));
    }

    public function test_menampilkan_tracking_dengan_nomor_tiket_dan_whatsapp(): void
    {
        $pengaduan = Pengaduan::create($this->dataPengaduan([
            'nomor_tiket' => 'PPA-2026-9999',
            'status' => 'menunggu_verifikasi',
        ]));

        $this->post(route('tracking.hasil'), [
            'nomor_tiket' => $pengaduan->nomor_tiket,
            'nomor_whatsapp' => $pengaduan->nomor_whatsapp,
        ])->assertOk()->assertSee($pengaduan->nomor_tiket);
    }

    public function test_operator_dapat_mengubah_status_dan_mencatat_riwayat(): void
    {
        $operator = User::factory()->create(['role' => 'operator', 'aktif' => true]);
        $pengaduan = Pengaduan::create($this->dataPengaduan([
            'nomor_tiket' => 'PPA-2026-0002',
            'status' => 'menunggu_verifikasi',
        ]));

        $this->actingAs($operator)->patch(route('admin.laporan.status', $pengaduan), [
            'status' => 'diterima',
            'tingkat_urgensi' => 'tinggi',
            'catatan' => 'Diterima untuk diproses.',
        ])->assertRedirect();

        $pengaduan->refresh();
        $this->assertSame('diterima', $pengaduan->status);
        $this->assertSame(1, $pengaduan->riwayatStatus()->count());
    }

    private function dataPengaduan(array $override = []): array
    {
        return array_merge([
            'nama_pelapor' => 'Pelapor Uji',
            'nik_pelapor' => '1234567890123456',
            'jenis_kelamin_pelapor' => 'Perempuan',
            'nomor_whatsapp' => '08123456789',
            'email_pelapor' => null,
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
            'persetujuan' => '1',
        ], $override);
    }
}
