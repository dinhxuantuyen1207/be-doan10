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

class NotificationEventAdmin implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id_admin;
    public $notification;

    public function __construct($id_admin, $notification)
    {
        $this->id_admin = $id_admin;
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        return new Channel('adminnotifications');
    }

    public function broadcastAs()
    {
        return 'adminnotification';
    }

    public function broadcastWith()
    {
        return [
            'id_admin' => $this->id_admin,
            'notification' => $this->notification,
        ];
    }
}
