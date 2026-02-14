<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestController;

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

// Quest API routes
Route::prefix('quests')->group(function () {
    Route::get('/available', [QuestController::class, 'available']);
    Route::get('/active', [QuestController::class, 'active']);
    Route::get('/completed', [QuestController::class, 'completed']);
    Route::post('/{quest}/accept', [QuestController::class, 'accept']);
    Route::post('/{quest}/complete', [QuestController::class, 'complete']);
    Route::delete('/{quest}/abandon', [QuestController::class, 'abandon']);
});
