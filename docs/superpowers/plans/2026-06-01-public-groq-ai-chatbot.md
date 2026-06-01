# Public Groq AI Chatbot Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a public SILAPAK AI chatbot powered by Groq that helps visitors understand reporting, OTP, tracking, evidence, privacy, and emergency guidance without requiring login.

**Architecture:** The browser renders a reusable Blade chatbot component in the public layout only. The component sends short messages to a Laravel `POST /ai/chat` endpoint, which validates input and delegates to a small `GroqAiService` wrapper around Groq's OpenAI-compatible chat completions API. The system prompt enforces privacy/safety rules, and the controller returns a safe fallback when Groq is unavailable.

**Tech Stack:** Laravel 13, PHP 8.3, Blade, Alpine.js, Laravel HTTP Client, Groq Chat Completions API, PHPUnit Feature Tests, Vite/Tailwind build.

---

## File Structure

- Create: `app/Services/GroqAiService.php`
  - Encapsulates Groq request building, system prompt, fallback message, and response parsing.
- Create: `app/Http/Controllers/PublicAiChatController.php`
  - Validates public chat request and returns JSON reply.
- Create: `resources/views/components/public-ai-chat.blade.php`
  - Floating public chatbot UI using Alpine and `fetch()`.
- Create: `tests/Feature/PublicAiChatTest.php`
  - Verifies validation, Groq success, fallback behavior, guest access, prompt guardrails, and public layout rendering.
- Modify: `routes/web.php`
  - Add `POST /ai/chat` public route.
- Modify: `config/services.php`
  - Add Groq configuration from environment.
- Modify: `.env.example`
  - Document Groq key/model variables.
- Modify: `resources/views/layouts/app.blade.php`
  - Include chatbot component before `</body>`.

## Task 1: Write Failing Tests for Public AI Chat Endpoint

**Files:**
- Create: `tests/Feature/PublicAiChatTest.php`

- [ ] **Step 1: Create endpoint and service behavior tests**

Create `tests/Feature/PublicAiChatTest.php` with this content:

```php
<?php

namespace Tests\Feature;

use App\Services\GroqAiService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PublicAiChatTest extends TestCase
{
    public function test_public_ai_chat_rejects_empty_message(): void
    {
        $this->postJson(route('ai.chat'), ['message' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('message');
    }

    public function test_public_ai_chat_rejects_overly_long_message(): void
    {
        $this->postJson(route('ai.chat'), ['message' => str_repeat('a', 1001)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('message');
    }

    public function test_public_ai_chat_returns_groq_reply_for_guest(): void
    {
        config()->set('services.groq.key', 'test-groq-key');
        config()->set('services.groq.model', 'llama-3.1-8b-instant');

        Http::fake([
            'api.groq.com/openai/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Untuk membuat laporan, klik tombol Lapor dan isi formulir resmi SILAPAK.',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $this->postJson(route('ai.chat'), ['message' => 'Bagaimana cara membuat laporan?'])
            ->assertOk()
            ->assertJson([
                'reply' => 'Untuk membuat laporan, klik tombol Lapor dan isi formulir resmi SILAPAK.',
            ]);

        Http::assertSent(function ($request): bool {
            $payload = $request->data();

            return $request->url() === 'https://api.groq.com/openai/v1/chat/completions'
                && $request->hasHeader('Authorization', 'Bearer test-groq-key')
                && $payload['model'] === 'llama-3.1-8b-instant'
                && $payload['messages'][0]['role'] === 'system'
                && str_contains($payload['messages'][0]['content'], 'Jangan meminta NIK')
                && str_contains($payload['messages'][0]['content'], 'jangan meminta nomor WhatsApp')
                && str_contains($payload['messages'][0]['content'], 'jangan meminta alamat lengkap')
                && str_contains($payload['messages'][0]['content'], 'jangan meminta kronologi detail')
                && $payload['messages'][1] === [
                    'role' => 'user',
                    'content' => 'Bagaimana cara membuat laporan?',
                ];
        });
    }

    public function test_public_ai_chat_returns_safe_fallback_when_groq_is_unavailable(): void
    {
        config()->set('services.groq.key', 'test-groq-key');

        Http::fake([
            'api.groq.com/openai/v1/chat/completions' => Http::response(['error' => ['message' => 'service unavailable']], 503),
        ]);

        $this->postJson(route('ai.chat'), ['message' => 'Halo'])
            ->assertOk()
            ->assertJson([
                'reply' => GroqAiService::fallbackMessage(),
            ]);
    }

    public function test_public_ai_chat_returns_safe_fallback_when_api_key_is_missing(): void
    {
        config()->set('services.groq.key', null);

        $this->postJson(route('ai.chat'), ['message' => 'Halo'])
            ->assertOk()
            ->assertJson([
                'reply' => GroqAiService::fallbackMessage(),
            ]);

        Http::assertNothingSent();
    }

    public function test_public_layout_renders_ai_chat_widget(): void
    {
        $this->get(route('beranda'))
            ->assertOk()
            ->assertSee('Tanya SILAPAK AI')
            ->assertSee('Jangan tuliskan NIK', false);
    }
}
```

