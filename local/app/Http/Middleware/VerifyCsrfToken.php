<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        'HRM/receive_sms',
        'AVURP/api/*',
        'HRM/api/*',
        'api/*',
        'recruitment/confirmPayment-ipn-v2',
        'recruitment/confirmPayment-ipn-v4',
    ];
}
