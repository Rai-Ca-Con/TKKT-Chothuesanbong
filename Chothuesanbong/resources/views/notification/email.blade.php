<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            padding: 0 2rem;
        }


        h3 {
            margin: 1rem 0 0 0;
        }
    </style>
</head>

<body>
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 1rem;">
    <tr>
        <td width="50%" style="padding: 0; border: none;">
            <h3>Bill đặt sân bóng</h3>
        </td>
        <td width="50%" style="padding: 0; text-align: right; border: none;">
            <h3>Date: {{$dateNow}}</h3>
        </td>
    </tr>
</table>


<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 1rem">
    <tr>
        <td width="33%" style="vertical-align: top; padding: 10px 0; border: none;">
            <strong>From</strong><br>
            Admin atus<br>
            SupperBowls<br>
            26 De La Thanh, Ha Noi<br>
            SĐT: 0912668866<br>
            Email: atus@supperbowls.com
        </td>
        <td width="33%" style="vertical-align: top; padding: 10px; border: none;">
            <strong>To</strong><br>
            {{$nameCus}}<br>
            Khách hàng<br>
            {{$addressCus}}<br>
            SĐT: {{$phoneCus}}<br>
            Email: {{$emailCus}}
        </td>
        <td width="33%" style="vertical-align: top; padding: 10px; border: none;">
            <strong>Mã hóa đơn: {{$receiptId}}</strong><br><br>
            <b>Mã đặt sân:</b> {{$bookingId}}<br>
            <b>Tài khoản:</b> {{$accountId}}
        </td>
    </tr>
</table>


<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; table-layout: fixed;">
    <thead>
    <tr style="background-color: #f9f9f9;">
        <th style="border: 1px solid #ccc; padding: 8px; text-align: left;">Tên sân</th>
        <th style="border: 1px solid #ccc; padding: 8px; text-align: left;">Giờ bắt đầu</th>
        <th style="border: 1px solid #ccc; padding: 8px; text-align: left;">Giờ kết thúc</th>
        <th style="border: 1px solid #ccc; padding: 8px; text-align: left;">Số tiền thanh toán</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="border: 1px solid #ccc; padding: 8px; word-break: break-word;">{{$nameField}}</td>
        <td style="border: 1px solid #ccc; padding: 8px; word-break: break-word;">{{$timeStart}}</td>
        <td style="border: 1px solid #ccc; padding: 8px; word-break: break-word;">{{$timeEnd}}</td>
        <td style="border: 1px solid #ccc; padding: 8px; word-break: break-word;">{{$amount}}</td>
    </tr>
    </tbody>
</table>
</body>
</html>
