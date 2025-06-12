<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isManager
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            auth()->check() &&
            auth()->user()->role &&
            in_array(auth()->user()->role->name, ['manager', 'admin'])
        ) {
            return $next($request);
        }

        return response()->json([
            'error' => 'Unauthorized - Manager only'
        ], 403);
    }
}
