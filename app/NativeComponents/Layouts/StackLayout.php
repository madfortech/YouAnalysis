<?php

namespace App\NativeComponents\Layouts;

use Native\Mobile\Edge\Layouts\Builders\NavBar;
use Native\Mobile\Edge\Layouts\NativeLayout;
use Native\Mobile\Edge\NativeComponent;

class StackLayout extends NativeLayout
{
    public function navBar(NativeComponent $screen): ?NavBar
    {
        return NavBar::make()
            ->title($screen->navTitle())
            ->back();
    }
}
