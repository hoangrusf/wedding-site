<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ThrottlePerIp
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $key = 'throttle:ip:' . $ip;
        $maxAttempts = 15;
        $decaySeconds = 180;

        $attempts = Cache::get($key, 0);
        if ($attempts >= $maxAttempts) {
            return response()->json([
                'message' => 'Bạn đã gửi quá nhiều yêu cầu. Vui lòng thử lại sau.',
            ], 429);
        }

        Cache::put($key, $attempts + 1, $decaySeconds);
        return $next($request);
    }
}
