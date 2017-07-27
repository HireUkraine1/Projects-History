<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    public function handle($request, Closure $next)
    {
        $bag = \Session::getMetadataBag();
        $max = \Config::get('session.lifetime') * 60;
        if ($bag && $max < (time() - $bag->getLastUsed())) {
            return redirect()->back();
        }
        return $next($request);
    }
}
