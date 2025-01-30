<?php

use App\Http\Controllers\Api\ParticipationController;
use App\Http\Controllers\Api\RoomApiController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->middleware(['auth:sanctum'])->group(function () {
    // Rooms Routes
    Route::apiResource('user/rooms', RoomApiController::class);
    Route::get('user/owned_rooms', [RoomApiController::class, 'ownedRooms'])->name('owned_rooms');
    Route::post('user/rooms/{id}/start', [RoomApiController::class, 'start'])->name('rooms.start');

    // Participations Routes
    Route::get('user/participations', [ParticipationController::class, 'index']);
    
    // Room Join Routes
    Route::post('user/rooms/{id}/join', [\App\Http\Controllers\Api\RoomJoinController::class, 'join'])->name('rooms.join');
    Route::post('user/rooms/join-by-token', [\App\Http\Controllers\Api\RoomJoinController::class, 'joinByToken'])->name('rooms.join-by-token');
    Route::post('user/rooms/{id}/leave', [\App\Http\Controllers\Api\RoomJoinController::class, 'leave'])->name('rooms.leave');

    // Game Routes
    Route::get('user/game', [\App\Http\Controllers\Api\GameController::class, 'show'])->name('game.show');
    Route::post('user/game/action', [\App\Http\Controllers\Api\GameController::class, 'action'])->name('game.action');
    Route::post('user/game/skip', [\App\Http\Controllers\Api\GameController::class, 'skip'])->name('game.skip');
    Route::get('user/game/{room_id}/end', [\App\Http\Controllers\Api\GameController::class, 'end'])->name('game.end');
});
