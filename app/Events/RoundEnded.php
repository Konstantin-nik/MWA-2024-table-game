<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoundEnded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;

    /**
     * Create a new event instance.
     */
    public function __construct($room)
    {
        $this->room = $room;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        return new Channel('game.'.$this->room->id);
    }

    public function broadcastAs()
    {
        return 'round.ended';
    }

    public function broadcastWith()
    {
        return [
            'room' => $this->room,
            'message' => 'Round ended!',
        ];
    }
}
