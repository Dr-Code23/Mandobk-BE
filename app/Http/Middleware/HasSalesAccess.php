<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponse;
use App\Traits\RoleTrait;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class hasSalesAccess
{
    use RoleTrait, HttpResponse;

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response|RedirectResponse) $next
     *
     * @return Response|RedirectResponse|JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $array = [
            'company',
            'storehouse',
            'pharmacy',
            'pharmacy_sub_user',
        ];

        if (!in_array($this->getRoleNameForAuthenticatedUser(), $array)) {

            return $this->forbiddenResponse('You do not have permissions to showOneSubUser sales');
        }

        return $next($request);
    }
}
