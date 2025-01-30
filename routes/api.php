<?php

use Illuminate\Support\Facades\Route;

Route::name('api.')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('user/rooms', \App\Http\Controllers\Api\RoomApiController::class);
});
