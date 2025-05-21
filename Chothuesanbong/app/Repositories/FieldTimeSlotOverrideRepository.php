<?php

namespace App\Repositories;

use App\Models\FieldTimeSlotOverride;

class FieldTimeSlotOverrideRepository
{
    protected $model;

    public function __construct(FieldTimeSlotOverride $model)
    {
        $this->model = $model;
    }

    /**
     * Tìm override theo field_id, time_slot_id và date cụ thể
     */
    public function findByFieldSlotAndDate($fieldId, $timeSlotId, $date)
    {
        return $this->model->where('field_id', $fieldId)
            ->where('time_slot_id', $timeSlotId)
            ->where('date', $date)
            ->first();
    }

    /**
     * Tạo override mới
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Cập nhật override theo ID
     */
    public function update($id, array $data)
    {
        return $this->model->where('id', $id)->update($data);
    }

    /**
     * Xoá override theo ID
     */
    public function delete($id)
    {
        return $this->model->where('id', $id)->delete();
    }
}
