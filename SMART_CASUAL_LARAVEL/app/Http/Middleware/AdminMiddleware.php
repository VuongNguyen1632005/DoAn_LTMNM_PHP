<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('admin.login')->with('error', 'Vui lòng đăng nhập với tài khoản quản trị.');
        }

        $user = Auth::guard('admin')->user();
        if (!$user->TrangThai || !$user->isStaff()) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->with('error', 'Tài khoản không có quyền truy cập khu vực quản trị.');
        }

        return $next($request);
    }
}
