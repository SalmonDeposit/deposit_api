<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\V1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends ApiController
{
    /**
     * Handle an incoming authentication request.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $authToken = Auth::user()->createToken('web-auth');

        return $this->successResponse(
            new UserResource(Auth::user()),
            __('Successfully logged in.'),
            [
                'token' => $authToken->plainTextToken,
                'expired_at' => $authToken->accessToken->expired_at
            ]
        );
    }

    /**
     * Refresh current user's authentication token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user->tokens()->delete();
        $refreshedToken = $user->createToken('web-auth');

        return $this->successResponse(
            new UserResource(Auth::user()),
            __('Token successfully refreshed.'),
            [
                'token' => $refreshedToken->plainTextToken,
                'expired_at' => $refreshedToken->accessToken->expired_at
            ]
        );
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        //$request->user()->currentAccessToken()->delete();

        Auth::guard('web')->logout();

        return $this->successResponse(
            null,
            __('Logged out successfully.')
        );
    }
}
