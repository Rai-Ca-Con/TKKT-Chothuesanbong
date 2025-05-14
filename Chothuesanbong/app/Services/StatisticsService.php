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
}
