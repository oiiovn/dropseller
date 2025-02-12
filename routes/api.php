<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TikTokController;
use App\Http\Controllers\NotificationController;


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



// // routes/api.php
// Route::middleware('auth:sanctum')->put('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);

Route::get('/tiktok', [TikTokController::class, 'fetchTikTokData']);

