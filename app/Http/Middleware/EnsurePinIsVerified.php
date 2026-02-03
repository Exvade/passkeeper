<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePinIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Kalau user belum login, biarkan (nanti dihandle auth middleware)
        if (!auth()->check()) {
            return $next($request);
        }

        // 2. Cek apakah user sudah punya PIN?
        // Kalau belum punya (pengguna baru), paksa ke halaman Setup PIN
        if (is_null(auth()->user()->pin)) {
            if ($request->routeIs('pin.setup', 'pin.create')) {
                return $next($request);
            }
            return redirect()->route('pin.create');
        }

        // 3. Cek apakah di sesi ini PIN sudah terverifikasi?
        if (!$request->session()->get('pin_verified')) {
            // Kalau akses halaman verifikasi PIN, boleh lewat
            if ($request->routeIs('pin.verify', 'pin.check')) {
                return $next($request);
            }
            // Selain itu, tendang ke layar kunci
            return redirect()->route('pin.verify');
        }

        return $next($request);
    }
}