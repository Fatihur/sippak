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

    public function test_public_ai_chat_button_stays_anchored_and_uses_icon_only_on_mobile(): void
    {
        $this->get(route('beranda'))
            ->assertOk()
            ->assertSee('class="fixed bottom-5 right-5 z-[9999] flex flex-col items-end font-outfit"', false)
            ->assertSee('class="absolute bottom-full right-0 mb-4', false)
            ->assertSee('class="hidden sm:inline">Tanya SILAPAK AI</span>', false);
    }
}
