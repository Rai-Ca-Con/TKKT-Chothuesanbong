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

    public function getAll()
    {
        return $this->model->all();
    }

    public function findByStartHour(string $startHour)
    {
        return $this->model
            ->where('start_time', '<=', $startHour)
            ->where('end_time', '>', $startHour)
            ->first();
    }
    public function find($id)
    {
        return $this->model->find($id);
    }
}
