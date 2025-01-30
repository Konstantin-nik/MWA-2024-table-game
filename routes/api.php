<?php

use App\Http\Controllers\Api\ParticipationController;
use App\Http\Controllers\Api\RoomApiController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('user/rooms', RoomApiController::class);
    Route::get('user/participations', [ParticipationController::class, 'index']);
});
