<?php

namespace App\Http\Middleware;

use App\Support\Redirect\RedirectService;
use Closure;

class Redirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $redirectService = new RedirectService();

        if ($redirect = $redirectService->get($request)) {
            return redirect($redirect['newurl'], $redirect['coderedirect']);
        }

        return $next($request);
    }
}