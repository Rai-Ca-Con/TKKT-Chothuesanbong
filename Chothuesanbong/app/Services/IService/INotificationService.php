<?php

namespace App\Services\IService;

interface INotificationService
{
    public function send($info,$subject);
}
