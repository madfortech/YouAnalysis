<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouter
{
    /**
     * Analyze prompt using free OpenRouter models.
     *
     * Passing the models array lets OpenRouter itself fall back
     * across the free list when a model is rate-limited or down.
     */
    public function analyze(string $prompt): string
    {
        $models = config('services.openrouter.free_models', []);

        if ($models === [] || blank(config('services.openrouter.key'))) {
            return 'No response generated.';
        }

        $response = Http::timeout(120)
            ->withHeaders([
                'Authorization' => 'Bearer '.config('services.openrouter.key'),
                'Content-Type' => 'application/json',
            ])
            ->post(
                'https://openrouter.ai/api/v1/chat/completions',
                [
                    'models' => $models,

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
                    'max_tokens' => 4096,
                ]
            );

        /**
         * Failed request
         */
        if (! $response->successful()) {

            Log::warning('OpenRouter AI request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return 'AI request failed.';
        }

        /**
         * Return AI response
         */
        return data_get(
            $response->json(),
            'choices.0.message.content',
            'No response generated.'
        );
    }
}
