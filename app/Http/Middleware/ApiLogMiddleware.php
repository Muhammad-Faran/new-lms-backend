<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use DB;

class ApiLogMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Proceed with the request
        return $next($request);
    }

    /**
     * Ensure logging happens even if middleware blocks the request.
     */
   public function terminate($request, $response)
{
    $userId = Auth::id();
    $this->logRequest($request, $response, $userId);
}


    /**
     * Log the API request and response
     */
  private function logRequest(Request $request, Response $response, ?int $userId)
{
    try {

        DB::commit(); 

        DB::table('api_logs')->insert([
            'user_id' => $userId,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'request_headers' => json_encode($request->headers->all()),
            'request_body' => json_encode($request->all()),
            'response_body' => json_encode($response->getContent(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'response_status' => $response->getStatusCode(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);


    } catch (\Exception $e) {
        \Log::error("🚨 API Logging failed after commit: " . $e->getMessage());
    }
}



}
