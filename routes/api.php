<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TikTokController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProgramController;


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
// Route::middleware('auth:sanctum')->get('/stats', [DashboardController::class, 'getDashboardStats']);


// // routes/api.php
// Route::middleware('auth:sanctum')->put('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);

Route::get('/tiktok', [TikTokController::class, 'fetchTikTokData']);

// API routes cho tự động hóa gói đăng sản phẩm
Route::post('/program/auto-execute/{programShopId}', [ProgramController::class, 'autoExecuteProgram']);
Route::post('/program/auto-complete/{programShopId}', [ProgramController::class, 'autoCompleteProgram']);

// API routes cho quản lý gói
Route::get('/program/{programId}/registration-status', [ProgramController::class, 'getProgramRegistrationStatus']);
Route::get('/program/{programId}/payment-status', [ProgramController::class, 'getProgramPaymentStatus']);

