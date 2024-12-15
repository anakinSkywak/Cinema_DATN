<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeatSelectedEventRealTime
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Public properties for the event
     */
    public $seatId;
    public $status;
    public $userId;
    public $showtimeId;

    /**
     * Create a new event instance.
     */
    public function __construct($seatId, $status, $userId, $showtimeId)
    {
        $this->seatId = $seatId;
        $this->status = $status;
        $this->userId = $userId;
        $this->showtimeId = $showtimeId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('seat-status'),
        ];
    }

    /**
     * Broadcast event name
     */
    public function broadcastAs()
    {
        return 'seat.updated';
    }
}
