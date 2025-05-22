<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookingResource;
use App\Http\Resources\ReceiptResource;
use App\Responses\APIResponse;
use Illuminate\Http\Request;
use App\Services\StatisticsService;

class StatisticsController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function statsUntilDate(Request $request)
    {
        $date = $request->query('date'); // ví dụ: 2025-05-08

        if (!$date) {
            return APIResponse::error('Vui lòng truyền ngày thống kê.', 400);
        }

        $stats = $this->statisticsService->getBookingStatsUntil($date);

        $result = $stats->map(function ($item) {
            return [
                'field_id'      => $item->field_id,
                'field_name'    => $item->field->name ?? 'Không xác định',
                'total_bookings'=> $item->total_bookings,
            ];
        });

        return APIResponse::success($result);
    }

    public function mostActiveUsers()
    {
        $users = $this->statisticsService->getMostActiveUsers();
        return APIResponse::success($users);
    }

    public function revenueByField(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        if (!$start || !$end) {
            return response()->json(['message' => 'Vui lòng cung cấp start_date và end_date'], 422);
        }

        $revenues = $this->statisticsService->getRevenueByFieldInRange($start, $end);
        return APIResponse::success(ReceiptResource::collection($this->statisticsService->getRevenueByFieldInRange($start, $end)));

//        return response()->json($revenues);
//        return APIResponse::success(FieldResource::collection($this->fieldService->paginate($perPage)));
    }

    public function revenueReport(Request $request)
    {
        $data = $request->only([
            'field_id',
            'start_date',
            'end_date',
            'start_time',
            'end_time',
        ]);

        if (!$data['field_id']) {
            return response()->json(['message' => 'Thiếu field_id'], 422);
        }

        $report = $this->statisticsService->getRevenueAndBookings($data);

        return response()->json([
            'total_revenue' => $report['total_revenue'],
            'bookings' => BookingResource::collection($report['bookings']),
        ]);
    }
}
