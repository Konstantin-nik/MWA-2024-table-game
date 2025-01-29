<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;

class PlayersList extends Component
{
    public $roomId;

    public $room;

    public $participations;

    protected $listeners = ['refreshPlayers' => '$refresh'];

    public function mount($roomId)
    {
        $this->roomId = $roomId;
        $this->loadRoom();
        $this->loadParticipations();
    }

    public function loadRoom()
    {
        $this->room = Room::findOrFail($this->roomId);
    }

    public function loadParticipations()
    {
        $this->participations = $this->room->participations()->with('user')->get();
    }

    public function render()
    {
        return view('livewire.players-list');
    }
}
