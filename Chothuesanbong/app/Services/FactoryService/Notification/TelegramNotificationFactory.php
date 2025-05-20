<?php

namespace App\Services\FactoryService\Notification;

use App\Services\FactoryService\Notification\NotificationFactory;
use App\Services\IService\INotificationService;
use App\Services\TelegramNotificationService;

class TelegramNotificationFactory extends NotificationFactory
{
    public function createNotification(): INotificationService
    {
        return new TelegramNotificationService();
    }
}
