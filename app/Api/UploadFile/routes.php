<?php

Route::middleware(['auth:sanctum'])->group(function(){
    Route::post('file-upload',[App\Api\UploadFile\UploadFileController::class,"uploadFile"]);
});