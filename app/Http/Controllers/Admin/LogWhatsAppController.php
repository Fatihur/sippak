<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogNotifikasiWhatsApp;
use App\Services\WhatsAppGatewayService;
use Illuminate\Http\RedirectResponse;

class LogWhatsAppController extends Controller
{
    public function __construct(
        private readonly WhatsAppGatewayService $whatsAppGatewayService,
    ) {}

    public function kirimUlang(LogNotifikasiWhatsApp $log): RedirectResponse
    {
        $result = $this->whatsAppGatewayService->kirimPesan($log->nomor_tujuan, $log->pesan);
        $success = (bool) ($result['success'] ?? false);

        $log->update([
            'status' => $success ? 'terkirim' : 'gagal',
            'response' => $result,
            'error' => $success ? null : ($result['message'] ?? 'Gateway merespon gagal.'),
            'terkirim_at' => $success ? now() : null,
        ]);

        return back()->with($success ? 'success' : 'error', $success ? 'Pesan berhasil dikirim ulang.' : 'Pesan gagal dikirim ulang.');
    }
}
