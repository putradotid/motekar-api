<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth.token')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);
});

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