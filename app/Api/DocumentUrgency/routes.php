<?php


Route::middleware(['auth:sanctum'])
    ->apiResource('urgencies',App\Api\DocumentUrgency\DocumentUrgencyController::class)
    ->only('index','store','update','destroy','show');