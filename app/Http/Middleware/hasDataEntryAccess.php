<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponse;
use App\Traits\userTrait;
use Closure;
use Illuminate\Http\Request;

class hasDataEntryAccess
{
    use userTrait;
    use HttpResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->hasPermission('data_entry'))
            return $this->forbiddenResponse();
        return $next($request);
    }
}
