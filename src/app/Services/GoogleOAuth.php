<?php

namespace App\Services;

use App\Models\Social;
use Carbon\Carbon;
use Exception;
use Google\Service\Drive;
use Google\Service\Oauth2;

class GoogleOAuth
{
    /** @var string */
    private static $id;

    /** @var string */
    private static $secret;

    /** @var \Google_Client */
    private static $client;

    /** @var ?Social */
    private static $social = null;

    /** @throws Exception */
    public function init(): void
    {
        self::$id = (string) env('GOOGLE_CLIENT_ID', '');
        self::$secret = (string) env('GOOGLE_CLIENT_SECRET', '');

        self::setup();
    }

    /**
     * @param string $token
     * @return bool
     * @throws Exception
     */
    public function verifyUserByToken(string $token): bool
    {
        $payload = self::$client->verifyIdToken($token);

        if ($payload === false)
            return false;

        self::extract($payload);

        return true;
    }

    public function getSocial(): ?Social
    {
        return self::$social;
    }

    /** @throws Exception */
    private function extract(array $payload): void
    {
        $social = Social::where(['subscriber_id' => $payload['sub']])->first();

        // We verify that the corresponding Google Account has a Social model
        self::$social = $social ?? Social::create([
            'subscriber_id' => $payload['sub'],
            'firstname' => $payload['given_name'] ?? '',
            'lastname' => $payload['family_name'] ?? '',
            'avatar_url' => $payload['picture'] ?? '',
            'issued_at' => Carbon::parse($payload['iat']),
            'expired_at' => Carbon::parse($payload['exp'])
        ]);
    }

    /**
     * @throws Exception
     * @return void
     */
    private function setup(): void
    {
        self::$client = new \Google_Client();
        self::$client->setApplicationName((string) env('app_name', 'deposit'));
        self::$client->setAuthConfig(['client_id' => self::$id, 'client_secret' => self::$secret]);
        self::$client->setAccessType('offline');
        self::$client->setApprovalPrompt('force');
        self::$client->setScopes([
            Oauth2::USERINFO_PROFILE,
            Oauth2::USERINFO_EMAIL,
            Oauth2::OPENID,
            Drive::DRIVE_METADATA_READONLY
        ]);
        self::$client->setIncludeGrantedScopes(true);
    }
}
