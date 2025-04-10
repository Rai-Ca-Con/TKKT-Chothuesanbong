<?php

namespace App\Exceptions;

use App\Enums\ErrorCode;
use Exception;

class AppException extends Exception
{
    protected $errorCode;
    //
    public function __construct(ErrorCode $errorCode)
    {
        parent::__construct($errorCode->message());
        $this->errorCode = $errorCode;
    }

    public function getErrorCode(){
        return $this->errorCode;
    }

    public function render($request)
    {
        return response()->json([
            'code' => $this->errorCode->code(),
            'message' => $this->errorCode->message(),
        ], $this->errorCode->httpStatus());
    }
}
