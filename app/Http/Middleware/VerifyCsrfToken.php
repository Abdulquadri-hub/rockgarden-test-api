<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/charge-callback/*',
        'api/charge-callback',
        'https://api-rockgarden.degreydigital.com/api/charge-callback',
        'http://api-rockgarden.degreydigital.com/api/charge-callback'
    ];
}
