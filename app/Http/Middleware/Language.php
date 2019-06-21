<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;

class Language
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
        $lang = $request->cookie('language');
        $languages = config('nova.language');
        if(isset($lang, $languages[$lang])){
            app()->setLocale($lang);
        }

        /** @var Response $response */
        $response = $next($request);
        $response->cookie('language', app()->getLocale(), 43200, '/', '', false, false);
        return $response;
    }
}
