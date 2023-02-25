<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\UserQueryFilter;
use App\Http\Controllers\ApiController;
use App\Http\Requests\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserCollection;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filter = new UserQueryFilter();
            $filterItems = $filter->transform($request);

            $includeDocuments = $request->query('includeDocuments');
            $includeProfiles = $request->query('includeProfiles');

            $users = User::where($filterItems ?? []);

            if (!empty($includeDocuments)) {
                $users->with('documents');
            }

            if (!empty($includeProfiles)) {
                $users->with('profiles');
            }

            return $this->successResponse(
                new UserCollection($users->paginate()->appends($request->query()))
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function show(Request $request, User $user): JsonResponse
    {
        try {
            $includeDocuments = $request->query('includeDocuments');
            $includeProfiles = $request->query('includeProfiles');

            if (!empty($includeDocuments)) {
                $user->loadMissing('documents');
            }

            if (!empty($includeProfiles)) {
                $user->loadMissing('profiles');
            }

            return $this->successResponse(new UserResource($user));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * @param UpdateUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $user->update($request->all());

            return $this->successResponse(new UserResource($user->refresh()));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * @param User $user
     */
    public function delete(User $user)
    {
        // @TODO Do not destroy. Anonymize !
        // return User::destroy($user);
    }
}
