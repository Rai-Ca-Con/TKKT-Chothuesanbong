<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest\BookingRequest;
use App\Http\Resources\BookingResource;
use App\Responses\APIResponse;
use App\Services\BookingService;
use App\Services\VNPayService;
use Illuminate\Http\Request;


class BookingController extends Controller
{
    protected $bookingService;
    protected $vnPayService;

    public function __construct(BookingService $bookingService, VNPayService $vnPayService)
    {
        $this->bookingService = $bookingService;
        $this->vnPayService = $vnPayService;
    }

    // Đặt sân
    public function store(BookingRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $result = $this->bookingService->create($data);
//        $booking = $result['booking'];
        $booking = $result->load('field', 'receipt');

//        return APIResponse::success([
//            'booking' => new BookingResource($booking),
//            'payUrl' => $result['payUrl']
//        ]);

        return APIResponse::success(new BookingResource($booking));
    }

    // Huỷ đặt sân
    public function cancel($id)
    {
        $userId = auth()->id();
        $this->bookingService->cancel($id, $userId);
        return response()->json(['message' => 'Booking cancelled successfully']);
    }

    // Lấy danh sách đặt sân theo user (tuỳ chọn)
    public function userBookings()
    {
        $userId = auth()->id();
        return APIResponse::success(BookingResource::collection($this->bookingService->getByUserId($userId)));
    }

    // Lấy danh sách đặt sân trong ngày (đã thanh toán) => sử dụng cho chat option
    public function userBookingsToday()
    {
        $userId = auth()->id();
        $today = now()->toDateString();

        $bookings = $this->bookingService->getTodayPaidBookingsByUser($userId, $today);

        return APIResponse::success(BookingResource::collection($bookings));
    }





    // Callback của VNPay (IPN)
    public function handleBookingPayment(Request $request)
    {
//        Log::info('VNPay Callback:', $request->all());
        $result = $this->vnPayService->handleCallback($request->all());
        return response()->json($request->all());
    }
}
