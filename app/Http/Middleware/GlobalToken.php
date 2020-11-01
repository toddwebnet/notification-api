<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GlobalToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if(!$request->header('token') == env('GLOBAL_TOKEN')){
            return response('Unauthenticated', 401);
        }
        return $next($request);
    }
}