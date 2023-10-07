<?php

Route::middleware(['auth:sanctum'])
    ->apiResource('users',App\Api\Users\UserController::class)
    ->only('index','store','update','destroy','show');

