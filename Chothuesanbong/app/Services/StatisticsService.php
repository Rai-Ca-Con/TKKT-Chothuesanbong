<?php
namespace App\Services;

use App\Repositories\BookingRepository;
use App\Repositories\ReceiptRepository;

class StatisticsService
{
    protected $bookingRepository;
    protected $receiptRepository;

    public function __construct(BookingRepository $bookingRepository, ReceiptRepository $receiptRepository)
    {
        $this->bookingRepository = $bookingRepository;
        $this->receiptRepository = $receiptRepository;
    }

    public function getBookingStatsUntil($date)
    {
        return $this->bookingRepository->countBookingsPerFieldUntil($date);
    }
    public function getRevenueByFieldInRange($startDate, $endDate)
    {
        return $this->receiptRepository->getRevenueByFieldInRange($startDate, $endDate);
    }

    public function getMostActiveUsers()
    {
        return $this->bookingRepository->getMostActiveUsers();
    }

    public function getRevenueAndBookings(array $filters)
    {
        $bookings = $this->bookingRepository->getBookingsWithReceiptsFiltered($filters);

        $totalRevenue = $bookings->sum(function ($booking) {
            if ($booking->receipt->is_fully_paid) {
                return $booking->receipt->total_price;
            }
            return $booking->receipt->deposit_price;
        });

        return [
            'total_revenue' => $totalRevenue,
            'bookings' => $bookings,
        ];
    }
}
