<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponse;
use App\Traits\RoleTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Closure;

class hasOfferAccess
{
    use RoleTrait, HttpResponse;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse|JsonResponse
     */
    public function handle(Request $request, Closure $next): Response|JsonResponse|RedirectResponse
    {
        if (!$this->roleNameIn(['company', 'storehouse'])) {

            return $this->forbiddenResponse('You Do not have permissions to access offers');
        }

        return $next($request);
    }
}
