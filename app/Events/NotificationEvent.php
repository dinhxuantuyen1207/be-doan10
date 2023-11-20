<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ThongBao;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id_user;
    public $notification;

    public function __construct($id_user, $notification)
    {
        $this->id_user = $id_user;
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        return new Channel('notifications');
    }

    public function broadcastAs()
    {
        return 'notification';
    }

    public function broadcastWith()
    {
        return [
            'id_user' => $this->id_user,
            'notification' => $this->notification,
        ];
    }
}
