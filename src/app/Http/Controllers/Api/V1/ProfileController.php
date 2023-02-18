<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProfileCollection;
use App\Http\Resources\V1\ProfileResource;
use App\Models\Profile;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ProfileCollection
     */
    public function index(): ProfileCollection
    {
        return new ProfileCollection(
            Auth::user()->profiles()
                ->orderByDesc('created_at')
                ->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return ProfileResource
     */
    public function store(Request $request): ProfileResource
    {
        $profile = Profile::create($request->all());

        return new ProfileResource($profile);
    }

    /**
     * Display the specified resource.
     *
     * @param Profile $profile
     * @return ProfileResource|JsonResponse
     */
    public function show(Profile $profile)
    {
        if ($profile->belongsTo(Auth::user())) {
            return new ProfileResource($profile);
        }

        return Response::json([], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Profile $profile
     * @return ProfileResource|JsonResponse
     */
    public function update(Request $request, Profile $profile)
    {
        if ($profile->belongsTo(Auth::user())) {
            $profile->update($request->all());
            return new ProfileResource($profile);
        }

        return Response::json([], 404);
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
            if ($profile->belongsTo(Auth::user())) {
                $result = (bool) Profile::destroy($profile->id);
            }
        } catch (Exception $e) {
            $result = $e->getMessage();
        } finally {
            return Response::json([
                'data' => $result ?? false
            ]);
        }
    }
}
