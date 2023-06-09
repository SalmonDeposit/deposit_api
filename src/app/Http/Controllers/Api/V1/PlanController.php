<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PlanCollection;
use App\Models\Plan;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\JsonResponse;

class PlanController extends Controller
{
    use ApiResponser;

    public function index(): JsonResponse
    {
        try {
            return $this->successResponse(new PlanCollection(Plan::all()), '');
        } catch (Exception $e) {
            return $this->errorResponse(__('An error occurred, please try again later.'));
        }
    }
}
