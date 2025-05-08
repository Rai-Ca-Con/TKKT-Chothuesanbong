<?php

namespace App\Services\FactoryService\Notification;

use App\Services\IService\INotificationService;

abstract class NotificationFactory
{
    abstract public function createNotification() : INotificationService;
}
