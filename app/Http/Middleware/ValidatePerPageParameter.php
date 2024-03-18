<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidatePerPageParameter
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $perPage = $request->query->get('perPage');

        if (empty($perPage) || !ctype_digit($perPage)) {
            $perPage = 5;
        }

        $request->query->add(['perPage' => $perPage]);

        return $next($request);
    }
}
