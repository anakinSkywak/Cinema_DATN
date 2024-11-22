<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {

        // Kiểm tra xem người dùng đã đăng nhập chưa
        if(!auth()->check()){
            return response()->json(['message' => 'Bạn không có quyền truy cập'], 403);
        }

        // Kiểm tra xem người dùng có vai trò phù hợp không
        if(!in_array(auth()->user()->role, $roles)){
            return response()->json(['message' => 'Bạn không có quyền truy cập'], 403);
        }

        return $next($request);
    }
}
