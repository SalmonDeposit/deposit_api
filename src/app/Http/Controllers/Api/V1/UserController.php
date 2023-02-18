<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserCollection;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    /**
     * @return UserCollection
     */
    public function index(): UserCollection
    {
        return new UserCollection(User::paginate());
    }

    /**
     * @param User $user
     * @return UserResource
     */
    public function show(User $user): UserResource
    {
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
