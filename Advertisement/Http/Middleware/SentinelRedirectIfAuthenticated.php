<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Sentinel;

class SentinelRedirectIfAuthenticated
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Sentinel::check()) {
            $user = Sentinel::getUser();
            $worker = Sentinel::findRoleByName('Worker');
            $superAdmin = Sentinel::findRoleByName('SuperAdmin');
            if ($user->inRole($worker)) {
                return redirect('кабинет');
            }
            if ($user->inRole($superAdmin)) {
                return redirect('админ-панель');
            }
        }

        return $next($request);
    }
}
