<?php

namespace App\View\Components\Room;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    public $room;

    public $auth;

    /**
     * Create a new component instance.
     */
    public function __construct($room, $auth = false)
    {
        $this->room = $room;
        $this->auth = $auth;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.room.card');
    }
}
