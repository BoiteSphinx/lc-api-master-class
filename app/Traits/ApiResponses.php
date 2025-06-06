<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    protected function ok(string $message): JsonResponse
    {
        return $this->success(message: $message, statusCode: 200);
    }

    protected function success(string $message, int $statusCode = 200): JsonResponse
    {
        return response()->json(data: [
            'message' => $message,
            'status' => $statusCode,
        ], status: $statusCode);
    }
}