- [ ] **Step 2: Run tests to verify RED**

Run:

```bash
php artisan test tests/Feature/PublicAiChatTest.php --stop-on-failure
```

Expected:
- FAIL because route `ai.chat` does not exist, `GroqAiService` does not exist, and the component is not rendered.

## Task 2: Implement Groq AI Service and Public Controller

**Files:**
- Create: `app/Services/GroqAiService.php`
- Create: `app/Http/Controllers/PublicAiChatController.php`
- Modify: `routes/web.php`
- Modify: `config/services.php`
- Modify: `.env.example`

- [ ] **Step 1: Create `app/Services/GroqAiService.php`**

```php
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
```

- [ ] **Step 2: Create `app/Http/Controllers/PublicAiChatController.php`**

```php
<?php

namespace App\Http\Controllers;

use App\Services\GroqAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicAiChatController extends Controller
{
    public function __invoke(Request $request, GroqAiService $groqAiService): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:1000'],
        ]);

        return response()->json([
            'reply' => $groqAiService->chat(trim($data['message'])),
        ]);
    }
}
```

- [ ] **Step 3: Add route in `routes/web.php`**

Add import near other controller imports:

```php
use App\Http\Controllers\PublicAiChatController;
```

Add public route after `Route::get('/edukasi', ...)`:

```php
Route::post('/ai/chat', PublicAiChatController::class)->name('ai.chat');
```

- [ ] **Step 4: Add Groq config to `config/services.php`**

Before the closing array `];`, add:

```php
    'groq' => [
        'key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'llama-3.1-8b-instant'),
        'timeout' => env('GROQ_TIMEOUT', 20),
    ],
```

- [ ] **Step 5: Document env variables in `.env.example`**

Append:

```env
GROQ_API_KEY=
GROQ_MODEL=llama-3.1-8b-instant
GROQ_TIMEOUT=20
```

- [ ] **Step 6: Run endpoint tests**

Run:

```bash
php artisan test tests/Feature/PublicAiChatTest.php --stop-on-failure
```

Expected:
- Endpoint tests pass or only layout widget test fails because component has not been created yet.

## Task 3: Add Public Chatbot UI Component

**Files:**
- Create: `resources/views/components/public-ai-chat.blade.php`
- Modify: `resources/views/layouts/app.blade.php`

- [ ] **Step 1: Create chatbot component**

Create `resources/views/components/public-ai-chat.blade.php`:

