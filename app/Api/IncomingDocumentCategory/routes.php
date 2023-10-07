<?php


Route::middleware(['auth:sanctum'])
    ->apiResource('incoming-document-categories',App\Api\IncomingDocumentCategory\IncomingDocumentCategoryController::class)
    ->only('index','store','update','destroy','show');