<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MeetingController;

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth.token')->group(function() {
    
    // route user
    Route::post('/meetings', [MeetingController::class, 'store']);
    Route::get('/my-meetings', [MeetingController::class, 'myMeetings']);
    
    // route admin
    Route::post('/admin/meetings', [MeetingController::class, 'index']);
    Route::put('/admin/meetings/{id}/approve', [MeetingController::class, 'approved']);
    Route::put('/admin/meetings/{id}/reject', [MeetingController::class, 'reject']);
    Route::put('/admin/meetings/{id}/done', [MeetingController::class, 'done']);

    // create admin
    Route::post('/admin/users', [AuthController::class, 'createdAdmin']);

    // logout
    Route::post('/logout', [AuthController::class, 'logout']);
});

// testing api dan middleware (optional bisa di hapus)
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