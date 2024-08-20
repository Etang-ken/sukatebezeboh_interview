<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('donations', [UserController::class, 'login'])->name('login');