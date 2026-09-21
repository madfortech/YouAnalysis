<?php

namespace App\NativeComponents;

use Illuminate\View\View;
use Native\Mobile\Edge\NativeComponent;

class ConfirmLogout extends NativeComponent
{
    public function deleteEverything(): void
    {
        session()->flush();
        session()->invalidate();
        session()->regenerateToken();

        $this->replace('/');
    }

    public function navTitle(): string
    {
        return 'Delete Everything?';
    }

    public function render(): View
    {
        return view('native.confirm-logout');
    }
}
