<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponser
{
    /**
     * @param mixed $data
     * @param string|null $message
     * @param array $additionalData
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse($data, string $message = null, array $additionalData = [], int $code = 200): JsonResponse
    {
        $response = [
            'errors' => null,
            'hasError' => false,
            'message' => $message,
            'object' => $data
        ];

        if (!empty($additionalData)) {
            foreach ($additionalData as $field => $value) {
                $response[$field] = $value;
            }
        }

        return response()->json($response, $code);
    }

    /**
     * @param string|null $message
     * @param int $code
     * @return JsonResponse
     */
    protected function errorResponse(string $message = null, int $code = 500): JsonResponse
    {
        return response()->json([
            'errors' => '',
            'hasError' => true,
            'message' => $message,
            'object' => null
        ], $code);
    }
}
