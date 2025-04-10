<?php

namespace App\Http\Requests\UserRequest;

use App\Enums\ErrorCode;
use App\Exceptions\AppException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|min:3|max:50',
            'email' => 'required|email|unique:users,email',
            'address' => 'required|min:6|max:255',
            'phone_number' => [
                'required',
                'regex:/^0(3[0-9]|5[6|8|9]|7[0|6-9]|8[1-9]|9[0-9])[0-9]{7}$/',
            ],
            'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            're_password' => 'required|same:password',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => "USERNAME_NOT_NULL",
            'name.min' => "USERNAME_SIZE",
            'name.max' => "USERNAME_SIZE",
            'email.required' => "EMAIL_NOT_NULL",
            'email.email' => "EMAIL_NOT_FORMAT",
            'email.unique' => "EMAIL_EXITED",
            'address.required' => "ADDRESS_NOT_NULL",
            'address.min' => "ADDRESS_SIZE",
            'address.max' => "ADDRESS_SIZE",
            'phone_number.required' => "PHONENUMBER_NOT_NULL",
            'phone_number.regex' => "PHONENUMBER_NOT_FORMAT",
            'password.required' => "PASSWORD_NOT_NULL",
            'password.min' => "PASSWORD_SIZE",
            'password.regex' => "PASSWORD_NOT_FORMAT",
            're_password.required' => "PASSWORD_NOT_NULL",
            're_password.same' => "PASSWORD_NOT_MATCH",
            'avatar.image' => "NOT_IMAGE",
            'avatar.mimes' => "WRONG_FILE_FORMAT",
            'avatar.max' => "FILE_TOO_LARGE",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errorName = $validator->errors()->first();
        throw new AppException(ErrorCode::getCaseName($errorName));
    }
}
