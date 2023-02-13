<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponse;
use App\Traits\UserTrait;
use Illuminate\Http\Request;

class hasVisitorAccess
{
    use UserTrait;
    use HttpResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, \Closure $next)
    {
        if (!$this->hasPermission('visitor', true)) {
            return $this->forbiddenResponse();
        }

        return $next($request);
    }
}
