<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogNotifikasiWhatsApp;
use App\Models\PengaturanAplikasi;
use App\Services\LogAktivitasService;
use App\Services\WhatsAppGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppController extends Controller
{
    public function __construct(
        private readonly WhatsAppGatewayService $whatsAppGatewayService,
        private readonly LogAktivitasService $logAktivitasService,
    ) {}

    public function index(): View
    {
        $status = ['success' => false, 'status' => 'unreachable', 'message' => 'Gateway tidak dapat dihubungi.'];
        $qr = ['qr' => null];

        try {
            $status = $this->whatsAppGatewayService->status();
            $qr = $this->whatsAppGatewayService->qr();
        } catch (\Throwable $e) {
            $status['message'] = $e->getMessage();
        }

        return view('admin.whatsapp.index', [
            'gatewayUrl' => $this->whatsAppGatewayService->baseUrl(),
            'gatewayToken' => $this->whatsAppGatewayService->token(),
            'status' => $status,
            'qr' => $qr,
            'logs' => LogNotifikasiWhatsApp::with('pengaduan')->latest()->limit(15)->get(),
        ]);
    }

    public function simpan(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'wa_gateway_url' => ['required', 'url'],
            'wa_gateway_token' => ['required', 'string', 'min:8'],
        ]);

        PengaturanAplikasi::simpan('wa_gateway_url', rtrim($data['wa_gateway_url'], '/'));
        PengaturanAplikasi::simpan('wa_gateway_token', $data['wa_gateway_token'], true);
        $this->logAktivitasService->catat('pengaturan_whatsapp_disimpan', 'URL gateway WhatsApp diperbarui.');

        return back()->with('success', 'Pengaturan WhatsApp Gateway berhasil disimpan.');
    }

    public function testKirim(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nomor_tujuan' => ['required', 'string', 'max:30'],
            'pesan_test' => ['required', 'string', 'max:1000'],
        ]);

        $result = $this->whatsAppGatewayService->kirimPesan($data['nomor_tujuan'], $data['pesan_test']);
        if (! ($result['success'] ?? false)) {
            return back()->withErrors(['nomor_tujuan' => $result['message'] ?? 'Gagal mengirim pesan test.']);
        }

        return back()->with('success', 'Pesan test berhasil dikirim.');
    }

    public function restart(): RedirectResponse
    {
        $this->whatsAppGatewayService->restart();

        return back()->with('success', 'Perintah restart koneksi WhatsApp dikirim.');
    }

    public function logout(): RedirectResponse
    {
        $this->whatsAppGatewayService->logout();

        return back()->with('success', 'Perintah logout WhatsApp dikirim.');
    }
}
