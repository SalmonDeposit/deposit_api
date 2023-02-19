<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\V1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
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

        return response()->json([
            'message' => 'Successfully authenticated',
            'data' => [
                'token' => $authToken->plainTextToken,
                'expired_at' => $authToken->accessToken->expired_at,
                'user' => new UserResource(Auth::user())
            ]
        ]);
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

        return response()->json([
            'message' => 'Authentication successfully refreshed',
            'data' => [
                'token' => $refreshedToken->plainTextToken,
                'expired_at' => $refreshedToken->accessToken->expired_at,
                'user' => new UserResource($user->refresh())
            ]
        ]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     * @return Response
     */
    public function destroy(Request $request)
    {
        return response()->json([]);
    }
}
