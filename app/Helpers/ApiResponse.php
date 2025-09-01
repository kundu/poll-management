<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Universal API response method
     *
     * @param mixed $data Response data
     * @param string $message Response message
     * @param int $code HTTP status code
     * @param bool $success Success status
     * @return JsonResponse
     */
    public static function response($data = null, string $message = 'Success', int $code = 200, bool $success = true): JsonResponse
    {
        $response = [
            'success' => $success,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }
}
