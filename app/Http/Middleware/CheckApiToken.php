<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah ada token di session
        if (!session()->has('api_token')) {
            return redirect()->route('login')->withErrors(['nip' => 'Sesi habis, silakan login kembali.']);
        }

        return $next($request);
    }
}