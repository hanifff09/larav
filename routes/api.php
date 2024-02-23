<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('/regis', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/log', [AuthController::class, 'logout']);
Route::get('/show', [AuthController::class, 'show']);
Route::delete('/delete/{id}', [AuthController::class, 'deleteUser']);
Route::get('/get/{id}', [AuthController::class, 'getUser']);
Route::post('/update/{id}', [AuthController::class, 'updateUser']);
