<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuktiPengaduan extends Model
{
    protected $table = 'bukti_pengaduan';

    protected $fillable = ['pengaduan_id', 'nama_asli', 'path_file', 'mime_type', 'ukuran_file'];

    public function pengaduan(): BelongsTo
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id');
    }
}
