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
        // Check if the token exists in a Room that can be joined
        if (! Room::toJoin()->where('invitation_token', $value)->exists()) {
            $fail(__('The provided invitation token is invalid.'));
        }
    }
}
