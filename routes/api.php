<?php

use App\Http\Controllers\Api\ActivityLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ManageUserController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SettingController;

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// public kirim pesan
Route::post('/contact', [ContactController::class, 'store']);

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

    // message
    Route::get('/messages', [MessageController::class, 'meetings']);
    Route::get('/messages/{meetingId}', [MessageController::class, 'index']);
    Route::post('/messages/{meetingId}', [MessageController::class, 'store']);

    // media
    Route::get('/admin/media', [MediaController::class, 'index']);
    Route::post('/admin/media', [MediaController::class, 'store']);
    Route::get('/admin/media/{id}', [MediaController::class, 'show']);
    Route::delete('/admin/media/{id}', [MediaController::class, 'destroy']);

    // setting
    Route::get('/settings',  [SettingController::class, 'index']);
    Route::post('/settings', [SettingController::class, 'update']);
    
    // activity log
    Route::get('/admin/activity-logs', [ActivityLogController::class, 'index']);

    // notification
    Route::get('/admin/notifications/count', [NotificationController::class, 'notificationCount']);
    Route::post('/messages/{meetingId}/read', [MessageController::class, 'markAsRead']);

    // pesan masuk dari public
    Route::get('/admin/contacts', [ContactController::class, 'index']);
    Route::get('/admin/contacts/{id}', [ContactController::class, 'show']);
    Route::put('/admin/contacts/{id}/status', [ContactController::class, 'updateStatus']);
    Route::delete('/admin/contacts/{id}', [ContactController::class, 'destroy']);

    // logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
