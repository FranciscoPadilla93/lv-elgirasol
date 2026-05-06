<?php

namespace App\Helpers;

class ResponseHelper
{
    public static function success($data = null, $message = 'OK', $code = 200, $meta = null)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
            'errors' => null
        ], $code);
    }

    public static function error($message = 'Error', $code = 400, $errors = null)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => null,
            'meta' => null,
            'errors' => $errors
        ], $code);
    }
}
