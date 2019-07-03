<?php

namespace App\Http\Middleware;

use Closure;
use stdClass;
use Task23544\ApiResponse;

class Recaptcha
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
        if(!$request->has('g-recaptcha-response')){
            return abort(419);
        }

        $ch = curl_init(config('nova.recaptcha_url'));
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'secret' => config('nova.recaptcha_secret_key'),
                'response' => $request->post('g-recaptcha-response'),
            ]),
        ]);
        $response = json_decode(curl_exec($ch)) ?? new stdClass();
        curl_close($ch);
        if((!$response->success) || ($response->hostname !== $request->getHost())){
            abort(419);
        }
        return $next($request);
    }
}
