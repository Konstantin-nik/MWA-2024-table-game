<?php

namespace App\Rules;

use App\Models\Room;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InvitationTokenIsValid implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $room = Room::toJoin()->where('invitation_token', $value)->first();
        // Check if the token exists in a Room that can be joined
        if (! $room) {
            $fail(__('The provided invitation token is invalid.'));
        } elseif ($room->isFull()) {
            $fail(__('The room is full.'));
        } elseif (auth()->user()->isInAnyRoom()) {
            $fail(__('You are already in the room.'));
        }
    }
}
