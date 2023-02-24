<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiController;
use App\Http\Resources\V1\ProfileCollection;
use App\Http\Resources\V1\ProfileResource;
use App\Models\Profile;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class ProfileController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->successResponse(
                new ProfileCollection(
                    Auth::user()->profiles()
                        ->orderByDesc('created_at')
                        ->paginate()
                )
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $profile = Profile::create($request->all());

            return $this->successResponse(
                new ProfileResource($profile),
                __('Profile successfully created.'),
                [],
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Profile $profile
     * @return JsonResponse
     */
    public function show(Profile $profile): JsonResponse
    {
        try {
            if (!$profile->belongsTo(Auth::user()))
                throw new Exception(__('Resource not found.'));

            return $this->successResponse(new ProfileResource($profile));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Profile $profile
     * @return JsonResponse
     */
    public function update(Request $request, Profile $profile): JsonResponse
    {
        try {
            if (!$profile->belongsTo(Auth::user()))
                throw new Exception(__('Resource not found.'));

            $profile->update($request->all());

            return $this->successResponse(
                new ProfileResource($profile),
                __('Profile successfully updated.'),
                []
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Profile $profile
     * @return JsonResponse
     */
    public function destroy(Profile $profile): JsonResponse
    {
        try {
            if (!$profile->belongsTo(Auth::user()))
                throw new Exception(__('Resource not found.'));

            Profile::destroy($profile->id);

            return $this->successResponse(
                null,
                __('Profile deleted.')
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
