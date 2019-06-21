<?php

namespace App\Http\Middleware;

use Closure;

class AccountStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //check any account related status
        return $next($request);
    }
}
