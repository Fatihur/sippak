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

    public function test_label_role_user_sesuai_aktor_internal(): void
    {
        $this->assertSame('Admin/Operator', User::roleLabel('operator'));
        $this->assertSame('Kabid PPA', User::roleLabel('kepala_bidang'));
        $this->assertSame('Kepala Dinas P2KBP3A', User::roleLabel('kepala_dinas'));
        $this->assertSame('Role Tidak Dikenal', User::roleLabel('pelapor'));
        $this->assertSame([
            'operator' => 'Admin/Operator',
            'kepala_bidang' => 'Kabid PPA',
            'kepala_dinas' => 'Kepala Dinas P2KBP3A',
        ], User::opsiRole());
    }

    public function test_kabid_dan_kepala_dinas_dapat_memonitor_laporan_tanpa_aksi_operasional(): void
    {
        $pengaduan = Pengaduan::create($this->dataPengaduan([
            'nomor_tiket' => 'PPA-2026-0003',
            'status' => 'diterima',
        ]));

        foreach (['kepala_bidang', 'kepala_dinas'] as $role) {
            $user = User::factory()->create(['role' => $role, 'aktif' => true]);

            $this->actingAs($user)
                ->get(route('admin.dashboard'))
                ->assertOk();

            $this->actingAs($user)
                ->get(route('admin.laporan.index'))
                ->assertOk()
                ->assertSee($pengaduan->nomor_tiket)
                ->assertDontSee('Edit')
                ->assertDontSee('Hapus');

            $this->actingAs($user)
                ->get(route('admin.laporan.show', $pengaduan))
                ->assertOk()
                ->assertSee('Mode Monitoring')
                ->assertDontSee('Simpan Status')
                ->assertDontSee('Simpan Asesmen')
                ->assertDontSee('Kirim Panggilan');

            $this->actingAs($user)
                ->get(route('admin.rekap.index'))
                ->assertOk()
                ->assertSee('Export PDF / Cetak')
                ->assertDontSee('Export Excel/CSV')
                ->assertDontSee('Backup Database');
        }
    }

    public function test_kabid_dan_kepala_dinas_tidak_dapat_melakukan_aksi_operasional(): void
    {
        $pengaduan = Pengaduan::create($this->dataPengaduan([
            'nomor_tiket' => 'PPA-2026-0004',
            'status' => 'menunggu_verifikasi',
        ]));

        foreach (['kepala_bidang', 'kepala_dinas'] as $role) {
            $user = User::factory()->create(['role' => $role, 'aktif' => true]);

            $this->actingAs($user)
                ->patch(route('admin.laporan.status', $pengaduan), [
                    'status' => 'diterima',
                    'tingkat_urgensi' => 'tinggi',
                    'catatan' => 'Tidak boleh berubah oleh role monitoring.',
                ])
                ->assertForbidden();

            $this->actingAs($user)
                ->get(route('admin.pengguna.index'))
                ->assertForbidden();

            $this->actingAs($user)
                ->get(route('admin.whatsapp.index'))
                ->assertForbidden();

            $this->actingAs($user)
                ->get(route('admin.rekap.export-csv'))
                ->assertForbidden();

            $this->actingAs($user)
                ->get(route('admin.backup.sqlite'))
                ->assertForbidden();
        }

        $pengaduan->refresh();
        $this->assertSame('menunggu_verifikasi', $pengaduan->status);
        $this->assertSame(0, $pengaduan->riwayatStatus()->count());
    }

    public function test_pelapor_tidak_memiliki_akses_login_admin(): void
    {
        $pelapor = User::factory()->create([
            'email' => 'pelapor-test@sippak.test',
            'password' => 'password',
            'role' => 'pelapor',
            'aktif' => true,
        ]);

        $this->post(route('login.proses'), [
            'email' => $pelapor->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
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
