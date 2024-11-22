<?php

namespace App\View\Components;

use App\Models\Room;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use phpDocumentor\Reflection\Types\Boolean;

class TiledRoomsLayout extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public Collection $rooms,
        public bool $showcreate=false,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('layouts.tiled-rooms-layout');
    }
}
