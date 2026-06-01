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
