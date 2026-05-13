<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsesmenAwal extends Model
{
    protected $table = 'asesmen_awal';

    protected $fillable = [
        'pengaduan_id', 'kondisi_korban', 'tingkat_risiko', 'kebutuhan_korban',
        'pendampingan_hukum', 'pendampingan_psikologis', 'catatan_operator', 'operator_id',
    ];

    protected function casts(): array
    {
        return [
            'pendampingan_hukum' => 'boolean',
            'pendampingan_psikologis' => 'boolean',
        ];
    }

    public function pengaduan(): BelongsTo
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
