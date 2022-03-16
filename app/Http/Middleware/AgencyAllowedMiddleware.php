<?php

namespace App\Http\Middleware;

use App\Exceptions\V1\UserException;
use Closure;
use Illuminate\Support\Facades\Auth;

class AgencyAllowedMiddleware
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
        if (Auth::user()->hasAnyRole(['Super Admin','Customer']) || app('agency')->id != Auth::user()->agency_id) {
            throw UserException::unAuthorized();
        }

        $response = $next($request);

        return $response;
    }
}
