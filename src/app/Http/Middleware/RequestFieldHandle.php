<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class RequestFieldHandle
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $fields = $request->all();

        foreach ($fields as $field => $value) {
            $replaced[!str_contains($field, 'include') ? Str::snake($field) : $field] = $value;
        }

        $request->replace($replaced ?? []);

        return $next($request);
    }
}
