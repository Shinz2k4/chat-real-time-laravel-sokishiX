<?php

namespace App\Events;

use App\Models\Message;
use App\Providers\BroadcastServiceProvider;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
// use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets; // avoid SerializesModels to prevent model re-fetching

    public array $payload;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        $message->load('fromContact');
        $this->payload = [
            'message' => $message->toArray(),
            'from_contact' => optional($message->fromContact)->toArray(),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('messages.' . ($this->payload['message']['to'] ?? ''));
    }

    public function broadcastWith()
    {
        return $this->payload;
    }
}
