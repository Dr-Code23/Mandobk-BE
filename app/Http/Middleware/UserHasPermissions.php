<?php

namespace App\Http\Middleware;

use App\Traits\UserTrait;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserHasPermissions
{
    use UserTrait;
    // use RoleTrait;

    /**
     * @param Request $request
     * @param Closure $next
     * @param string $roleName
     * @param bool $excludeCEO
     * @return JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next, string $roleName, bool $excludeCEO = true): mixed
    {
        if (!$this->hasPermission($roleName, $excludeCEO)) {
            return $this->forbiddenResponse();
        }
        return $next($request);
    }
}
