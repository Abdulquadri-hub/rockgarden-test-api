<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // header('Access-Control-Allow-Origin:  *');
        // header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
        // header('Access-Control-Allow-Methods:  POST, PUT');
        // $request->headers->set('Accept', '*/*');
        // $request->headers->set('Accept-Encoding', 'gzip, deflate, br, zstd');
        // $request->headers->set('Accept-Language', 'en-GB');
        // $request->headers->set('Connection', 'keep-alive');
        // $request->headers->set('Host', 'api.rockgardenehr.com');
        // $request->headers->set('Origin', 'https://admin.rockgardenehr.com');
            return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Allow-Methods', 'GET,HEAD,OPTIONS,POST,PUT"')
            ->header('Access-Control-Allow-Headers', 'Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers, Access-Control-Allow-Origin'); 
    }
}
