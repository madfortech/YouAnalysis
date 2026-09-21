<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Groq
{
    /**
     * Analyze prompt using Groq AI.
     *
     * Falls back to free OpenRouter models when Groq
     * is rate-limited, unavailable, or returns no content.
     */
    public function analyze(string $prompt): string
    {
        $response = Http::timeout(120)
            ->withHeaders([
                'Authorization' => 'Bearer '.config('services.groq.key'),
                'Content-Type' => 'application/json',
            ])
            ->post(
                'https://api.groq.com/openai/v1/chat/completions',
                [
                    'model' => 'openai/gpt-oss-20b',

                    'messages' => [

                        [
                            'role' => 'system',
                            'content' => 'You are an expert YouTube Growth Strategist. Analyze only the provided YouTube API data. Never invent statistics.',
                        ],

                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],

                    ],

                    'temperature' => 0.2,
                    'max_tokens' => 8192,
                ]
            );

        /**
         * Failed request - fall back to free OpenRouter models
         */
        $content = data_get(
            $response->json(),
            'choices.0.message.content',
            ''
        );

        if (! $response->successful() || trim((string) $content) === '') {

            Log::warning('Groq AI request failed, falling back to OpenRouter free models', [
                'status' => $response->status(),
            ]);

            return app(OpenRouter::class)->analyze($prompt);
        }

        return $content;
    }
}
