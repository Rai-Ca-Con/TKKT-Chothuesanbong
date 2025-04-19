<?php

namespace App\Repositories;

use App\Models\BookingSchedule;
use Illuminate\Support\Str;

class BookingRepository
{
    protected $model;

    public function __construct(BookingSchedule $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
//        $data['id'] = (string) Str::uuid();
        return $this->model->create($data);
    }

    public function find($id)
    {
        return $this->model->with(['field'])->find($id);
    }

//    public function findByUser($userId)
//    {
//        return $this->model->with(['field'])
//            ->where('user_id', $userId)
//            ->get();
//    }

    public function findByUser($userId)
    {
        return $this->model->with(['field', 'receipt'])
            ->where('user_id', $userId)
            ->get();
    }

    public function findByFieldAndDate($fieldId, $dateStart, $dateEnd)
    {
        return $this->model->where('field_id', $fieldId)
            ->where(function ($query) use ($dateStart, $dateEnd) {
                $query->where('date_start', '<', $dateEnd)
                    ->where('date_end', '>', $dateStart);
            })
            ->get();
    }

    public function delete($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    public function getTodayPaidBookingsByUser($userId, $date)
    {
        return $this->model->with(['field', 'receipt']) // để resource có đầy đủ thông tin
        ->where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->whereHas('receipt', function ($query) {
                $query->where('status', 'paid');
            })
            ->get();
    }

    public function countBookingsPerFieldUntil($date)
    {
        return $this->model
            ->join('receipts', 'booking_schedule.id', '=', 'receipts.booking_id')
            ->where('receipts.status', 'paid')
            ->whereDate('booking_schedule.date_start', '<=', $date)
            ->selectRaw('booking_schedule.field_id, COUNT(*) as total_bookings')
            ->groupBy('booking_schedule.field_id')
            ->orderByDesc('total_bookings')
            ->with('field') // cần đảm bảo có quan hệ field
            ->get();
    }

    public function getMostActiveUsers()
    {
        // Lấy tất cả booking có receipt 'paid' và eager load user
        $bookings = $this->model->with('user', 'receipt')
            ->whereHas('receipt', function ($query) {
                $query->where('status', 'paid');
            })
            ->get();

        // Nhóm theo user_id và đếm số booking
        $grouped = $bookings->groupBy('user_id')->map(function ($bookings, $userId) {
            $user = $bookings->first()->user;
            return [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'total_bookings' => $bookings->count(),
            ];
        });

        // Sắp xếp theo total_bookings giảm dần và trả về
        return $grouped->sortByDesc('total_bookings')->values();
    }

    public function findByUserAndField($userId, $fieldId)
    {
        return BookingSchedule::where([
            ['user_id', '=', $userId],
            ['field_id', '=', $fieldId],
        ])
            ->whereNull('deleted_at')
            ->count();
    }


}
