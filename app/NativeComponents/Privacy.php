<?php

namespace App\NativeComponents;

use Illuminate\View\View;
use Native\Mobile\Edge\NativeComponent;

class Privacy extends NativeComponent
{
    public function items(): array
    {
        return [
            'AI Analysis sends only your topic and public YouTube statistics to third-party AI services (Groq and OpenRouter) to generate insights; no personal data is shared.',
            'Search queries may be processed to generate AI-powered YouTube analytics and insights.',
            'Temporary session data may be stored for analysis history and platform functionality.',
            'No passwords, payment details, or private YouTube account credentials are collected.',
            'This platform uses YouTube API Services and related third-party technologies.',
            'Session data can be removed anytime using the "Delete Everything" option.',
            'This Privacy Policy may be updated periodically without prior notice.',
        ];
    }

    public function navTitle(): string
    {
        return 'Privacy Policy';
    }

    public function render(): View
    {
        return view('native.privacy');
    }
}
