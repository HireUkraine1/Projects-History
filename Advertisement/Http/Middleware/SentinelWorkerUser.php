<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;

class SentinelWorkerUser
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
        $user = Sentinel::getUser();
        $role = Sentinel::findRoleByName('Worker');
        if (!$user->inRole($role)) {
            return redirect('/');
        }
        return $next($request);
    }
}
