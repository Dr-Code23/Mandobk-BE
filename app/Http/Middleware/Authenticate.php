<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    /**
     * Override The Authenticate Handle Method To Add JWT Cookie as a header to authenticate
     * @param mixed $request
     * @param Closure $next
     * @param array $guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if($jwt_cookie = $request->cookie('jwt_token')){
            $request->headers->set('Authorization', "Bearer $jwt_cookie");
        }
        $this->authenticate($request, $guards);

        return $next($request);
    }
}
