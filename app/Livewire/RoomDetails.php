<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;

class RoomDetails extends Component
{
    public $room;

    public $roomId;

    public $participations;

    public $canEdit;

    public $canDelete;

    public $canStart;

    public $canJoin;

    public $canLeave;

    protected $listeners = ['refreshRoom' => '$refresh'];

    public function mount($roomId)
    {
        $this->roomId = $roomId;
        $this->loadRoom();
    }

    public function loadRoom()
    {
        $this->room = Room::with('participations.user')->findOrFail($this->roomId);
        $this->participations = $this->room->participations;

        $user = auth()->user();
        $this->canEdit = $user->canEditRoom($this->room);
        $this->canDelete = $user->canDeleteRoom($this->room);
        $this->canStart = $user->canStartRoom($this->room);
        $this->canJoin = $user->canJoinRoom($this->room);
        $this->canLeave = $user->canLeaveRoom($this->room);
    }

    public function loadRoomStatus()
    {
        
    }

    public function render()
    {
        return view('livewire.room-details');
    }
}
