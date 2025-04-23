<?php

namespace App\Console\Commands;

use App\Models\Receipt;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckExpiredPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expired-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Lấy các hóa đơn chưa thanh toán và đã hết hạn dựa trên expired_at
        $receipts = Receipt::where('status', 'pending')
            ->where('expired_at', '<=', $now)
            ->get();

        foreach ($receipts as $receipt) {
            // Huỷ đặt sân liên quan nếu có
            if ($receipt->booking) {
                $receipt->booking->delete();
            }

            // Cập nhật trạng thái hóa đơn thành 'expired'
            $receipt->update(['status' => 'expired']);
        }
    }
}
