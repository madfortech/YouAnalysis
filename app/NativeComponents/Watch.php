<?php

namespace App\NativeComponents;

use Illuminate\View\View;
use Native\Mobile\Edge\NativeComponent;

class Watch extends NativeComponent
{
    public function video(): array
    {
        return $this->data('video', []);
    }

    public function navTitle(): string
    {
        return 'Watch';
    }

    public function render(): View
    {
        return view('native.watch');
    }
}
