<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponser;
use Closure;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class EnsureUserIsAdmin
{
    use ApiResponser;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $isAdmin = Auth::user()->is_admin;
        if(!$isAdmin) {
            return $this->errorResponse("You're not admin", 401);
        }
        return $next($request);
    }
}
