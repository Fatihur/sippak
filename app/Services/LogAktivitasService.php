<?php

namespace App\Services;

use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class LogAktivitasService
{
    public function catat(string $aksi, ?string $keterangan = null): void
    {
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aksi' => $aksi,
            'keterangan' => $keterangan,
            'ip_address' => request()?->ip(),
        ]);
    }
}
