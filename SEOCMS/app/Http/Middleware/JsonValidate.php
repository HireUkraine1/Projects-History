<?php

namespace App\Http\Middleware;

use Closure;

class JsonValidate
{
    /**
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->json()->all()) {
            return response()->json([
                'error' => [
                    'format' => ['Format should be Json.']
                ]
            ],
                422
            );
        }
        return $next($request);
    }
}