<?php

namespace App\Services\FactoryService\Notification;

use App\Services\EmailNotificationService;
use App\Services\FactoryService\Notification\NotificationFactory;
use App\Services\IService\INotificationService;

class EmailNotificationFactory extends NotificationFactory
{
    public function createNotification(): INotificationService
    {
        return new EmailNotificationService();
    }
}
