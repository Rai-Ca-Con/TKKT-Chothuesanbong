<?php

namespace App\Services;


use App\Repositories\BookingRepository;
use App\Repositories\ReceiptRepository;
use App\Repositories\FieldRepository;
use App\Services\FactoryService\Notification\EmailNotificationFactory;
use App\Services\FactoryService\Notification\NotificationFactory;
use Illuminate\Support\Facades\Log;

class VNPayService
{
    protected $fieldRepo;
    protected $receiptRepo;
    protected $bookingScheduleRepo;
    protected $hashSecret;
    protected NotificationFactory $notificationFactory;

    public function __construct(ReceiptRepository $receiptRepo, BookingRepository $bookingScheduleRepo, FieldRepository $fieldRepo)
    {
        $this->receiptRepo = $receiptRepo;
        $this->bookingScheduleRepo = $bookingScheduleRepo;
        $this->fieldRepo = $fieldRepo;
        $this->hashSecret = env('VNP_HASH_SECRET');
    }
    public function handleCallback(array $params)
    {
        $vnp_SecureHash = $params['vnp_SecureHash'] ?? '';
        unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);

        ksort($params);
        $hashData = '';
        $first = true;
        foreach ($params as $key => $value) {
            if (!$first) {
                $hashData .= '&';
            }
            $hashData .= urlencode($key) . '=' . urlencode($value);
            $first = false;
        }

        $generatedHash = hash_hmac('sha512', $hashData, $this->hashSecret);

//        Log::info('VNPay generatedHash vs received:', [
//            'generated' => $generatedHash,
//            'received' => $vnp_SecureHash
//        ]);

        if ($generatedHash != $vnp_SecureHash) {
            return ['RspCode' => '97', 'Message' => 'Invalid signature'];
        }

        $receiptId = $params['vnp_TxnRef'];
        $status = $params['vnp_TransactionStatus'] ?? '';
        $amount = $params['vnp_Amount'] / 100;

        $receipt = $this->receiptRepo->find($receiptId);

        if (!$receipt) {
            return ['RspCode' => '01', 'Message' => 'Receipt not found'];
        }

        if ($receipt->status === 'paid') {
            return ['RspCode' => '02', 'Message' => 'Receipt already paid'];
        }

        if ((int)$receipt->deposit_price !== (int)$amount) {
            return ['RspCode' => '04', 'Message' => 'Amount mismatch'];
        }

        if ($status === '00') {
            // Xử lý đang thanh toán
            $this->receiptRepo->markAsPaid($receipt);
            // Xử lý cho việc gửi thông báo về email
            $receiptId = $params['vnp_TxnRef'];
            $receiptWithUserAndBooking = $this->receiptRepo->findWithBooking($receiptId);
            $field = $this->fieldRepo->findById($receiptWithUserAndBooking->booking->field_id);
            $receiptWithUserAndBooking->field = $field;
            Log::info($receiptWithUserAndBooking);

            // send bill dat san qua email
            $this->notificationFactory = new EmailNotificationFactory();
            $emailNotify = $this->notificationFactory->createNotification();
            $emailNotify->send($receiptWithUserAndBooking,"Đặt sân");

            return ['RspCode' => '00', 'Message' => 'Success'];
        }
        // Thanh toán thất bại
        $this->receiptRepo->markAsCancelled($receipt); // Giả sử bạn có hàm này
        $this->bookingScheduleRepo->delete($receipt->booking_id); // Nếu bạn liên kết booking với receipt

        return ['RspCode' => '00', 'Message' => 'Payment failed or cancelled'];
    }


    public function createPaymentUrl($receipt)
    {
        $vnp_TmnCode = env('VNP_TMNCODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_Url = env('VNP_URL');
        $vnp_ReturnUrl = env('VNP_RETURN_URL');

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => (int)($receipt->deposit_price * 100),
            "vnp_Command" => "pay",
            "vnp_CreateDate" => now()->format('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => request()->ip(),
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => "Thanh toan dat san #" . $receipt->id,
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $receipt->id,
        ];

        ksort($inputData);

        $hashDataArr = [];
        $queryArr = [];

        foreach ($inputData as $key => $value) {
            $encoded = urlencode($key) . '=' . urlencode($value);
            $hashDataArr[] = $encoded;
            $queryArr[] = $encoded;
        }

        $hashData = implode('&', $hashDataArr);
        $query = implode('&', $queryArr);

        $vnp_Url .= '?' . $query;

        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
            $vnp_Url .= '&vnp_SecureHash=' . $vnpSecureHash;
        }

        return $vnp_Url;
    }
}
