<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;

class RoomDetailsSection extends Component
{
    public $roomId;

    public $room;

    public $numberOfUsersInRoom;

    protected $listeners = ['refreshRoomDetails' => '$refresh'];

    public function mount($roomId)
    {
        $this->roomId = $roomId;
        $this->loadRoom();
        $this->loadNumberOfUsers();
    }

    public function loadRoom()
    {
        $this->room = Room::findOrFail($this->roomId);
    }

    public function loadNumberOfUsers()
    {
        $this->numberOfUsersInRoom = $this->room->users()->count();
    }

    public function render()
    {
        return view('livewire.room-details-section');
    }
}
