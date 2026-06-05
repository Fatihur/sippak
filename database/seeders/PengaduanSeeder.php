<?php

namespace Database\Seeders;

use App\Models\AsesmenAwal;
use App\Models\Pengaduan;
use App\Models\RiwayatStatusPengaduan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PengaduanSeeder extends Seeder
{
    public function run(): void
    {
        $operator = User::where('role', 'operator')->first();
        $kabid = User::where('role', 'kepala_bidang')->first();
        $operatorId = $operator?->id ?: 1;
        $kabidId = $kabid?->id ?: 2;

        $kecamatan = config('sippak.kecamatan', ['Sumbawa', 'Unter Iwes', 'Labuhan Badas', 'Alas', 'Lunyuk']);

        $jenisKekerasan = [
            'KDRT', 'Kekerasan seksual', 'Kekerasan fisik', 'Kekerasan verbal',
            'Penelantaran anak', 'Eksploitasi anak', 'Penganiayaan', 'Pelecehan', 'Lainnya'
        ];

        $dataPelapor = [
            ['nama' => 'Ahmad Fauzi', 'jk' => 'Laki-laki', 'hub' => 'Orang Tua'],
            ['nama' => 'Siti Aminah', 'jk' => 'Perempuan', 'hub' => 'Ibu Kandung'],
            ['nama' => 'Sri Wahyuni', 'jk' => 'Perempuan', 'hub' => 'Tante'],
            ['nama' => 'Budi Santoso', 'jk' => 'Laki-laki', 'hub' => 'Tetangga'],
            ['nama' => 'Rina Wijaya', 'jk' => 'Perempuan', 'hub' => 'Guru'],
            ['nama' => 'Eko Prasetyo', 'jk' => 'Laki-laki', 'hub' => 'Paman'],
            ['nama' => 'Dewi Lestari', 'jk' => 'Perempuan', 'hub' => 'Kerabat'],
            ['nama' => 'Andi Wijaya', 'jk' => 'Laki-laki', 'hub' => 'Kakak'],
            ['nama' => 'Mega Utami', 'jk' => 'Perempuan', 'hub' => 'Wali'],
            ['nama' => 'Hendra Setiawan', 'jk' => 'Laki-laki', 'hub' => 'Tetangga'],
        ];

        $dataKorban = [
            ['nama' => 'Doni', 'umur' => 8, 'jk' => 'Laki-laki'],
            ['nama' => 'Salsa', 'umur' => 12, 'jk' => 'Perempuan'],
            ['nama' => 'Rian', 'umur' => 10, 'jk' => 'Laki-laki'],
            ['nama' => 'Nabila', 'umur' => 14, 'jk' => 'Perempuan'],
            ['nama' => 'Aldi', 'umur' => 7, 'jk' => 'Laki-laki'],
            ['nama' => 'Indah', 'umur' => 15, 'jk' => 'Perempuan'],
            ['nama' => 'Bagas', 'umur' => 9, 'jk' => 'Laki-laki'],
            ['nama' => 'Putri', 'umur' => 11, 'jk' => 'Perempuan'],
            ['nama' => 'Zidan', 'umur' => 6, 'jk' => 'Laki-laki'],
            ['nama' => 'Kiki', 'umur' => 13, 'jk' => 'Perempuan'],
        ];

        $kronologiContoh = [
            'Telah terjadi dugaan kekerasan fisik yang dilakukan secara berulang oleh terlapor terhadap korban di lingkungan rumah. Korban mengalami lebam di bagian tangan.',
            'Dugaan perlakuan tidak menyenangkan dan pelecehan verbal di sekolah oleh oknum tertentu yang mengakibatkan trauma psikologis mendalam bagi anak.',
            'Korban ditelantarkan oleh orang tuanya selama lebih dari 3 bulan tanpa diberikan nafkah lahir dan batin serta pendidikan yang layak.',
            'Terjadi dugaan kekerasan seksual yang dilakukan oleh orang dekat korban di rumah saat situasi sepi. Korban mengalami kecemasan ekstrem.',
            'Korban dipaksa bekerja paruh waktu hingga malam hari (eksploitasi anak) oleh pihak keluarga dan sering mendapatkan kekerasan verbal jika menolak.',
        ];

        $statuses = [
            'menunggu_verifikasi', 'diterima', 'revisi', 'asesmen_awal',
            'dalam_penanganan', 'pendampingan', 'dirujuk', 'selesai', 'ditolak'
        ];

        $urgencies = ['tinggi', 'sedang', 'rendah'];

        // Buat 25 data pengaduan secara variatif sepanjang tahun 2026
        for ($i = 1; $i <= 25; $i++) {
            $pelapor = $dataPelapor[array_rand($dataPelapor)];
            $korban = $dataKorban[array_rand($dataKorban)];
            $status = $statuses[$i % count($statuses)];
            $urgency = $urgencies[$i % count($urgencies)];
            $kec = $kecamatan[array_rand($kecamatan)];
            $jenis = $jenisKekerasan[array_rand($jenisKekerasan)];

            // Sebar tanggal dibuat dari Januari sampai Juni 2026
            $bulan = ($i % 6) + 1; // 1 s.d 6
            $hari = ($i * 7) % 28 + 1;
            $createdAt = Carbon::create(2026, $bulan, $hari, 10, 0, 0);

            $pengaduan = Pengaduan::create([
                'nomor_tiket' => 'PPA-2026-' . str_pad((string)$i, 4, '0', STR_PAD_LEFT),
                'otp_hash' => null,
                'otp_kedaluwarsa_at' => null,
                'terverifikasi_at' => $createdAt,
                'nama_pelapor' => $pelapor['nama'],
                'nik_pelapor' => '5204' . str_pad((string)rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT),
                'jenis_kelamin_pelapor' => $pelapor['jk'],
                'nomor_whatsapp' => '0812' . rand(10000000, 99999999),
                'email_pelapor' => strtolower(str_replace(' ', '', $pelapor['nama'])) . '@example.com',
                'alamat_pelapor' => 'Dusun Baru, RT 02 / RW 01, Kecamatan ' . $kec,
                'kecamatan' => $kec,
                'nama_korban' => $korban['nama'],
                'umur_korban' => $korban['umur'],
                'jenis_kelamin_korban' => $korban['jk'],
                'hubungan_dengan_pelapor' => $pelapor['hub'],
                'jenis_kekerasan' => $jenis,
                'lokasi_kejadian' => 'Lingkungan sekitar rumah pelaku',
                'tanggal_kejadian' => $createdAt->copy()->subDays(rand(1, 5))->toDateString(),
                'kronologi_kejadian' => $kronologiContoh[$i % count($kronologiContoh)] . ' Mohon segera ditindaklanjuti demi keselamatan korban.',
                'persetujuan_kerahasiaan' => true,
                'status' => $status,
                'tingkat_urgensi' => $urgency,
                'catatan_umum' => $status === 'selesai' ? 'Kasus telah diselesaikan secara kekeluargaan dan korban dalam pengawasan kerabat.' : null,
                'operator_id' => in_array($status, ['menunggu_verifikasi', 'revisi']) ? null : $operatorId,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(rand(1, 3)),
            ]);

            // Seeding Riwayat Status
            RiwayatStatusPengaduan::create([
                'pengaduan_id' => $pengaduan->id,
                'status' => 'menunggu_verifikasi',
                'catatan' => 'Laporan dikirim oleh pelapor.',
                'user_id' => null,
                'created_at' => $createdAt,
            ]);

            if ($status !== 'menunggu_verifikasi' && $status !== 'ditolak') {
                RiwayatStatusPengaduan::create([
                    'pengaduan_id' => $pengaduan->id,
                    'status' => 'diterima',
                    'catatan' => 'Laporan diverifikasi dan diterima oleh petugas.',
                    'user_id' => $operatorId,
                    'created_at' => $createdAt->copy()->addHours(12),
                ]);
            }

            if (in_array($status, ['asesmen_awal', 'dalam_penanganan', 'pendampingan', 'dirujuk', 'selesai'])) {
                RiwayatStatusPengaduan::create([
                    'pengaduan_id' => $pengaduan->id,
                    'status' => 'asesmen_awal',
                    'catatan' => 'Telah dilakukan asesmen awal kondisi korban.',
                    'user_id' => $operatorId,
                    'created_at' => $createdAt->copy()->addDays(1),
                ]);

                // Buat data asesmen
                AsesmenAwal::create([
                    'pengaduan_id' => $pengaduan->id,
                    'kondisi_korban' => 'Korban mengalami trauma tingkat sedang dan membutuhkan perlindungan lingkungan aman.',
                    'tingkat_risiko' => $urgency,
                    'kebutuhan_korban' => 'Pendampingan psikologis terpadu dan tempat aman sementara.',
                    'pendampingan_hukum' => $i % 2 === 0,
                    'pendampingan_psikologis' => true,
                    'catatan_operator' => 'Segera hubungi tim psikolog PPA.',
                    'operator_id' => $operatorId,
                    'created_at' => $createdAt->copy()->addDays(1),
                ]);
            }

            if (in_array($status, ['dalam_penanganan', 'pendampingan', 'dirujuk', 'selesai'])) {
                RiwayatStatusPengaduan::create([
                    'pengaduan_id' => $pengaduan->id,
                    'status' => 'dalam_penanganan',
                    'catatan' => 'Penanganan kasus sedang berjalan bekerjasama dengan mitra terkait.',
                    'user_id' => $operatorId,
                    'created_at' => $createdAt->copy()->addDays(2),
                ]);
            }

            if ($status === 'selesai') {
                RiwayatStatusPengaduan::create([
                    'pengaduan_id' => $pengaduan->id,
                    'status' => 'selesai',
                    'catatan' => 'Kasus ditutup secara resmi.',
                    'user_id' => $operatorId,
                    'created_at' => $createdAt->copy()->addDays(7),
                ]);
            }

            if ($status === 'ditolak') {
                RiwayatStatusPengaduan::create([
                    'pengaduan_id' => $pengaduan->id,
                    'status' => 'ditolak',
                    'catatan' => 'Laporan ditolak karena data kronologi tidak konsisten.',
                    'user_id' => $operatorId,
                    'created_at' => $createdAt->copy()->addHours(6),
                ]);
            }

            // Tambahkan Catatan Kabid PPA untuk beberapa kasus aktif bermasalah
            if ($i % 5 === 0 && in_array($status, ['diterima', 'asesmen_awal', 'dalam_penanganan'])) {
                $arahanKabid = [
                    'Pastikan koordinasi dengan Polsek kecamatan ' . $kec . ' berjalan lancar.',
                    'Prioritaskan pemulihan psikologis anak terlebih dahulu.',
                    'Segera siapkan berkas laporan untuk rujukan tingkat lanjut jika diperlukan.',
                    'Lakukan kunjungan lapangan berkala untuk memantau keamanan korban.',
                ];
                RiwayatStatusPengaduan::create([
                    'pengaduan_id' => $pengaduan->id,
                    'status' => $status,
                    'catatan' => 'Catatan Kabid PPA: ' . $arahanKabid[$i % count($arahanKabid)],
                    'user_id' => $kabidId,
                    'created_at' => $createdAt->copy()->addDays(3),
                ]);
            }
        }
    }
}
