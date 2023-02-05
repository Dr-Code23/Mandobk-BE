<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponse;
use App\Traits\userTrait;
use Illuminate\Http\Request;

class hasStorehouseAccess
{
    use userTrait;
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
        if (!$this->hasPermission('storehouse', true)) {
            return $this->forbiddenResponse();
        }

        return $next($request);
    }
}
