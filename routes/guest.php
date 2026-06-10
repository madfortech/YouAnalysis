<?php

use Illuminate\Support\Facades\Route;

Route::post('/guest-login', function () {

    session([
        'guest' => 'Guest'
    ]);

    return redirect('/ai-analysis');

});

Route::get('/ai-analysis', function () {

    if (! session()->has('guest')) {

        return redirect('/');

    }

    return view('ai-analysis');

});

Route::get('/logout', function () {

    session()->forget('guest');

    return redirect('/');

});

Route::get('/confirm-logout', function () {

    return view('confirm-logout');

});

Route::get('/delete-everything', function () {

    // Clear all session data
    session()->flush();
    
    // Regenerate session
    session()->invalidate();
    session()->regenerateToken();
    
    return redirect('/');

});

