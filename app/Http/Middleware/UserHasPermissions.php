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
     * @return JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next, string $roleName, string $excludeCEO = 'yes'): mixed
    {
        if (! $this->hasPermission($roleName, $excludeCEO)) {
            return $this->forbiddenResponse();
        }

        return $next($request);
    }
}
