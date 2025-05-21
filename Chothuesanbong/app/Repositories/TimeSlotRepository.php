<?php

namespace App\Repositories;

use App\Models\TimeSlot;

class TimeSlotRepository
{
    protected $model;

    public function __construct(TimeSlot $model)
    {
        $this->model = $model;
    }

    public function findByStartHour(string $startHour)
    {
        return $this->model
            ->where('start_time', '<=', $startHour)
            ->where('end_time', '>', $startHour)
            ->first();
    }
}
