<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    private $commentId;
    public function __construct($commentId)
    {
        $this->commentId = $commentId;
    }

    public function broadcastOn()
    {
        return new Channel('comments');
    }

    //Cau hinh ten su kien ngan gon khi lang nghe phia FE thay vi App\Events\CommentCreated
    public function broadcastAs()
    {
        return 'CommentDeleted';
    }

    // customize kieu du lieu tra ve
    public function broadcastWith()
    {
        return[
            'message' => "Thành công!",
            'content' => $this->commentId
        ] ;

    }
}
