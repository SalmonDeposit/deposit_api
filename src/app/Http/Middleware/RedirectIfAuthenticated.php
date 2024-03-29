<?php

namespace App\Http\Middleware;

use App\Http\Resources\V1\UserResource;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param  string[]|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return response()->json([
                    'message' => 'Already authenticated',
                    'data' => [
                        'user' => new UserResource(Auth::user())
                    ]
                ], 400);
                // return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
