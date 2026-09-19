<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DeepSeek
{
    protected string $endpoint = 'https://api.deepseek.com/chat/completions';

    public function analyze(string $prompt): string
    {
        $response = Http::timeout(120)
            ->withHeaders([
                'Authorization' => 'Bearer ' . config('services.deepseek.key'),
                'Content-Type'  => 'application/json',
            ])
            ->post($this->endpoint, [

                'model' => 'deepseek-v4-flash',

                'messages' => [
                    [
                        'role' => 'user',
                        'content' => 'You are a professional YouTube SEO strategist.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],

                'thinking' => [
                    'type' => 'enabled'
                ],

                'reasoning_effort' => 'medium',

                'temperature' => 0.7,

                'max_tokens' => 1200,

                'stream' => false,
            ]);

        if (!$response->successful()) {

            \Log::error('DeepSeek API Error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            throw new \Exception('DeepSeek API request failed.');
        }

        return data_get(
            $response->json(),
            'choices.0.message.content',
            'No response generated.'
        );
    }
}

