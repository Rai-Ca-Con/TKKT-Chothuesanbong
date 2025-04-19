<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReceiptResource;
use App\Responses\APIResponse;
use App\Services\ReceiptService;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    protected $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }


}
