<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('home');
// });

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/guest.php';
