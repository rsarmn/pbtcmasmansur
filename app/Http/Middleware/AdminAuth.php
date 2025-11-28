<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            // kalau belum login, redirect ke /login
            return redirect('/login');
        }

        if (auth()->user()->role !== 'admin') {
            // kalau bukan admin, tolak akses
            return abort(403, 'Akses ditolak!');
        }

        return $next($request);
    }
}
