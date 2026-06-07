<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UnitController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/properties', [PropertyController::class, 'store']);
Route::get('/properties', [PropertyController::class, 'index']);
Route::post('/properties/{property}/units', [UnitController::class, 'store']);
    Route::get('/properties/{property}/units', [UnitController::class, 'index']);
    Route::post('/caretakers', [AuthController::class, 'registerCaretaker']);
Route::post('/tenants', [AuthController::class, 'registerTenant']);
});