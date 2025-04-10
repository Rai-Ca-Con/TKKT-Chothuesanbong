<?php

namespace App\Responses;

use Illuminate\Support\Facades\Hash;

class APIResponse
{
    public static function success($data = null)
    {
        return response()->json([
            'message' => "Thành công!",
            'data' => $data
        ], 200);
    }

    public static function paginated($paginator)
    {
        return response()->json([
            'message' => 'Thành công!',
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ], 200);
    }

    public static function error($message = 'Có lỗi xảy ra', $code = 9999, $data = null)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], 400);
    }
}
