<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FriendRequestResponded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $friendRequest;
    public $fromUserId;
    public $toUser;
    public $action;

    /**
     * Create a new event instance.
     */
    public function __construct($friendRequest, $fromUserId, $toUser, $action)
    {
        $this->friendRequest = $friendRequest;
        $this->fromUserId = $fromUserId;
        $this->toUser = $toUser;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('friend-requests.' . $this->fromUserId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'FriendRequestResponded';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $message = $this->action === 'accept' 
            ? 'Lời mời kết bạn đã được chấp nhận bởi ' . $this->toUser->username
            : 'Lời mời kết bạn đã bị từ chối bởi ' . $this->toUser->username;

        return [
            'friend_request' => $this->friendRequest,
            'to_user' => $this->toUser,
            'action' => $this->action,
            'message' => $message,
        ];
    }
}

