<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next , string $roles): Response
    {
        $user = auth('api')->user();

        $roleArray = explode('|', $roles);
        
        if (!$user || !in_array($user->role, $roleArray)) {
            return response()->json([
                'message' => __('auth.unauthorized_access'),
            ], 403);
        }
    
        return $next($request);
    }

}
