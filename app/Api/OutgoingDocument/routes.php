<?php
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])
// Route::middleware(['auth:sanctum'])->group(function(){
//     Route::get('outgoing-documents/{hashcode}',App\Api\OutgoingDocument\OutgoingDocumentController::class,"showRecord");

//     Route::apiResource('outgoing-documents',App\Api\OutgoingDocument\OutgoingDocumentController::class)
//     ->only('index','store','update','destroy');

// });

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('outgoing-documents/{hashcode}', 'App\Api\OutgoingDocument\OutgoingDocumentController@update');
    Route::put('outgoing-documents/mark-complete/{hashcode}', 'App\Api\OutgoingDocument\OutgoingDocumentController@markAsComplete');

    Route::apiResource('outgoing-documents', 'App\Api\OutgoingDocument\OutgoingDocumentController')
        ->only('index', 'store', 'destroy','show');
});