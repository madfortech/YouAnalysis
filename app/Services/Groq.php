<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Groq
{
    /**
     * Analyze prompt using Groq AI
     */
    public function analyze(string $prompt): string
    {
        $response = Http::timeout(120)
            ->withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type'  => 'application/json',
            ])
            ->post(
                'https://api.groq.com/openai/v1/chat/completions',
                [
                    'model' => 'openai/gpt-oss-20b',

                    'messages' => [
                        [
                            'role'    => 'user',
                            'content' => $prompt,
                        ]
                    ],

                    'temperature' => 0.7,
                    'max_tokens'  => 1000,
                ]
            );

        /**
         * Failed request
         */
        if (! $response->successful()) {

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