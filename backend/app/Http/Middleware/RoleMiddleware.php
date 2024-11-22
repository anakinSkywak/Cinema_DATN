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
        if (!auth()->check()) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng đăng nhập để tiếp tục'
            ], 401);
        }

        // Lấy vai trò của người dùng hiện tại
        $userRole = auth()->user()->vai_tro;

        // Kiểm tra xem vai trò của người dùng có trong danh sách vai trò được phép không
        if (!in_array($userRole, $roles)) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn không có quyền truy cập chức năng này'
            ], 403);
        }

        return $next($request);
    }
}
