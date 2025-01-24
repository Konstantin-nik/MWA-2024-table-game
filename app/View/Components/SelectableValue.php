<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectableValue extends Component
{
    public $value;
    public $isSelected;
    public $selectedColor;
    public $unselectedColor;

    /**
     * Create a new component instance.
     */
    public function __construct($value, $isSelected = false, $selectedColor = 'bg-blue-400', $unselectedColor = 'bg-blue-100')
    {
        $this->value = $value;
        $this->isSelected = $isSelected;
        $this->selectedColor = $selectedColor;
        $this->unselectedColor = $unselectedColor;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.selectable-value');
    }
}
