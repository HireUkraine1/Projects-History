<?php

namespace App\Http\Middleware;

use Closure;

class UrlName
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
        $url = rawurldecode($request->server->get("REQUEST_URI"));
        $request->server->set('REQUEST_URI', $url);
        return $next($request);
    }
}
