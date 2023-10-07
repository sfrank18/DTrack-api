<?php


Route::middleware(['auth:sanctum'])
    ->apiResource('divisions',App\Api\Divisions\DivisionsController::class)
    ->only('index','store','update','destroy','show');