<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->user()->currentAccessToken();

        if ($token->expires_at && Carbon::now()->greaterThan($token->expires_at)) {
            $token->delete();
            return response()->json(['error' => 'Token has expired.'], 401);
        }

        return $next($request);
    }
}