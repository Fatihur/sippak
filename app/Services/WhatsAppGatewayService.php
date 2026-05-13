<?php

namespace App\Services;

use App\Models\PengaturanAplikasi;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class WhatsAppGatewayService
{
    public function baseUrl(): string
    {
        return rtrim(PengaturanAplikasi::ambil('wa_gateway_url', config('services.whatsapp.url') ?: 'http://127.0.0.1:3010'), '/');
    }

    public function token(): string
    {
        return PengaturanAplikasi::ambil('wa_gateway_token', config('services.whatsapp.token') ?: 'sippak-local-token') ?? 'sippak-local-token';
    }

    public function status(): array
    {
        return $this->request('get', '/status')->json() ?? ['success' => false, 'message' => 'Tidak ada respon gateway.'];
    }

    public function qr(): array
    {
        return $this->request('get', '/qr')->json() ?? ['success' => false, 'message' => 'Tidak ada QR.'];
    }

    public function kirimPesan(string $nomor, string $pesan): array
    {
        return $this->request('post', '/send-message', ['nomor' => $nomor, 'pesan' => $pesan])->json() ?? ['success' => false];
    }

    public function restart(): array
    {
        return $this->request('post', '/restart')->json() ?? ['success' => false];
    }

    public function logout(): array
    {
        return $this->request('post', '/logout')->json() ?? ['success' => false];
    }

    private function request(string $method, string $path, array $payload = []): Response
    {
        return Http::timeout(10)
            ->acceptJson()
            ->withToken($this->token())
            ->{$method}($this->baseUrl().$path, $payload);
    }
}
