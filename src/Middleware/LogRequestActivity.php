<?php

namespace VaraTech\ActivityMonitor\Middleware;

use Closure;
use Illuminate\Http\Request;
use VaraTech\ActivityMonitor\Facades\ActivityMonitor;

class LogRequestActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Log the request after it's processed
        if ($this->shouldLogRequest($request)) {
            ActivityMonitor::logRequest($request, [
                'response_status' => $response->getStatusCode(),
            ]);
        }

        return $response;
    }

    /**
     * Determine if the request should be logged.
     */
    protected function shouldLogRequest(Request $request): bool
    {
        // Skip logging for certain routes/paths
        $excludedPaths = [
            'telescope/*',
            'horizon/*',
            '_debugbar/*',
            'nova-api/*',
        ];

        foreach ($excludedPaths as $path) {
            if ($request->is($path)) {
                return false;
            }
        }

        return true;
    }
} 