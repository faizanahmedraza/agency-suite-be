<?php

namespace App\Http\Middleware;

use App\Exceptions\V1\AgencyException;
use App\Exceptions\V1\DomainException;
use App\Exceptions\V1\UserException;
use App\Models\Agency;
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

        if (app('agency')->status == Agency::STATUS['blocked']) {
            throw AgencyException::blocked();
        }

        $response = $next($request);

        return $response;
    }
}
