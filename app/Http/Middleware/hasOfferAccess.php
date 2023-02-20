<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponse;
use App\Traits\RoleTrait;
use Closure;
use Illuminate\Http\Request;

class hasOfferAccess
{
    use RoleTrait;
    use HttpResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->roleNameIn(['company', 'storehouse'])) return $this->forbiddenResponse('You Do not have permissions to access offers');
        return $next($request);
    }
}
