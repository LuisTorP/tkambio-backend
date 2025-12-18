<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:api');
Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');

Route::middleware('auth:api')->group(function () {
    Route::post('/generate-report', [ReportController::class, 'generate']);
    Route::get('/get-report/{id}', [ReportController::class, 'get']);
    Route::get('/list-reports', [ReportController::class, 'list']);
});

