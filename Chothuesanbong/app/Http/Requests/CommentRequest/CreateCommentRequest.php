<?php

namespace App\Http\Requests\CommentRequest;

use App\Enums\ErrorCode;
use App\Exceptions\AppException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CreateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|min:15',
            'field_id' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'parent_id' => 'nullable',
        ];
    }
    public function messages()
    {
        return [
            'content.required' => "COMMENT_CONTENT_NOT_EMPTY",
            'content.min' => "COMMENT_CONTENT_TOO_SHORT",
            'field_id.required' => "FIELD_NOT_EMPTY",
            'image.image' => "NOT_IMAGE",
            'image.mimes' => "WRONG_FILE_FORMAT",
            'image.max' => "FILE_TOO_LARGE",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errorName = $validator->errors()->first();
        throw new AppException(ErrorCode::getCaseName($errorName));
    }
}
