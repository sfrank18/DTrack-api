<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function(){
    Route::apiResource('outgoing-document-comments',App\Api\OutgoingDocumentComment\OutgoingDocumentCommentController::class)
    ->only('index','store','update','destroy','show');

});
    