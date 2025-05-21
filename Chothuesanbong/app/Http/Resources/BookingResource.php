<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'field'      => new FieldResource($this->whenLoaded('field')),
            'receipt' => $this->whenLoaded('receipt', function () {
                return [
                    'total_price' => $this->receipt->total_price,
                    'deposit_price' => $this->receipt->deposit_price,
                    'status'      => $this->receipt->status,
                    'payment_url' => $this->receipt->payment_url,
                    'expired_at'  => $this->receipt->expired_at,
                ];
            }),
            'date_start' => $this->date_start,
            'date_end'   => $this->date_end,
            'created_at' => $this->created_at,
        ];
    }
}
