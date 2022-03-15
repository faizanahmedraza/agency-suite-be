<?php

namespace App\Http\Middleware;

use App\Exceptions\V1\DomainException;
use App\Exceptions\V1\UserException;
use Closure;
use Illuminate\Support\Facades\Auth;

class AgencyDomainMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!isset(app('agency')->id)) {
            throw DomainException::agencyDomainNotExist();
        }
        $response = $next($request);

        return $response;
    }
}
