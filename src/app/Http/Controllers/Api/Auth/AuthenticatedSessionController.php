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
            'errors' => null,
            'hasError' => false,
            'message' => 'Successfully authenticated.',
            'token' => $authToken->plainTextToken,
            'expired_at' => $authToken->accessToken->expired_at,
            'object' => new UserResource(Auth::user())
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
            'errors' => null,
            'hasError' => false,
            'message' => 'Authentication successfully refreshed.',
            'token' => $refreshedToken->plainTextToken,
            'expired_at' => $refreshedToken->accessToken->expired_at,
            'object' => new UserResource($user->refresh())
        ]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        Auth::user()
            ->tokens()
            ->where('name', 'web-auth')
            ->delete();

        return response()->json([
            'errors' => null,
            'hasError' => false,
            'message' => 'Logged out successfully.',
            'object' => null
        ]);
    }
}
