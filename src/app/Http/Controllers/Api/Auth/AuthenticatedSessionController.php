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

        $authToken = Auth::user()->createToken('auth');

        return response()->json([
            'message' => 'Successfully authenticated',
            'data' => [
                'token' => $authToken->plainTextToken,
                'user' => new UserResource(Auth::user())
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
