<?php

namespace App\Http\Middleware;

use App\Traits\UserTrait;
use Closure;
use Illuminate\Http\Request;

class UserHasNoAccess
{

    use UserTrait;
    public function handle(Request $request, Closure $next, string $excludeUser): mixed
    {
        if($this->userHasNoPermissions($excludeUser)){
            return $this->forbiddenResponse();
        }
        return $next($request);
    }
}
