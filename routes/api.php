<?php

use App\Http\Controllers\DonationContributionController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'user']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('donations', [DonationController::class, 'index']);
    Route::post('donations', [DonationController::class, 'store']);
    Route::post('donations/{donation}/contribute', [DonationContributionController::class, 'store']);
    Route::patch('donations/{donation}/status', [DonationController::class, 'updateStatus']);
});