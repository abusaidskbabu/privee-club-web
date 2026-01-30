<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class AdminLocale
{
    public function handle($request, Closure $next)
    {
        if (session()->has('admin_language')) {
            App::setLocale(session('admin_language'));
        }

        return $next($request);
    }
}
