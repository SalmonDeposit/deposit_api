<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OAuthRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Services\GoogleOAuth;
use App\Traits\ApiResponser;
use Exception;
use Google\Service\Drive;
use Google\Service\Oauth2;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    use ApiResponser;

    public function login(OAuthRequest $request): JsonResponse {
        try {
            GoogleOAuth::init();

            if (!GoogleOAuth::verifyUserByToken($request->token_id, $request->email))
                throw new Exception(__('Provided token does not match with any user.'));

            $user = User::where(['email' => $request->email])->first();

            // Not supposed to happen once
            if ($user === null) {
                Log::critical('Unexpected situation occurred with email ' . $request->email);
                throw new Exception(__('An error occurred, please try again later.'));
            }

            Auth::login($user);

            $authToken = Auth::user()->createToken('web-auth');

            return $this->successResponse(
                new UserResource(Auth::user()),
                __('Successfully logged in.'),
                [
                    'token' => $authToken->plainTextToken,
                    'expired_at' => $authToken->accessToken->expired_at
                ]
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->errorResponse($e->getMessage());
        }
    }

    public function store(OAuthRequest $request): JsonResponse {
        try {
            GoogleOAuth::init();

            if (!GoogleOAuth::verifyUserByToken($request->token_id, $request->email))
                throw new Exception(__('Provided token does not match with any user.'));

            $password = random_bytes(24);
            $password_hash = Hash::make($password);

            $existingUser = User::where(['email' => (string) $request->email])->first();

            if ($existingUser !== null)
                throw new Exception(__('Email already taken.'));
            
            $user = User::create([
                'email' => $request->email,
                'password' => $password_hash,
                'simon_coin_stock' => 0
            ]);

            if ($user === null || !Auth::attempt([
                'email' => $request->email,
                'password' => $password
            ])) {
                throw new Exception(__('Oops, it looks like something went wrong. Please try again later.'));
            }

            $user->socials()->save(GoogleOAuth::getSocial());

            $authToken = Auth::user()->createToken('web-auth');

            return $this->successResponse(
                new UserResource(Auth::user()),
                __('Successfully logged in.'),
                [
                    'token' => $authToken->plainTextToken,
                    'expired_at' => $authToken->accessToken->expired_at
                ]
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->errorResponse($e->getMessage());
        }
    }
}
