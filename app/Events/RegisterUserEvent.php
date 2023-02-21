<?php

namespace App\Events;

use App\Actions\PrepareNotificationPayload;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RegisterUserEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public static string $channelName = 'newUserRegister';
    public array $payload;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(private $user)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $this->payload = PrepareNotificationPayload::make($this->user, 'Good');
        return new PrivateChannel(self::$channelName);
    }
}
