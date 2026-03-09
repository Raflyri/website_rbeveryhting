<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Allow cross-origin requests from the base64tools static subdomain.
 *
 * Applied only to the /api/base64/* route group so it does not affect
 * any other part of the application.
 */
class CorsBase64Tools
{
    private const ALLOWED_ORIGINS = [
        'https://base64tools.rbeverything.com',
        'http://localhost',        // local dev
        'http://127.0.0.1',       // local dev
        'null',                   // file:// origin (open index.html directly)
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->header('Origin', '');

        // Pre-flight OPTIONS request — return immediately with CORS headers
        if ($request->isMethod('OPTIONS')) {
            return $this->addHeaders(response('', 204), $origin);
        }

        /** @var Response $response */
        $response = $next($request);

        return $this->addHeaders($response, $origin);
    }

    private function addHeaders(Response $response, string $origin): Response
    {
        // Only echo back the origin if it is in the allow-list
        $allowedOrigin = in_array($origin, self::ALLOWED_ORIGINS, true)
            ? $origin
            : self::ALLOWED_ORIGINS[0];

        $response->headers->set('Access-Control-Allow-Origin', $allowedOrigin);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Accept');
        $response->headers->set('Access-Control-Max-Age', '600');

        return $response;
    }
}
