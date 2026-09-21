<?php

namespace App\NativeComponents\Layouts;

use App\Icons\Android;
use Native\Mobile\Edge\Layouts\Builders\NavBar;
use Native\Mobile\Edge\Layouts\Builders\Tab;
use Native\Mobile\Edge\Layouts\Builders\TabBar;
use Native\Mobile\Edge\Layouts\NativeLayout;
use Native\Mobile\Edge\NativeComponent;

class TabsLayout extends NativeLayout
{
    public function navBar(NativeComponent $screen): ?NavBar
    {
        return NavBar::make()
            ->title($screen->navTitle())
            ->displayMode('large');
    }

    public function tabBar(NativeComponent $screen): ?TabBar
    {
        return TabBar::make()
            ->activeColor(theme('primary'))
            ->add(
                Tab::link('Home', '/', icon: 'home', android: Android::Home)
            )
            ->add(
                Tab::link('Explore', '/popular', icon: 'explore', android: Android::Explore)
            )
            ->add(
                Tab::link('Analytics', '/ai-analysis', icon: 'analytics', android: Android::Analytics)
            );
    }
}
