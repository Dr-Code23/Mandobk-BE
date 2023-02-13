<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponse;
use App\Traits\RoleTrait;
use Illuminate\Http\Request;

class hasProductAccess
{
    use RoleTrait;
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
        if (!in_array($this->getRoleNameForAuthenticatedUser(), [
            'ceo',
            'data_entry',
            'company',
            'storehouse',
            'pharmacy',
            'pharmacy_sub_user',
        ])) {
            return $this->forbiddenResponse();
        }

        return $next($request);
    }
}