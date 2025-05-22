<?php

namespace App\Repositories;

use App\Models\FieldTimeSlotOverride;
use Carbon\Carbon;

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

    public function getInactiveOverridesByWeek($fieldId, $startDate, $endDate)
    {
        return $this->model
            ->with('timeSlot')
            ->where('field_id', $fieldId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'inactive')
            ->get();
    }

    public function getOverridesForFieldInWeek($fieldId, $startDate, $endDate)
    {
        return $this->model
            ->where('field_id', $fieldId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($item) {
                return $item->date . '_' . $item->time_slot_id;
            });
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
