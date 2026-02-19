<?php

namespace App\Services;

use App\Models\AiConversation;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class AiService
{
    /**
     * Ask a question and get an AI response.
     * Uses OpenAI API if configured, otherwise returns a dummy response.
     */
    public function ask(User $user, string $question): AiConversation
    {
        $apiKey = config('services.openai.key');

        if ($apiKey) {
            $answer = $this->askOpenAI($question, $apiKey);
        } else {
            $answer = $this->generateDummyResponse($question);
        }

        return AiConversation::create([
            'user_id' => $user->id,
            'question' => $question,
            'answer' => $answer,
        ]);
    }

    /**
     * Get user's conversation history.
     */
    public function getHistory(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $user->aiConversations()
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Ask OpenAI API.
     */
    private function askOpenAI(string $question, string $apiKey): string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant. Respond in the same language as the user\'s question.'],
                    ['role' => 'user', 'content' => $question],
                ],
                'max_tokens' => 1000,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content', 'Maaf, saya tidak dapat memproses pertanyaan Anda saat ini.');
            }

            return 'Maaf, terjadi kesalahan saat menghubungi AI. Silakan coba lagi.';
        } catch (\Exception $e) {
            return 'Maaf, terjadi kesalahan: ' . $e->getMessage();
        }
    }

    /**
     * Generate a dummy response for testing without API key.
     */
    private function generateDummyResponse(string $question): string
    {
        $responses = [
            'Terima kasih atas pertanyaan Anda! Ini adalah respons demo dari AI Agent. Dalam mode production, respons ini akan berasal dari OpenAI API.',
            'Pertanyaan yang menarik! Saya adalah AI Agent demo. Untuk mendapatkan respons nyata, silakan konfigurasi OPENAI_API_KEY di file .env Anda.',
            'Halo! Saya AI Agent dalam mode demo. Pertanyaan Anda: "' . mb_substr($question, 0, 50) . '..." Untuk respons yang sebenarnya, aktifkan integrasi OpenAI.',
            'Ini adalah mode simulasi AI Agent. Saya menerima pertanyaan Anda dan akan memberikan jawaban yang lebih baik setelah OpenAI API dikonfigurasi.',
            'Saya adalah asisten AI dalam mode pengujian. Pertanyaan Anda telah dicatat. Silakan atur API key OpenAI untuk mendapatkan respons yang lebih akurat dan bermanfaat.',
        ];

        return $responses[array_rand($responses)];
    }
}
