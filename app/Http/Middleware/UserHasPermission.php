<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponse;
use App\Traits\UserTrait;
use Closure;
use Illuminate\Http\Request;

class UserHasPermission
{
    use UserTrait;
    use HttpResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next, string $role = 'ceo', bool $excludeCEO = false)
    {
        if (!$this->hasPermission($role, $excludeCEO)) {
            return $this->forbiddenResponse();
        }
        return $next($request);
    }
}
