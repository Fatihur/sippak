<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengaduan extends Model
{
    use HasFactory;

    protected $table = 'pengaduan';

    protected $fillable = [
        'nomor_tiket', 'otp_hash', 'otp_kedaluwarsa_at', 'terverifikasi_at',
        'nama_pelapor', 'nik_pelapor', 'jenis_kelamin_pelapor', 'nomor_whatsapp', 'email_pelapor', 'alamat_pelapor', 'kecamatan',
        'nama_korban', 'umur_korban', 'jenis_kelamin_korban', 'hubungan_dengan_pelapor',
        'jenis_kekerasan', 'lokasi_kejadian', 'tanggal_kejadian', 'kronologi_kejadian', 'persetujuan_kerahasiaan',
        'status', 'tingkat_urgensi', 'catatan_umum', 'notifikasi_terakhir_at', 'operator_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kejadian' => 'date',
            'persetujuan_kerahasiaan' => 'boolean',
            'otp_kedaluwarsa_at' => 'datetime',
            'terverifikasi_at' => 'datetime',
            'notifikasi_terakhir_at' => 'datetime',
        ];
    }

    public function bukti(): HasMany
    {
        return $this->hasMany(BuktiPengaduan::class, 'pengaduan_id');
    }

    public function riwayatStatus(): HasMany
    {
        return $this->hasMany(RiwayatStatusPengaduan::class, 'pengaduan_id')->latest();
    }

    public function asesmenAwal(): HasOne
    {
        return $this->hasOne(AsesmenAwal::class, 'pengaduan_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function disposisi(): HasMany
    {
        return $this->hasMany(Disposisi::class, 'pengaduan_id');
    }

    public function disposisiKadis(): HasOne
    {
        return $this->hasOne(Disposisi::class, 'pengaduan_id')->where('tingkat', 'kadis')->latest();
    }

    public function disposisiKabid(): HasOne
    {
        return $this->hasOne(Disposisi::class, 'pengaduan_id')->where('tingkat', 'kabid')->latest();
    }

    public function tindakLanjut(): HasMany
    {
        return $this->hasMany(TindakLanjut::class, 'pengaduan_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::labelStatus($this->status);
    }

    public static function labelStatus(string $status): string
    {
        return str($status)->replace('_', ' ')->title()->toString();
    }

    public static function opsiStatus(): array
    {
        return [
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'diterima' => 'Diterima',
            'revisi' => 'Revisi',
            'menunggu_disposisi_kadis' => 'Menunggu Disposisi Kepala Dinas',
            'didisposisikan_ke_kabid' => 'Didisposisikan ke Kabid',
            'menunggu_tindak_lanjut_operator' => 'Menunggu Tindak Lanjut Operator',
            'asesmen_awal' => 'Asesmen Awal',
            'dalam_penanganan' => 'Dalam Penanganan',
            'pendampingan' => 'Pendampingan',
            'dirujuk' => 'Dirujuk',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];
    }
}
