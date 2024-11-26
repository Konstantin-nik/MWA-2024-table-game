<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\GameController;
use App\Http\Controllers\User\ParticipationController;
use App\Http\Controllers\User\RoomJoinController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Private Routes
Route::name('user.')->middleware(['auth', 'verified'])->group(function () {
    Route::resource('user/rooms', \App\Http\Controllers\User\RoomController::class);
    Route::post('user/rooms/{id}/join', [RoomJoinController::class, 'join'])->name('rooms.join');
    Route::post('user/rooms/join', [RoomJoinController::class, 'joinByToken'])->name('rooms.token.join');
    Route::post('user/rooms/{id}/leave', [RoomJoinController::class, 'leave'])->name('rooms.leave');
    Route::get('user/participations', [ParticipationController::class, 'index'])->name('participations');
    Route::get('user/owned_rooms', [\App\Http\Controllers\User\RoomController::class, 'ownedRooms'])->name('owned_rooms');
    Route::post('user/rooms/{id}/start', [\App\Http\Controllers\User\RoomController::class, 'start'])->name('rooms.start');
    Route::get('user/game/{id}', [GameController::class, 'show'])->name('game');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
