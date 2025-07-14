<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    protected function ok(string $message, array $data = []): JsonResponse
    {
        return $this->success(message: $message, data: $data, statusCode: 200);
    }

    protected function success(string $message, array $data = [], int $statusCode = 200): JsonResponse
    {
        return response()->json(data: [
            'data' => $data,
            'message' => $message,
            'status' => $statusCode,
        ], status: $statusCode);
    }

    protected function error(array|string $errors = [], ?int $statusCode = null): JsonResponse
    {
        if (is_string($errors)) {
            return response()->json(data: [
                'message' => $errors,
                'status' => $statusCode,
            ], status: $statusCode);

        }

        return response()->json([
            'errors' => $errors
        ]);
    }

    protected function notAuthorized(string $message): JsonResponse
    {
        return $this->error([
            'status' => 401,
            'message' => $message,
            'source' => ''
        ]);
    }
}
