<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdmin
{
    use ApiResponser;
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure(Request): (Response|RedirectResponse)  $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $isAdmin = (bool) Auth::user()->is_admin;
        if (!$isAdmin) {
            return $this->errorResponse('Unauthorized.', 401);
        }

        return $next($request);
    }
}
