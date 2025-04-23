<?php

namespace App\Events;

use App\Http\Resources\CommentResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;
    private $commentResource;

    public function __construct(CommentResource $commentResource)
    {
        $this->commentResource = $commentResource;
    }

    // tra ve 1 channel public co ten comments
    public function broadcastOn()
    {
        return new Channel('comments');
    }

    //Cau hinh ten su kien ngan gon khi lang nghe phia FE thay vi App\Events\CommentCreated
    public function broadcastAs()
    {
        return 'CommentUpdated';
    }

    // customize kieu du lieu tra ve
    public function broadcastWith()
    {
        return[
            'message' => "Thành công!",
            'content' => $this->commentResource->resolve()
        ] ;

    }
}
