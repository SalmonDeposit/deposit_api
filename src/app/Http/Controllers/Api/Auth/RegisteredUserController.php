<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends ApiController
{
    /**
     * Handle an incoming registration request.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $authToken = $user->createToken('web-auth');

            return $this->successResponse(
                new UserResource($user),
                __('Your registration is successful.'),
                [
                    'token' => $authToken->plainTextToken,
                    'expired_at' => $authToken->accessToken->expired_at
                ]
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
