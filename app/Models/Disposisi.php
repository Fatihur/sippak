<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disposisi extends Model
{
    protected $table = 'disposisi';

    protected $fillable = [
        'pengaduan_id', 'nomor_disposisi', 'dari_user_id', 'untuk_user_id',
        'tingkat', 'tanggal_disposisi', 'prioritas', 'instruksi',
        'arahan_pelaksanaan', 'nama_petugas', 'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_disposisi' => 'date',
        ];
    }

    public function pengaduan(): BelongsTo
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id');
    }

    public function dariUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dari_user_id');
    }

    public function untukUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'untuk_user_id');
    }

    public static function opsiPrioritas(): array
    {
        return [
            'biasa' => 'Biasa',
            'penting' => 'Penting',
            'sangat_mendesak' => 'Sangat Mendesak',
        ];
    }

    public function labelPrioritas(): string
    {
        return self::opsiPrioritas()[$this->prioritas] ?? '-';
    }
}
