<?php

namespace App\Http\Requests\FieldRequest;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\ErrorCode;
use App\Exceptions\AppException;
use Illuminate\Contracts\Validation\Validator;

class CreateFieldRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'state_id' => 'required|exists:states,id',
            'price' => 'required|numeric|min:0|max:9999999999.99',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'image' => 'required|array|min:1',
            'image.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'FIELD_NAME_REQUIRED',
            'name.string' => 'FIELD_NAME_MUST_BE_STRING',
            'name.max' => 'FIELD_NAME_TOO_LONG',

            'address.required' => 'FIELD_ADDRESS_REQUIRED',
            'address.string' => 'FIELD_ADDRESS_MUST_BE_STRING',
            'address.max' => 'FIELD_ADDRESS_TOO_LONG',

            'category_id.required' => 'CATEGORY_ID_REQUIRED',
            'category_id.exists' => 'CATEGORY_ID_NOT_FOUND',

            'state_id.required' => 'STATE_ID_REQUIRED',
            'state_id.exists' => 'STATE_ID_NOT_FOUND',

            'price.required' => 'FIELD_PRICE_REQUIRED',
            'price.numeric' => 'FIELD_PRICE_INVALID',
            'price.min' => 'FIELD_PRICE_TOO_LOW',
            'price.max' => 'FIELD_PRICE_TOO_HIGH',

            'description.string' => 'FIELD_DESCRIPTION_INVALID',

            'latitude.numeric' => 'FIELD_LATITUDE_INVALID',
            'latitude.between' => 'FIELD_LATITUDE_OUT_OF_RANGE',

            'longitude.numeric' => 'FIELD_LONGITUDE_INVALID',
            'longitude.between' => 'FIELD_LONGITUDE_OUT_OF_RANGE',

            'image.required' => 'FIELD_IMAGE_REQUIRED',
            'image.array' => 'FIELD_IMAGE_MUST_BE_ARRAY',
            'image.*.image' => 'FIELD_IMAGE_INVALID',
            'image.*.mimes' => 'FIELD_IMAGE_INVALID_TYPE',
            'image.*.max' => 'FIELD_IMAGE_TOO_LARGE',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errorName = $validator->errors()->first();
        throw new AppException(ErrorCode::getCaseName($errorName));
    }
}
