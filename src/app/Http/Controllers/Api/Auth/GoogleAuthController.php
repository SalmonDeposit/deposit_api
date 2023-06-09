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

            // We verify to Google that the provided token actually refers to a real Google email address
            if (!GoogleOAuth::verifyUserByToken($request->token_id))
                throw new Exception(__('Provided token does not match with any user.'));

            // From here, we're supposed to have a Social model within our Facade
            $social = GoogleOAuth::getSocial();

            if ($social === null)
                throw new Exception(__('An error occurred when creating your Google account.'));

            // Let's see if the Social is attached
            $user = $social->user()->first();

            // If not, let's see if the user exists in database or create one.
            if ($user === null) {
                $existingUser = User::where(['email' => (string) $request->email])->first();
                if (!$existingUser) {
                    $password = random_bytes(24);
                    $password_hash = Hash::make($password);

                    $user = User::create([
                        'email' => $request->email,
                        'password' => $password_hash,
                        'simon_coin_stock' => 0
                    ]);
                }

                $social->user()->associate($user ?? $existingUser)->save();
            }

            $user = $social->refresh()->user()->first();
            $authToken = $user->createToken('web-auth');

            return $this->successResponse(
                new UserResource($user),
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
