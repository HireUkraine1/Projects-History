<?php

namespace App\Http\Middleware;

use Closure;

class DbLogFinish
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
        $response = $next($request);
        $logFile = fopen(storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_query.log'), 'a+');
        fwrite($logFile, date('Y-m-d H:i:s') . ': ' . 'Finish page: ' . $request->server->get("REQUEST_URI") . PHP_EOL);
        fclose($logFile);
        return $response;
    }
}
