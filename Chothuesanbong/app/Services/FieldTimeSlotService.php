<?php
namespace App\Services;

use App\Enums\ErrorCode;
use App\Exceptions\AppException;
use App\Repositories\BookingRepository;
use App\Repositories\FieldTimeSlotOverrideRepository;
use App\Repositories\FieldTimeSlotRepository;
use App\Repositories\TimeSlotRepository;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FieldTimeSlotService
{
    protected $fieldTimeSlotRepository;
    protected $timeSlotRepository;
    protected $bookingRepository;
    protected $fieldTimeSlotOverrideRepository;

    public function __construct(FieldTimeSlotRepository $fieldTimeSlotRepository, TimeSlotRepository $timeSlotRepository, BookingRepository $bookingRepository, FieldTimeSlotOverrideRepository $fieldTimeSlotOverrideRepository)
    {
        $this->fieldTimeSlotRepository = $fieldTimeSlotRepository;
        $this->timeSlotRepository = $timeSlotRepository;
        $this->bookingRepository = $bookingRepository;
        $this->fieldTimeSlotOverrideRepository = $fieldTimeSlotOverrideRepository;
    }

    public function update($id, array $data)
    {
        return $this->fieldTimeSlotRepository->update($id, $data);
    }

    public function overrideByDate(array $data)
    {
        $startHour = Carbon::parse($data['date_start'])->format('H:i:s');
        $date = Carbon::parse($data['date_start'])->toDateString();
        $fieldId = $data['field_id'];

        // Tìm time_slot theo giờ bắt đầu
        $timeSlot = $this->timeSlotRepository->findByStartHour($startHour);
        if (!$timeSlot) {
            throw new AppException(ErrorCode::TIME_SLOT_INVALID);
        }

        // Kiểm tra xem khung giờ này đã được đặt chưa
        if ($this->bookingRepository->existsBookingForFieldAndTimeSlot($fieldId, $date, $timeSlot->start_time)) {
            throw new AppException(ErrorCode::TIME_SLOT_ALREADY_BOOKED);
        }

        // Tìm override
        $override = $this->fieldTimeSlotOverrideRepository->findByFieldSlotAndDate($fieldId, $timeSlot->id, $date);

        $updateData = [];
        if (isset($data['custom_price'])) $updateData['custom_price'] = $data['custom_price'];
        if (isset($data['status'])) $updateData['status'] = $data['status'];

        if ($override) {
            // Cập nhật
            return $this->fieldTimeSlotOverrideRepository->update($override->id, $updateData);
        } else {
            // Tạo mới
            $newData = array_merge($updateData, [
                'id' => Str::uuid()->toString(),
                'field_id' => $fieldId,
                'time_slot_id' => $timeSlot->id,
                'date' => $date,
            ]);

            return $this->fieldTimeSlotOverrideRepository->create($newData);
        }
    }


}
