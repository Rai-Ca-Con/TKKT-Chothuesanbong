<?php

namespace App\Services;

use App\Enums\ErrorCode;
use App\Exceptions\AppException;
use App\Repositories\ReceiptRepository;

class ReceiptService
{
    protected $receiptRepository;

    public function __construct(ReceiptRepository $receiptRepository)
    {
        $this->receiptRepository = $receiptRepository;
    }

    public function confirmFullPayment(string $id)
    {
        $receipt = $this->receiptRepository->find($id);

        if (!$receipt) {
            throw new AppException(ErrorCode::RECEIPT_NOT_FOUND);
        }

        if ($receipt->status !== 'paid') {
            throw new AppException(ErrorCode::RECEIPT_NOT_ELIGIBLE_FOR_CONFIRMATION);
        }

        $this->receiptRepository->markAsFullyPaid($id);
    }


}
