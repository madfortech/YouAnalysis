<?php

namespace App\NativeComponents;

use Illuminate\View\View;
use Native\Mobile\Edge\NativeComponent;

class Terms extends NativeComponent
{
    public function items(): array
    {
        return [
            'Information is collected to improve platform functionality and user experience.',
            'Temporary session data may be stored for analysis history.',
            'No personal passwords or private YouTube account credentials are collected.',
            'This platform uses YouTube API Services.',
            'Session data can be cleared anytime using the "Delete Everything" option.',
            'This Privacy Policy may be updated periodically without prior notice.',
        ];
    }

    public function navTitle(): string
    {
        return 'Terms of Service';
    }

    public function render(): View
    {
        return view('native.terms');
    }
}
