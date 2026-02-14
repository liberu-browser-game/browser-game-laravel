<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Notification routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('api.notifications.index');
        Route::get('/unread', [NotificationController::class, 'unread'])->name('api.notifications.unread');
        Route::get('/count', [NotificationController::class, 'count'])->name('api.notifications.count');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('api.notifications.mark-as-read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('api.notifications.mark-all-as-read');
    });
});
