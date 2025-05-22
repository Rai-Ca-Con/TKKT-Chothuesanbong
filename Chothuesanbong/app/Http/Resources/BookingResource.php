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
            'user'       => $this->whenLoaded('user', function () {
                return [
                    'id'           => $this->user->id,
                    'name'         => $this->user->name,
                    'email'        => $this->user->email,
                    'phone_number' => $this->user->phone_number,
                    'address'      => $this->user->address,
                ];
            }),
            'field'      => new FieldResource($this->whenLoaded('field')),
            'receipt' => $this->whenLoaded('receipt', function () {
                return [
                    'id' => $this->receipt->id,
                    'total_price' => $this->receipt->total_price,
                    'deposit_price' => $this->receipt->deposit_price,
                    'is_fully_paid' => $this->receipt->is_fully_paid,
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
