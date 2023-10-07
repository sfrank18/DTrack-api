<?php


Route::middleware(['auth:sanctum'])
    ->apiResource('departments',App\Api\Departments\DepartmentsController::class)
    ->only('index','store','update','destroy','show');