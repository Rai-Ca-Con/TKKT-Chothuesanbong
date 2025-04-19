<?php

namespace App\Repositories;

use App\Models\Receipt;
use Illuminate\Support\Str;

class ReceiptRepository
{
    protected $model;

    public function __construct(Receipt $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        $data['id'] = Str::uuid()->toString();
        return $this->model->create($data);
    }

    public function updateStatus($id, $status)
    {
        return $this->model->where('id', $id)->update(['status' => $status]);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findWithBooking($id)
    {
        return $this->model->with(['user', 'booking'])->find($id);
    }

    public function findByBookingId($bookingId)
    {
        return $this->model->where('booking_id', $bookingId)->first();
    }
    public function markAsPaid(Receipt $receipt)
    {
        $receipt->status = 'paid';
        $receipt->save();
    }

    public function markAsCancelled(Receipt $receipt): void
    {
        $receipt->status = 'cancelled';
        $receipt->save();
    }

    public function getRevenueByFieldInRange($startDate, $endDate)
    {
        return $this->model->query()
            ->selectRaw('booking_schedule.field_id, SUM(receipts.total_price) as total_revenue')
            ->join('booking_schedule', 'receipts.booking_id', '=', 'booking_schedule.id')
            ->join('fields', 'booking_schedule.field_id', '=', 'fields.id')
            ->whereBetween('receipts.created_at', [$startDate, $endDate])
            ->where('receipts.status', 'paid')
            ->groupBy('booking_schedule.field_id', 'fields.name')
            ->with('booking.field') // <--- Eager load
            ->get()
            ->map(function ($item) {
                $item->field = \App\Models\Field::find($item->field_id);
                return $item;
            });
    }
}
