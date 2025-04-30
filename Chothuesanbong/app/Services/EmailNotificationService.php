<?php

namespace App\Services;

use App\Services\IService\INotificationService;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService implements INotificationService
{
    public function send($info,$subject)
    {
        $data = [
            'dateNow' => date('d-m-Y'),
            'nameCus' => $info->user->name,
            'emailCus' => $info->user->email,
            'addressCus' => $info->user->address,
            'phoneCus' => $info->user->phone_number,
            'accountId' => $info->user->id,
            'receiptId' => $info->id,
            'bookingId' => $info->booking_id,
            'nameField' => $info->field->name,
            'timeStart' => $info->booking->date_start,
            'timeEnd' => $info->booking->date_end,
            'amount' => number_format($info->total_price, 0, ',', '.')." VND",
        ];


        // ts1 ten view mail; ts2 du lieu gui sang view
        // ts3 ham xu li logic gui mail.
        Mail::send('notification.email', $data, function ($email) use ($data,$subject) {
            $email->subject("Thông báo thông tin về: ".$subject);
            // gui den dia chi email nao, ten nguoi gui den la gi
            $email->to($data['emailCus'],'Admin_atus'); //tra ve true || false
            // bien email co the dinh kem 1 file
        });
    }
}
