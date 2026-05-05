<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SellerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isSeller()) {
            abort(403, 'Unauthorized access. Seller only area.');
        }

        return $next($request);
    }
}