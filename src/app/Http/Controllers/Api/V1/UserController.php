<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\UserQueryFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserCollection;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return UserCollection
     */
    public function index(Request $request): UserCollection
    {
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

        return new UserCollection($users->paginate()->appends($request->query()));
    }

    /**
     * @param Request $request
     * @param User $user
     * @return UserResource
     */
    public function show(Request $request, User $user): UserResource
    {
        $includeDocuments = $request->query('includeDocuments');
        $includeProfiles = $request->query('includeProfiles');

        if (!empty($includeDocuments)) {
            $user->loadMissing('documents');
        }

        if (!empty($includeProfiles)) {
            $user->loadMissing('profiles');
        }

        return new UserResource($user);
    }

    /**
     * @param UpdateUserRequest $request
     * @param User $user
     * @return UserResource|JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if ($request->getMethod() === 'PUT') {
            return Response::json([
                'message' => 'Unsupported HTTP method. Try with patch.'
            ], 405);
        }

        $user->update($request->all());

        return new UserResource($user);
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
