<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * Return success response
     */
    public static function success($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Return error response
     */
    public static function error(string $message = 'Error', $data = null, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Return validation error response
     */
    public static function validationError($errors, string $message = 'Validation Error', int $statusCode = 422): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => [
                'errors' => $errors,
            ],
        ], $statusCode);
    }

    /**
     * Return unauthorized response
     */
    public static function unauthorized(string $message = 'Unauthorized', int $statusCode = 401): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null,
        ], $statusCode);
    }

    /**
     * Return not found response
     */
    public static function notFound(string $message = 'Resource Not Found', int $statusCode = 404): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null,
        ], $statusCode);
    }
}