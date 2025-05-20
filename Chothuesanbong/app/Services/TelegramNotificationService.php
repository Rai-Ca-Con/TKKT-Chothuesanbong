<?php

namespace App\Services;

use App\Services\IService\INotificationService;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramNotificationService implements INotificationService
{
    public function send($info,$subject)
    {
        $text = "<b>Thông báo ".$subject."</b>"
            . "\n<b>Tên sân: </b>".$info->name
            . "\n<b>Địa chỉ: </b>".$info->address
            . "\n<b>Loại sân: </b>".$info->category->name
            . "\n<b>Trạng thái: </b>".$info->state->name
            . "\n<b>Giá thuê: </b>".$info->price;

        Telegram::sendMessage([
            'chat_id' => '-1002666979466',
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);

    }
}
