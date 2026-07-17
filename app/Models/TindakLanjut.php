<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TindakLanjut extends Model
{
    protected $table = 'tindak_lanjut';

    protected $fillable = [
        'pengaduan_id', 'user_id', 'tanggal_penanganan', 'hasil_penanganan',
        'keterangan', 'status_penanganan', 'berita_acara', 'dokumentasi', 'dokumen_lain',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_penanganan' => 'date',
        ];
    }

    public function pengaduan(): BelongsTo
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
