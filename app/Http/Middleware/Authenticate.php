<?php

namespace App\Http\Middleware;

use App\Traits\UserTrait;
use Auth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Validation\UnauthorizedException;

class Authenticate extends Middleware
{
    use UserTrait;
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }

    /**
     * Override The Authenticate Handle Method To Add JWT Cookie as a header to authenticate.
     *
     * @param mixed $request
     * @param array $guards
     *
     * @return mixed
     */
    public function handle($request, \Closure $next, ...$guards)
    {

        $this->authenticate($request, $guards);

        if (Auth::user()->status != $this->isActive()) {
            Auth::logout();
            return $this->unauthenticatedResponse();
        }
        return $next($request);
    }
}
