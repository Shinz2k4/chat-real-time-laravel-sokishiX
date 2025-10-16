<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FriendRequestSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $friendRequest;
    public $fromUser;
    public $toUserId;

    /**
     * Create a new event instance.
     */
    public function __construct($friendRequest, $fromUser, $toUserId)
    {
        $this->friendRequest = $friendRequest;
        $this->fromUser = $fromUser;
        $this->toUserId = $toUserId;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('friend-requests.' . $this->toUserId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'FriendRequestSent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'friend_request' => $this->friendRequest,
            'from_user' => $this->fromUser,
            'message' => 'Bạn có lời mời kết bạn mới từ ' . $this->fromUser->username,
        ];
    }
}

