<?php

namespace App\Repositories;

use App\Models\FieldTimeSlot;
use Illuminate\Support\Str;

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

    public function findByFieldId($fieldId)
    {
        return $this->model->where('field_id', $fieldId)->get();
    }

    public function createWithPrice(string $fieldId, string $timeSlotId, float $customPrice, string $status = 'active'): FieldTimeSlot
    {
        return $this->model->create([
            'id'           => Str::uuid(),
            'field_id'     => $fieldId,
            'time_slot_id' => $timeSlotId,
            'status'       => $status,
            'custom_price' => $customPrice,
        ]);
    }
}
