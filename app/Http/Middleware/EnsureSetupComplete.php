<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\AgencySetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSetupComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        // Don't redirect if already on the setup page
        if ($request->is('admin/setup') || $request->is('admin/setup/*')) {
            return $next($request);
        }

        // Don't redirect for login/logout/password-reset routes
        if ($request->is('admin/login') || $request->is('admin/logout') || $request->is('admin/password-reset/*')) {
            return $next($request);
        }

        if (!AgencySetting::isSetupComplete()) {
            return redirect('/admin/setup');
        }

        return $next($request);
    }
}