```blade
<div
    x-data="{
        open: false,
        loading: false,
        message: '',
        messages: [
            {
                from: 'ai',
                text: 'Halo, saya Asisten SILAPAK. Saya bisa membantu menjelaskan cara membuat pengaduan, tracking laporan, OTP, bukti pendukung, dan alur layanan. Jangan tuliskan NIK, alamat lengkap, nomor WhatsApp, atau kronologi detail di chat ini.'
            }
        ],
        async send() {
            const text = this.message.trim();
            if (!text || this.loading) return;

            this.messages.push({ from: 'user', text });
            this.message = '';
            this.loading = true;

            try {
                const response = await fetch('{{ route('ai.chat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: text })
                });

                const data = await response.json();
                const reply = data.reply || 'Maaf, Asisten SILAPAK AI sedang tidak tersedia. Silakan gunakan menu FAQ, halaman tracking, atau hubungi petugas melalui WhatsApp.';
                this.messages.push({ from: 'ai', text: reply });
            } catch (error) {
                this.messages.push({ from: 'ai', text: 'Maaf, Asisten SILAPAK AI sedang tidak tersedia. Silakan gunakan menu FAQ, halaman tracking, atau hubungi petugas melalui WhatsApp.' });
            } finally {
                this.loading = false;
                this.$nextTick(() => {
                    const box = this.$refs.messages;
                    if (box) box.scrollTop = box.scrollHeight;
                });
            }
        }
    }"
    class="fixed bottom-5 right-5 z-[9999] font-outfit"
>
    <div x-show="open" x-cloak class="mb-4 w-[calc(100vw-2.5rem)] max-w-sm overflow-hidden rounded-3xl border border-orange-100 bg-white shadow-2xl shadow-slate-900/20">
        <div class="bg-slate-950 p-4 text-white">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.18em] text-orange-300">SILAPAK AI</p>
                    <h2 class="mt-1 text-lg font-black">Asisten Pengaduan Publik</h2>
                </div>
                <button type="button" class="grid h-9 w-9 place-items-center rounded-full bg-white/10 text-white hover:bg-white/20" @click="open = false" aria-label="Tutup chatbot">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <p class="mt-3 text-xs leading-5 text-slate-300">AI hanya memberi panduan umum. Untuk laporan resmi, gunakan form pengaduan SILAPAK.</p>
        </div>

        <div x-ref="messages" class="max-h-80 space-y-3 overflow-y-auto bg-[#fffaf3] p-4">
            <template x-for="(item, index) in messages" :key="index">
                <div class="flex" :class="item.from === 'user' ? 'justify-end' : 'justify-start'">
                    <div class="max-w-[85%] rounded-2xl px-4 py-3 text-sm leading-6 shadow-theme-xs" :class="item.from === 'user' ? 'bg-orange-500 text-white' : 'bg-white text-slate-700 ring-1 ring-orange-100'">
                        <p x-text="item.text"></p>
                    </div>
                </div>
            </template>
            <div x-show="loading" class="flex justify-start">
                <div class="rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-500 ring-1 ring-orange-100">Asisten sedang mengetik...</div>
            </div>
        </div>

        <form class="border-t border-orange-100 bg-white p-3" @submit.prevent="send">
            <div class="flex gap-2">
                <input
                    x-model="message"
                    maxlength="1000"
                    class="min-w-0 flex-1 rounded-2xl border border-orange-100 bg-white px-4 py-3 text-sm outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                    placeholder="Tulis pertanyaan singkat..."
                >
                <button type="submit" class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-orange-500 text-white shadow-lg shadow-orange-500/25 hover:bg-orange-600 disabled:opacity-60" :disabled="loading || !message.trim()" aria-label="Kirim pesan">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
            <p class="mt-2 text-[11px] leading-4 text-slate-500">Jangan kirim NIK, alamat lengkap, nomor WhatsApp, atau kronologi detail di chat.</p>
        </form>
    </div>

    <button type="button" class="flex items-center gap-3 rounded-full bg-orange-500 px-5 py-4 font-black text-white shadow-2xl shadow-orange-500/30 transition hover:-translate-y-1 hover:bg-orange-600" @click="open = !open" aria-label="Buka Tanya SILAPAK AI">
        <span class="grid h-9 w-9 place-items-center rounded-full bg-white/20"><i class="fa-solid fa-robot"></i></span>
        <span>Tanya SILAPAK AI</span>
    </button>
</div>
```

- [ ] **Step 2: Include component in public layout**

In `resources/views/layouts/app.blade.php`, add before `</body>`:

```blade
    <x-public-ai-chat />
```

- [ ] **Step 3: Run public AI tests**

Run:

```bash
php artisan test tests/Feature/PublicAiChatTest.php
```

Expected: PASS.

## Task 4: Full Verification and Build

**Files:**
- Verify all changed files.

- [ ] **Step 1: Run all tests**

Run:

```bash
php artisan test
```

Expected: PASS.

- [ ] **Step 2: Run frontend build per project workflow**

Run:

```bash
npm run build
```

Expected: PASS.

- [ ] **Step 3: Review diff**

Run:

```bash
git diff -- app/Services/GroqAiService.php app/Http/Controllers/PublicAiChatController.php routes/web.php config/services.php .env.example resources/views/components/public-ai-chat.blade.php resources/views/layouts/app.blade.php tests/Feature/PublicAiChatTest.php docs/superpowers/plans/2026-06-01-public-groq-ai-chatbot.md
```

Expected:
- No real Groq API key is committed.
- `GROQ_API_KEY=` is blank in `.env.example`.
- Chat endpoint is public but validates message length.
- Prompt contains privacy/safety guardrails.
- Chat component is only in `layouts.app`, not admin layout.

## Self-Review

- Spec coverage: Plan covers public placement, Groq integration, no login requirement, no database logging, guardrails, fallback handling, and build/testing.
- Placeholder scan: No TBD/TODO/implement later placeholders.
- Type consistency: `GroqAiService::fallbackMessage()`, `GroqAiService::systemPrompt()`, `GroqAiService::chat()`, route name `ai.chat`, and component `public-ai-chat` are defined before use.
