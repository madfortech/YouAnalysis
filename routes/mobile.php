<?php

use App\NativeComponents\AiAnalysis;
use App\NativeComponents\ConfirmLogout;
use App\NativeComponents\History;
use App\NativeComponents\Home;
use App\NativeComponents\Layouts\StackLayout;
use App\NativeComponents\Layouts\TabsLayout;
use App\NativeComponents\Popular;
use App\NativeComponents\Privacy;
use App\NativeComponents\Terms;
use App\NativeComponents\Watch;
use Illuminate\Support\Facades\Route;

Route::nativeGroup(TabsLayout::class, function () {
    Route::native('/', Home::class);
    Route::native('/popular', Popular::class);
    Route::native('/ai-analysis', AiAnalysis::class);
});

Route::native('/history', History::class)->layout(StackLayout::class);
Route::native('/privacy', Privacy::class)->layout(StackLayout::class);
Route::native('/terms', Terms::class)->layout(StackLayout::class);
Route::native('/confirm-logout', ConfirmLogout::class)->layout(StackLayout::class);
Route::native('/watch/{id}', Watch::class)->layout(StackLayout::class);
