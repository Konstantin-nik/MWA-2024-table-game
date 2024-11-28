<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ActionCard extends Component
{
    public $type;
    public $content;

    public static $actionMap = [
        1 => 'Fence action',
        2 => 'Estate action',
        3 => 'Landscape action',
        4 => 'Pool action',
        5 => 'Agent action',
        6 => 'Bis action',
    ];
    /**
     * Create a new component instance.
     */
    public function __construct($type, $content)
    {
        $this->type = $type; // 'action' or 'number'
        $this->content = $content; // Card content (action number or number)
    }

    /**
     * Get the formatted action name if the type is 'action'.
     */
    public function getActionName()
    {
        if ($this->type === 'action') {
            return self::$actionMap[$this->content] ?? 'Unknown action';
        }

        return null;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.action-card');
    }
}
