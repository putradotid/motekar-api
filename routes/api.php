<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ManageUserController;
use App\Http\Controllers\Api\MeetingController;

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth.token')->group(function() {
    
    // route user
    Route::post('/meetings', [MeetingController::class, 'store']);
    Route::get('/my-meetings', [MeetingController::class, 'myMeetings']);
    Route::get('/my-meetings/stats', [MeetingController::class, 'stats']);
    Route::delete('/my-meetings/{id}', [MeetingController::class, 'cancel']);
    
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    
    // route admin
    Route::get('/admin/meetings/recent', [MeetingController::class, 'recentMeeting']);
    Route::get('/admin/meetings/monthly', [MeetingController::class, 'monthlyStats']);
    Route::get('/admin/meetings/calendar', [MeetingController::class, 'calendarEvents']);
    Route::get('/admin/meetings', [MeetingController::class, 'index']);
    Route::get('/admin/meetings/{id}', [MeetingController::class, 'show']);
    Route::get('/admin/statistics', [MeetingController::class, 'statistics']);
    Route::put('/admin/meetings/{id}/approved', [MeetingController::class, 'approved']);
    Route::put('/admin/meetings/{id}/reject', [MeetingController::class, 'reject']);
    Route::put('/admin/meetings/{id}/done', [MeetingController::class, 'done']);

    // create admin
    Route::post('/admin/users', [AuthController::class, 'createdAdmin']);
    
    // manage user
    Route::get('/admin/users', [ManageUserController::class, 'ListUser']);
    Route::put('/admin/users/{id}/suspend', [ManageUserController::class, 'suspend']);
    Route::put('/admin/users/{id}/active', [ManageUserController::class, 'active']);


    // logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
