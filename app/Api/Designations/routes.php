<?php


Route::middleware(['auth:sanctum'])
    ->apiResource('designations',App\Api\Designations\DesignationsController::class)
    ->only('index','store','update','destroy','show');