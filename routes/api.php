<?php

use App\Http\Controllers\Api\AboutUsController;
use App\Http\Controllers\Api\ActivityLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\HomePageController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ManageUserController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProdukLayananController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\TestimoniController;
use App\Http\Controllers\Api\TimKamiController;

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// public beranda
Route::get('/homepage', [HomePageController::class, 'show']);

// public tentang kami
Route::get('/tentang-kami-page', [AboutUsController::class, 'show']);

// public produk & layanan
Route::get('/produk-layanan-page', [ProdukLayananController::class, 'show']);

// public testimoni
Route::get('/testimoni-page', [TestimoniController::class, 'show']);

// public tim kami
Route::get('/tim-kami-page', [TimKamiController::class, 'show']);

// public kirim pesan
Route::post('/contact', [ContactController::class, 'store']);

// setting public
Route::get('/settings',  [SettingController::class, 'index']);

Route::middleware('auth.token')->group(function() {
    
    //----- route user ------//
    Route::post('/meetings', [MeetingController::class, 'store']);
    Route::get('/my-meetings', [MeetingController::class, 'myMeetings']);
    Route::get('/my-meetings/stats', [MeetingController::class, 'stats']);
    Route::delete('/my-meetings/{id}', [MeetingController::class, 'cancel']);
    
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    
    //---- route admin ------//
    // website management
    Route::get('/admin/homepage', [HomePageController::class, 'index']);
    // Hero
    Route::post('/admin/homepage/hero', [HomePageController::class, 'storeHero']);
    Route::put('/admin/homepage/hero/{id}', [HomePageController::class, 'updateHero']);
    Route::delete('/admin/homepage/hero/{id}', [HomePageController::class, 'destroyHero']);
    // About
    Route::post('/admin/homepage/about', [HomePageController::class, 'storeAbout']);
    Route::put('/admin/homepage/about/{id}', [HomePageController::class, 'updateAbout']);
    // Stats
    Route::post('/admin/homepage/stats', [HomePageController::class, 'storeStats']);
    Route::put('/admin/homepage/stats/{id}', [HomePageController::class, 'updateStats']);
    // Services
    Route::post('/admin/homepage/services', [HomePageController::class, 'storeServiceSection']);
    Route::put('/admin/homepage/services/{id}', [HomePageController::class, 'updateServiceSection']);
    // CTA
    Route::post('/admin/homepage/cta', [HomePageController::class, 'storeCta']);
    Route::put('/admin/homepage/cta/{id}', [HomePageController::class, 'updateCta']);

    // Tentang Kami
    Route::get('/admin/tentang-kami',     [AboutUsController::class, 'index']);
    Route::post('/admin/tentang-kami',    [AboutUsController::class, 'store']);
    Route::put('/admin/tentang-kami/{id}',[AboutUsController::class, 'update']);

    // Produk & Layanan
    Route::get('/admin/produk-layanan', [ProdukLayananController::class, 'index']);
    // Hero
    Route::post('/admin/produk-layanan/hero', [ProdukLayananController::class, 'storeHero']);
    Route::put('/admin/produk-layanan/hero/{id}', [ProdukLayananController::class, 'updateHero']);
    // Products
    Route::post('/admin/produk-layanan/products', [ProdukLayananController::class, 'storeProduct']);
    Route::put('/admin/produk-layanan/products/{id}', [ProdukLayananController::class, 'updateProduct']);
    Route::delete('/admin/produk-layanan/products/{id}', [ProdukLayananController::class, 'destroyProduct']);
    // Services
    Route::post('/admin/produk-layanan/services', [ProdukLayananController::class, 'storeService']);
    Route::put('/admin/produk-layanan/services/{id}', [ProdukLayananController::class, 'updateService']);
    Route::delete('/admin/produk-layanan/services/{id}', [ProdukLayananController::class, 'destroyService']);

    // Testimoni
    Route::get('/admin/testimoni', [TestimoniController::class, 'index']);
    // Hero
    Route::post('/admin/testimoni/hero', [TestimoniController::class, 'storeHero']);
    Route::put('/admin/testimoni/hero/{id}', [TestimoniController::class, 'updateHero']);
    // Featured Customers
    Route::post('/admin/testimoni/featured-customers', [TestimoniController::class, 'storeFeaturedCustomer']);
    Route::put('/admin/testimoni/featured-customers/{id}', [TestimoniController::class, 'updateFeaturedCustomer']);
    Route::delete('/admin/testimoni/featured-customers/{id}', [TestimoniController::class, 'destroyFeaturedCustomer']);
    // Testimonials
    Route::post('/admin/testimoni/testimonials', [TestimoniController::class, 'storeTestimonial']);
    Route::put('/admin/testimoni/testimonials/{id}', [TestimoniController::class, 'updateTestimonial']);
    Route::delete('/admin/testimoni/testimonials/{id}', [TestimoniController::class, 'destroyTestimonial']);
    // Client & Partners
    Route::post('/admin/testimoni/partners', [TestimoniController::class, 'storePartner']);
    Route::put('/admin/testimoni/partners/{id}', [TestimoniController::class, 'updatePartner']);
    Route::delete('/admin/testimoni/partners/{id}', [TestimoniController::class, 'destroyPartner']);
    
    // dashboard
    Route::get('/admin/meetings/recent', [MeetingController::class, 'recentMeeting']);
    Route::get('/admin/meetings/monthly', [MeetingController::class, 'monthlyStats']);
    Route::get('/admin/meetings/calendar', [MeetingController::class, 'calendarEvents']);
    Route::get('/admin/meetings', [MeetingController::class, 'index']);
    Route::get('/admin/meetings/{id}', [MeetingController::class, 'show']);
    Route::get('/admin/statistics', [MeetingController::class, 'statistics']);
    Route::put('/admin/meetings/{id}/approved', [MeetingController::class, 'approved']);
    Route::put('/admin/meetings/{id}/reject', [MeetingController::class, 'reject']);
    Route::put('/admin/meetings/{id}/done', [MeetingController::class, 'done']);

    // Tim Kami
    Route::get('/admin/tim-kami',          [TimKamiController::class, 'index']);
    // Hero
    Route::post('/admin/tim-kami/hero',    [TimKamiController::class, 'storeHero']);
    Route::put('/admin/tim-kami/hero/{id}',[TimKamiController::class, 'updateHero']);
    // Team Members (semua divisi pakai endpoint sama)
    Route::post('/admin/tim-kami/members',       [TimKamiController::class, 'storeMember']);
    Route::put('/admin/tim-kami/members/{id}',   [TimKamiController::class, 'updateMember']);
    Route::delete('/admin/tim-kami/members/{id}',[TimKamiController::class, 'destroyMember']);

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
