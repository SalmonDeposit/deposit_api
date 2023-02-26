<?php

namespace App\Http\Controllers\Api\V1\Services;

use App\Http\Controllers\ApiController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class JobController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $providedJob = $request->get('job');
        $providedToken = $request->get('token');

        if (empty($request->input('token')))
            return $this->errorResponse(
                'Page not found',
                404
            );

        try {
            if (empty($providedJob))
                throw new Exception('Provided job is empty.');

            $serviceAccessToken = (string) env('AZURE_FUNCTION_ACCESS_TOKEN');

            if (empty($serviceAccessToken))
                throw new Exception('.env AZURE_FUNCTION_ACCESS_TOKEN key is empty.');

            if ($providedToken === $serviceAccessToken)
                Artisan::call($providedJob);
        } catch (Exception $e) {
            Log::error('[' . self::class . '] ' . $e->getMessage());
            return $this->errorResponse($e->getMessage());
        }

        return $this->successResponse('', '');
    }
}
