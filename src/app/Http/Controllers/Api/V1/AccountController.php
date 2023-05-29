<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiController;
use App\Mail\Account\PasswordReset;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AccountController extends ApiController
{
    use ApiResponser;

    /**
     * Reset User password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function passwordReset(Request $request): JsonResponse
    {
        try {
            $email = trim(htmlspecialchars($request->email));

            if (empty($email))
                return $this->errorResponse(__('Email field cannot be empty.'));

            $user = User::where('email', $email)->first();

            if (!empty($user)) {
                $password = bin2hex(random_bytes(6));
                $user->password = Hash::make($password);
                if ($user->save()) {
                    Mail::to($user->email)->send(new PasswordReset($user, $password));
                }
            }

            return $this->successResponse(
                [],
                __('Thank you for your request. You will receive an email shortly.')
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->errorResponse(
                __('An error occurred when proceeding your request. Please try again later.')
            );
        }
    }
}
