<?php


Route::middleware(['auth:sanctum'])
    ->apiResource('outgoing-document-categories',App\Api\OutgoingDocumentCategory\OutgoingDocumentCategoryController::class)
    ->only('index','store','update','destroy','show');