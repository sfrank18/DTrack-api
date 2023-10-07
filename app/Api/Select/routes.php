<?php


Route::middleware(['auth:sanctum'])->group(function(){
    Route::get('select/{source}',[App\Api\Select\SelectController::class,"makeSource"]);
});