<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OAuthRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    use ApiResponser;

    public function login(OAuthRequest $request): JsonResponse {

    }

    public function store(OAuthRequest $request): JsonResponse {
        try {
            $clientID = (string) env('GOOGLE_CLIENT_ID', '');
            $clientSecret = (string) env('GOOGLE_CLIENT_SECRET', '');

            if (empty($clientID) || empty($clientSecret))
                throw new Exception('GOOGLE_CLIENT_ID or GOOGLE_CLIENT_SECRET env key cannot be empty.');

            $client = new \Google_Client();
            $client->setApplicationName('depositapp');
            $client->setAuthConfig([
                'client_id' => $clientID,
                'client_secret' => $clientSecret
            ]);
            $client->setAccessType('offline');
            $client->setApprovalPrompt('force');
            $client->setScopes([
                \Google\Service\Oauth2::USERINFO_PROFILE,
                \Google\Service\Oauth2::USERINFO_EMAIL,
                \Google\Service\Oauth2::OPENID,
                \Google\Service\Drive::DRIVE_METADATA_READONLY // allows reading of google drive metadata
            ]);
            $client->setIncludeGrantedScopes(true);

            $payload = $client->verifyIdToken($request->token_id);

            if ($payload === null)
                throw new Exception(__('Unable to find any user matching those credentials.'));

            $password = random_bytes(24);
            $password_hash = Hash::make($password);

            $user = User::create([
                'email' => (string) $request->email,
                'password' => $password_hash,
                'simon_coin_stock' => 0
            ]);

            if ($user === null || !Auth::attempt([
                'email' => (string) $request->email,
                'password' => $password
            ]))
                throw new Exception(__('Oops, it looks like something went wrong. Please try again later.'));

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
            Log::critical($e->getMessage());
            return $this->errorResponse($e->getMessage());
        }
    }
}
