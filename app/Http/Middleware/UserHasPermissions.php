<?php

namespace App\Http\Middleware;

use App\Traits\UserTrait;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserHasPermissions
{
    use UserTrait;

    /**
     * @param Request $request
     * @param Closure $next
     * @param string $roleName
     * @param string $excludeCEO
     * @return JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next, string $roleName, string $excludeCEO = 'yes'): mixed
    {
        if (!$this->hasPermission($roleName, $excludeCEO)) {
            return $this->forbiddenResponse();
        }
        return $next($request);
    }
}
