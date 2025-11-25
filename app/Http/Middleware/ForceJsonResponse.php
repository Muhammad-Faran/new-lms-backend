<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Handle OPTIONS request early (CORS Preflight)
        if ($request->isMethod('OPTIONS')) {
            return response()->json(['status' => 'OK'], 200, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS, PUT, DELETE',
                'Access-Control-Allow-Headers' => '*',
                'Access-Control-Allow-Credentials' => 'true',
            ]);
        }

        try {
            $response = $next($request);
        } catch (Throwable $e) {
            // Catch exceptions and enforce CORS headers in error responses
            $response = response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }

        // Force response type as JSON
        $response->headers->set('Content-Type', 'application/json');

        // Apply CORS headers
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
        $response->headers->set('Access-Control-Allow-Headers', '*');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}
