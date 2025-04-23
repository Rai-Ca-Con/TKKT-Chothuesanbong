<?php

namespace App\Http\Requests\CommentRequest;

use App\Enums\ErrorCode;
use App\Exceptions\AppException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|min:15',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'image_status' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'content.required' => "COMMENT_CONTENT_NOT_EMPTY",
            'content.min' => "COMMENT_CONTENT_TOO_SHORT",
            'image.image' => "NOT_IMAGE",
            'image.mimes' => "WRONG_FILE_FORMAT",
            'image.max' => "FILE_TOO_LARGE",
            'image_status.required' => "STATE_ID_REQUIRED",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errorName = $validator->errors()->first();
        throw new AppException(ErrorCode::getCaseName($errorName));
    }
}
