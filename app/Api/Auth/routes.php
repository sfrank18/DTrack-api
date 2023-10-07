<?php
use App\Api\Auth\AuthController;

Route::post('/login',[AuthController::class,'login']);

Route::middleware(['auth:sanctum'])->group(function(){
    Route::get('/user',[AuthController::class,'user']);
    Route::post('/change-password',[AuthController::class,'changePassword']);
});