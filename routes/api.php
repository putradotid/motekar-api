<?php

use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json(['message' => 'API OK']);
});

Route::middleware('auth.token')->group(function () {

    Route::get('/test-auth', function () {
        return response()->json([
            'message' => 'Berhasil akses dengan token'
        ]);
    });

});