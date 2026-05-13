<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogNotifikasiWhatsApp extends Model
{
    protected $table = 'log_notifikasi_whatsapp';

    protected $fillable = ['pengaduan_id', 'nomor_tujuan', 'jenis', 'status', 'pesan', 'response', 'error', 'terkirim_at'];

    protected function casts(): array
    {
        return [
            'response' => 'array',
            'terkirim_at' => 'datetime',
        ];
    }

    public function pengaduan(): BelongsTo
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id');
    }
}
