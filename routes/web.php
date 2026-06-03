<?php

use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Http\Controllers\TeamInvitationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', fn () => view('welcome'));

// Kubernetes health check endpoints
Route::prefix('health')->group(function () {
    Route::get('/startup', fn () => response()->json(['status' => 'ok']));
    Route::get('/live',    fn () => response()->json(['status' => 'ok']));
    Route::get('/ready',   function () {
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
        } catch (\Throwable) {
            return response()->json(['status' => 'unavailable', 'reason' => 'database'], 503);
        }
        return response()->json(['status' => 'ok']);
    });
});

// Route::redirect('/login', '/app/login')->name('login');

// Route::redirect('/register', '/app/register')->name('register');

Route::redirect('/dashboard', '/app')->name('dashboard');

// Game Routes
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/game', function () {
        return view('game.dashboard');
    })->name('game.dashboard');
});

Route::get('/team-invitations/{invitation}', [TeamInvitationController::class, 'accept'])
    ->middleware(['signed', 'verified', 'auth', AuthenticateSession::class])
    ->name('team-invitations.accept');
