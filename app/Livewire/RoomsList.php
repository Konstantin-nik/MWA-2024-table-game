<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;
use Livewire\WithPagination;

class RoomsList extends Component
{
    use WithPagination;

    public $title;
    public $showCreate;
    public $emptyStateMessage;

    protected $listeners = ['refreshRooms' => '$refresh'];

    public function mount($title, $showCreate = false, $emptyStateMessage = null)
    {
        $this->title = $title;
        $this->showCreate = $showCreate;
        $this->emptyStateMessage = $emptyStateMessage;
    }

    public function getRoomsProperty()
    {
        return Room::public()->toJoin()->withCount('users')->get();
    }

    public function render()
    {
        return view('livewire.rooms-list', [
            'rooms' => $this->rooms,
        ]);
    }
}
