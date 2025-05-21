<?php

namespace App\Services;

use App\Http\Resources\BookingResource;
use App\Repositories\BookingRepository;
use App\Repositories\FieldRepository;
use App\Repositories\FieldTimeSlotOverrideRepository;
use App\Repositories\FieldTimeSlotRepository;
use App\Repositories\ReceiptRepository;
use App\Repositories\TimeSlotRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Exceptions\AppException;
use App\Enums\ErrorCode;
use Carbon\Carbon;

class BookingService
{
    protected $bookingRepository;
    protected $receiptRepository;
    protected $fieldRepository;
    protected $timeSlotRepository;
    protected $fieldTimeSlotRepository;
    protected $fieldTimeSlotOverrideRepository;

    public function __construct(BookingRepository $bookingRepository, ReceiptRepository $receiptRepository, FieldRepository $fieldRepository, TimeSlotRepository $timeSlotRepository,  FieldTimeSlotRepository $fieldTimeSlotRepository, FieldTimeSlotOverrideRepository $fieldTimeSlotOverrideRepository)
    {
        $this->bookingRepository = $bookingRepository;
        $this->receiptRepository = $receiptRepository;
        $this->fieldRepository = $fieldRepository;
        $this->timeSlotRepository = $timeSlotRepository;
        $this->fieldTimeSlotRepository = $fieldTimeSlotRepository;
        $this->fieldTimeSlotOverrideRepository = $fieldTimeSlotOverrideRepository;
    }

    public function isAvailable($fieldId, $dateStart, $dateEnd): bool
    {
        return $this->bookingRepository->findByFieldAndDate($fieldId, $dateStart, $dateEnd)->isEmpty();
    }

    public function create(array $data)
    {
        // Kiểm tra sân đã đặt chưa
        $available = $this->isAvailable($data['field_id'], $data['date_start'], $data['date_end']);

        if (!$available) {
            throw new AppException(ErrorCode::BOOKING_CONFLICT);
        }
        // Kiểm tra sân có hoạt động không
        $fieldCheck = $this->fieldRepository->find($data['field_id']);
        $fieldCheck->load('state');

        if (!$fieldCheck->state || $fieldCheck->state->name !== 'Active') {
            throw new AppException(ErrorCode::FIELD_NOT_ACTIVE);
        }

        $bookingId = Str::uuid()->toString();
        $data['id'] = $bookingId;

        // 3. Lấy giá và trạng thái theo ngày, giờ bắt đầu
        $date = Carbon::parse($data['date_start'])->toDateString(); // YYYY-MM-DD
        $startTime = Carbon::parse($data['date_start'])->format('H:i:s'); // HH:mm:ss

        $priceAndStatus = $this->getPriceAndStatusForBooking($data['field_id'], $date, $startTime);

//        Log::info($priceAndStatus['status']);

        if ($priceAndStatus['status'] !== 'active') {
            throw new AppException(ErrorCode::TIME_SLOT_INACTIVE);
        }

        // Tạo lịch đặt sân
        $booking = $this->bookingRepository->create($data);

        $totalPrice = $priceAndStatus['price'];
        $depositPrice = $totalPrice * 0.3;

        // Tạo hóa đơn
        $receipt = $this->receiptRepository->create([
            'user_id'     => $data['user_id'],
            'booking_id'  => $bookingId,
            'date'        => now(),
            'total_price' => $totalPrice,
            'deposit_price' => $depositPrice,
            'status'      => 'pending',
            'expired_at' => now()->addMinutes(15),
        ]);

        // 5. Gọi MomoService để tạo thanh toán
//        $momoService = new MomoService();
//        $momoResponse = $momoService->createPayment($totalPrice, $receipt->id);

        // 5. Gọi VNPayService để tạo thanh toán
        $vnpayService = new VNPayService($this->receiptRepository, $this->bookingRepository, $this->fieldRepository);
        $payUrl = $vnpayService->createPaymentUrl($receipt);

        $receipt->payment_url = $payUrl;
        $receipt->save();

        return $booking;

    }

