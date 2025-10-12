<?php

use App\Http\Controllers\ContactsController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebSocketController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Email verification routes
Route::get('/verify-email', [EmailVerificationController::class, 'showVerificationForm'])->name('verify.email.form');
Route::post('/verify-email', [EmailVerificationController::class, 'verifyEmail'])->name('verify.email');
Route::post('/resend-verification', [EmailVerificationController::class, 'resendVerification'])->name('resend.verification');

// Password reset routes
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    // Chat routes
    Route::get('/contacts', [ContactsController::class, 'get']);
    Route::get('/contacts/preload', [ContactsController::class, 'preloadConversations']);
    Route::post('/conversation/send', [ContactsController::class, 'send']);
    Route::get('/conversation/{id}', [ContactsController::class, 'getMessagesFor']);
    
    // WebSocket routes
    Route::get('/websocket/info', [WebSocketController::class, 'getConnectionInfo']);
    Route::post('/websocket/test', [WebSocketController::class, 'testConnection']);
    Route::post('/websocket/typing', [WebSocketController::class, 'sendTyping']);
    Route::post('/websocket/read', [WebSocketController::class, 'markMessageRead']);
    Route::post('/websocket/online', [WebSocketController::class, 'setOnlineStatus']);
    Route::get('/websocket/preload-realtime', [WebSocketController::class, 'getPreloadedConversationsWithRealtime']);
    
    // Profile management routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::put('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::delete('/profile/delete-avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.delete-avatar');
});
