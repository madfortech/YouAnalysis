<?php

namespace App\NativeComponents;

use Illuminate\View\View;
use Native\Mobile\Edge\NativeComponent;

class Home extends NativeComponent
{
    public function navTitle(): string
    {
        return 'You Analysis';
    }

    public function renderedAsGuest(): bool
    {
        return session()->has('guest');
    }

    public function continueAsGuest(): void
    {
        session(['guest' => 'Guest']);

        $this->navigate('/ai-analysis');
    }

    public function logout(): void
    {
        session()->forget('guest');

        $this->replace('/');
    }

    public function render(): View
    {
        return view('native.home');
    }
}
