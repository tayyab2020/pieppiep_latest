<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\organizations;

class CheckSubdomain
{
    public function handle(Request $request, Closure $next)
    {
        $subdomain = $request->route('shop');

        $organization = organizations::where('subdomain', $subdomain)->first();

        if (!$organization) {
            abort(404, 'Shop not found');
        }

        // Share the organization data across the application
        view()->share('organization', $organization);

        return $next($request);
    }
}
