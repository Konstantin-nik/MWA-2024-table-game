<?php

namespace App\View\Components\User;

use App\Models\Participation;
use App\Models\Room;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Game extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public Participation $participation)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user.game');
    }
}
