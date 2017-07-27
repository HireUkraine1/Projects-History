<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;

class SentinelSuperAdminUser
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
        $role = Sentinel::findRoleByName('SuperAdmin');
        if (!$user->inRole($role)) {
            return redirect('/админ-панель/войти');
        }
        return $next($request);
    }
}
