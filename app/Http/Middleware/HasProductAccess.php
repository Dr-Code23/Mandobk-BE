<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponse;
use App\Traits\RoleTrait;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HasProductAccess
{
    use RoleTrait, HttpResponse;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return Response|RedirectResponse|JsonResponse
     */
    public function handle(Request $request, Closure $next): Response|JsonResponse|RedirectResponse
    {
        $array = [
            'ceo',
            'data_entry',
            'company',
            'storehouse',
            'pharmacy',
            'pharmacy_sub_user',
        ];

        if (!in_array($this->getRoleNameForAuthenticatedUser(), $array)) {

            return $this->forbiddenResponse();
        }

        return $next($request);
    }
}
