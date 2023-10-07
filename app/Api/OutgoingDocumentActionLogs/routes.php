<?php


Route::middleware(['auth:sanctum'])
    ->apiResource('outgoing-document-action',App\Api\OutgoingDocumentActionLogs\OutgoingDocumentActionLogsController::class)
    ->only('index','store','update','destroy','show');