    public function getPriceAndStatusForBooking($fieldId, $date, $startTime)
    {
        $timeSlot = $this->timeSlotRepository->findByStartHour($startTime);
        if (!$timeSlot) {
            throw new AppException(ErrorCode::TIME_SLOT_INVALID);
        }

        // Ưu tiên override nếu có
        $override = $this->fieldTimeSlotOverrideRepository
            ->findByFieldSlotAndDate($fieldId, $timeSlot->id, $date);

        if ($override) {
            return [
                'price' => $override->custom_price,
                'status' => $override->status,
            ];
        }

        // Dùng giá mặc định nếu không có override
        $fieldSlot = $this->fieldTimeSlotRepository
            ->findByFieldAndSlot($fieldId, $timeSlot->id);

        if (!$fieldSlot) {
            throw new AppException(ErrorCode::FIELD_TIME_SLOT_NOT_FOUND);
        }

        return [
            'price' => $fieldSlot->custom_price,
            'status' => $fieldSlot->status,
        ];
    }

    public function findById($id)
    {
        $booking = $this->bookingRepository->find($id);

        if (!$booking) {
            throw new AppException(ErrorCode::BOOKING_NOT_FOUND);
        }
        return $booking;
    }

    public function getByUserId($userId)
    {
        return $this->bookingRepository->findByUser($userId);
    }

    public function getTodayPaidBookingsByUser($userId, $date)
    {
        return $this->bookingRepository->getTodayPaidBookingsByUser($userId, $date);
    }

    public function cancel($id, $userId)
    {
        $booking = $this->bookingRepository->find($id);

        if (!$booking) {
            throw new AppException(ErrorCode::BOOKING_NOT_FOUND);
        }

        // Kiểm tra người dùng hiện tại có quyền huỷ lịch này không
        if ($booking->user_id != $userId) {
            throw new AppException(ErrorCode::UNAUTHORIZED_ACTION);
        }

        // Cập nhật trạng thái hóa đơn liên quan (nếu có)
        $receipt = $this->receiptRepository->findByBookingId($id);
        if ($receipt) {
            $this->receiptRepository->updateStatus($receipt->id, 'cancelled');
        }

        return $this->bookingRepository->delete($id);
    }

//    public function getBookedTimeSlots($fieldId, $date)
//    {
//        $bookings = $this->bookingRepository->getBookingsByFieldAndDate($fieldId, $date);
//
//        $timeSlots = [];
//
//        foreach ($bookings as $booking) {
//            $start = strtotime($booking->date_start);
//            $end = strtotime($booking->date_end);
//
//            while ($start < $end) {
//                $timeSlots[] = date('H:i', $start);
//                $start = strtotime('+1 hour', $start);
//            }
//        }
//
//        return array_unique($timeSlots);
//    }

    public function getBookedTimeSlots($fieldId, $date)
    {
        return $this->bookingRepository->getBookingsByFieldAndDate($fieldId, $date);
    }

    public function getWeeklyBookings(string $date, $fieldId = null)
    {
        $selectedDate = Carbon::parse($date);

        $startOfWeek = $selectedDate->copy();
        $startOfWeek->startOfWeek(Carbon::MONDAY)->startOfDay();

        $endOfWeek = $selectedDate->copy();
        $endOfWeek->endOfWeek(Carbon::SUNDAY)->endOfDay();

        $bookings = $this->bookingRepository->getBookingsByWeek($startOfWeek, $endOfWeek, $fieldId);

        return [
            'start_of_week' => $startOfWeek->toDateString(),
            'end_of_week' => $endOfWeek->toDateString(),
            'bookings' => $bookings,
        ];
    }

    public function getBookingWithReceipt(array $data)
    {
        $startDateTime = Carbon::parse($data['date'] . ' ' . $data['start_time']);
        $endDateTime = Carbon::parse($data['date'] . ' ' . $data['end_time']);

        return $this->bookingRepository
            ->findByFieldAndTime($data['field_id'], $startDateTime, $endDateTime);
    }
}
