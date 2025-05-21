<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FieldTimeSlotService;

class FieldTimeSlotController extends Controller
{
    protected $fieldTimeSlotService;

    public function __construct(FieldTimeSlotService $fieldTimeSlotService)
    {
        $this->fieldTimeSlotService = $fieldTimeSlotService;
    }

    public function updateByDate(Request $request)
    {
        $data = $request->only(['field_id', 'date_start', 'date_end', 'custom_price', 'status']);

        $result = $this->fieldTimeSlotService->overrideByDate($data);

        return response()->json([
            'message' => 'Cập nhật thành công',
            'data' => $result
        ]);
    }
}
