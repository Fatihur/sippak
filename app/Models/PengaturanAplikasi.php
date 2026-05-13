<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PengaturanAplikasi extends Model
{
    protected $table = 'pengaturan_aplikasi';

    protected $fillable = ['kunci', 'nilai', 'rahasia'];

    protected function casts(): array
    {
        return ['rahasia' => 'boolean'];
    }

    public static function ambil(string $kunci, ?string $default = null): ?string
    {
        $setting = self::where('kunci', $kunci)->first();
        if (! $setting) {
            return $default;
        }

        if ($setting->rahasia && filled($setting->nilai)) {
            return Crypt::decryptString($setting->nilai);
        }

        return $setting->nilai ?? $default;
    }

    public static function simpan(string $kunci, ?string $nilai, bool $rahasia = false): void
    {
        self::updateOrCreate(
            ['kunci' => $kunci],
            ['nilai' => $rahasia && filled($nilai) ? Crypt::encryptString($nilai) : $nilai, 'rahasia' => $rahasia]
        );
    }
}
