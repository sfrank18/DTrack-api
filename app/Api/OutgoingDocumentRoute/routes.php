<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function(){
    Route::apiResource('outgoing-document-routes',App\Api\OutgoingDocumentRoute\OutgoingDocumentRoutesController::class)
    ->only('index','store','update','destroy','show');

    // Route::get('received-documents/outgoing/{department}',"App\Api\OutgoingDocumentRoute\OutgoingDocumentRoutesController@receivedDocuments");
});
    