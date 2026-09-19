<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PopularController;
use App\Http\Controllers\AiAnalysisController;


Route::get('/', function () {
    return view('home');
});

Route::get('/', function () {
    return view('welcome');
});


Route::get('/terms', function () {
    return view('terms');
});

Route::get('/privacy', function () {
    return view('privacy');
});


Route::get('/popular', [PopularController::class, 'index']);
Route::get('/ai-analysis', [AiAnalysisController::class, 'index']);
Route::post('/ai-analysis', [AiAnalysisController::class, 'analyze']);
Route::get('/history', [AiAnalysisController::class, 'history']);

require __DIR__.'/guest.php';