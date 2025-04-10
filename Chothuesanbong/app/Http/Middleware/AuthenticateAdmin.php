<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = auth()->user()->is_admin;
        if(!$role){
            return response()->json([
                'message' => 'Bạn không có quyền truy cập vào tài nguyên',
                'code' => '2000'
            ], 403);
        }
        return $next($request);
    }
}
