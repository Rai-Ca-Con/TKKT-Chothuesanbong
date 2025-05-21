<?php

namespace App\Repositories;

use App\Models\FieldTimeSlot;

class FieldTimeSlotRepository
{
    protected $model;

    public function __construct(FieldTimeSlot $model)
    {
        $this->model = $model;
    }

    public function findActiveByFieldAndTimeSlot(string $fieldId, string $timeSlotId)
    {
        return $this->model
            ->where('field_id', $fieldId)
            ->where('time_slot_id', $timeSlotId)
            ->where('status', 'active')
            ->first();
    }

    public function update($id, array $data)
    {
        $record = $this->model->find($id);
        $record->update($data);
        return $record;
    }

    public function findByFieldAndSlot($fieldId, $timeSlotId)
    {
        return $this->model
            ->where('field_id', $fieldId)
            ->where('time_slot_id', $timeSlotId)
            ->first();
    }
}
