<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room; // Assuming you have a Room model
use App\Models\User; // Assuming you have a User model

class ParticipationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $user->load('rooms');

        $rooms = $user->rooms()->orderByDesc('started_at')->get();
        $rooms->loadCount('users');

        return response()->json($rooms);
    }
}