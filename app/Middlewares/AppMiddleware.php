<?php

namespace App\Middlewares;

use Closure;
use Omega\Http\Request;
use Omega\Http\Response;

class AppMiddleware
{
    /**
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
