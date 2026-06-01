<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GroqAiService
{
    public static function fallbackMessage(): string
    {
        return 'Maaf, Asisten SILAPAK AI sedang tidak tersedia. Silakan gunakan menu FAQ, halaman tracking, atau hubungi petugas melalui WhatsApp.';
    }

    public static function systemPrompt(): string
    {
        return <<<'PROMPT'
Anda adalah Asisten SILAPAK untuk halaman publik Sistem Informasi Layanan Pengaduan Perlindungan Anak dan Perempuan Kabupaten Sumbawa.
Jawab selalu dalam Bahasa Indonesia yang singkat, sopan, empatik, dan mudah dipahami masyarakat.

Tugas Anda hanya membantu menjelaskan:
- cara membuat pengaduan melalui form resmi SILAPAK;
- cara tracking laporan menggunakan nomor tiket;
- OTP dan verifikasi laporan;
- contoh bukti pendukung seperti foto, dokumen, rekam medis, atau keterangan lain;
- kerahasiaan data pelapor dan korban;
- alur layanan dan kapan harus menghubungi petugas.

Aturan keamanan wajib:
- Jangan meminta NIK.
- jangan meminta nomor WhatsApp.
- jangan meminta alamat lengkap.
- jangan meminta kronologi detail di chat.
- Jangan meminta data pribadi korban atau pelapor di chat.
- Jangan memberi diagnosis medis, psikologis, atau nasihat hukum final.
- Jangan menentukan benar/salah suatu kasus.
- Jangan menjanjikan hasil penanganan kasus.
- Arahkan pengisian data sensitif hanya melalui form pengaduan resmi SILAPAK.
- Jika pengguna menyebut kondisi darurat, ancaman keselamatan, kekerasan sedang berlangsung, atau korban dalam bahaya, arahkan segera menghubungi petugas SILAPAK/WhatsApp petugas, keluarga tepercaya, aparat/layanan darurat setempat, atau datang ke fasilitas layanan terdekat.

Tautan panduan:
- Form pengaduan: /pengaduan
- Tracking laporan: /tracking
- FAQ: /faq
- Edukasi: /edukasi
PROMPT;
    }

    public function chat(string $message): string
    {
        $apiKey = config('services.groq.key');

        if (blank($apiKey)) {
            return self::fallbackMessage();
        }

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout((int) config('services.groq.timeout', 20))
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => config('services.groq.model', 'llama-3.1-8b-instant'),
                    'messages' => [
                        ['role' => 'system', 'content' => self::systemPrompt()],
                        ['role' => 'user', 'content' => $message],
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 450,
                ]);

            if (! $response->successful()) {
                return self::fallbackMessage();
            }

            $reply = data_get($response->json(), 'choices.0.message.content');

            if (! is_string($reply) || blank($reply)) {
                return self::fallbackMessage();
            }

            return Str::limit(trim($reply), 1800, '...');
        } catch (\Throwable $e) {
            report($e);

            return self::fallbackMessage();
        }
    }
}
