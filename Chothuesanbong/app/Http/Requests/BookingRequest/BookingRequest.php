<?php

namespace App\Http\Requests\BookingRequest;

use App\Enums\ErrorCode;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;


class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'field_id'   => 'required|exists:fields,id',
            'date_start' => 'required|date',
            'date_end'   => 'required|date|after:date_start',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $now = Carbon::now();
            $start = Carbon::parse($this->date_start);

            // Không cho đặt trong quá khứ
            if ($start->lt($now)) {
                $validator->errors()->add('date_start', ErrorCode::BOOKING_START_IN_PAST->message());
            }

            // Không cho đặt quá xa
            if ($start->gt($now->copy()->addMonth())) {
                $validator->errors()->add('date_start', ErrorCode::BOOKING_START_TOO_FAR->message());
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'message' => 'Dữ liệu không hợp lệ!',
            'errors'  => $validator->errors()
        ], 422));
    }
}
