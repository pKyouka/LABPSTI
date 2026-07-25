<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->remove('X-Powered-By');

        if (! $response->headers->has('Content-Security-Policy')) {
            // DEV: disable CSP entirely to avoid false-positives with Alpine.js / Vite HMR
            if (app()->environment('local')) {
                // no CSP in dev
            } else {
                $nonce = 'nonce-' . Str::random(32);
                $csp  = "default-src 'self'; ";
                $csp .= "script-src 'self' 'unsafe-inline' {$nonce}; ";
                $csp .= "style-src 'self' 'unsafe-inline' https://fonts.bunny.net; ";
                $csp .= "img-src 'self' data: blob: https://fonts.bunny.net; ";
                $csp .= "font-src 'self' data: https://fonts.bunny.net; ";
                $csp .= "connect-src 'self' https://fonts.bunny.net; ";
                $csp .= "frame-ancestors 'self'; ";
                $csp .= "base-uri 'self'; ";
                $csp .= "form-action 'self'";
                $response->headers->set('Content-Security-Policy', $csp);
                $response->headers->set('Content-Security-Policy-Nonce', $nonce);
            }
        }

        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
