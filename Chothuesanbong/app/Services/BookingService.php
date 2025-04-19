<?php

namespace App\Services;

use App\Http\Resources\BookingResource;
use App\Repositories\BookingRepository;
use App\Repositories\FieldRepository;
use App\Repositories\ReceiptRepository;
use Illuminate\Support\Str;
use App\Exceptions\AppException;
use App\Enums\ErrorCode;
use Carbon\Carbon;

class BookingService
{
    protected $bookingRepository;
    protected $receiptRepository;
    protected $fieldRepository;

    public function __construct(BookingRepository $bookingRepository, ReceiptRepository $receiptRepository, FieldRepository $fieldRepository)
    {
        $this->bookingRepository = $bookingRepository;
        $this->receiptRepository = $receiptRepository;
        $this->fieldRepository = $fieldRepository;
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

        // Tạo lịch đặt sân
        $booking = $this->bookingRepository->create($data);

        $hours = Carbon::parse($data['date_start'])->floatDiffInHours(Carbon::parse($data['date_end'])); // Convert to hours
        $field = $this->fieldRepository->find($data['field_id']);
        $pricePerHour = $field->price;
        $totalPrice = $hours * $pricePerHour;

        // Tạo hóa đơn
        $receipt = $this->receiptRepository->create([
            'user_id'     => $data['user_id'],
            'booking_id'  => $bookingId,
            'date'        => now(),
            'total_price' => $totalPrice,
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

//        return [
//            'booking' => $booking,
//            'payUrl' => $payUrl
//        ];
        return $booking;

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
}
