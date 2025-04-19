<?php

namespace App\Services;

use App\Repositories\ReceiptRepository;

class ReceiptService
{
    protected $receiptRepository;

    public function __construct(ReceiptRepository $receiptRepository)
    {
        $this->receiptRepository = $receiptRepository;
    }


}
