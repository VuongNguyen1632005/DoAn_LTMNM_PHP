<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ManagerOnlyMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();
        if (!$user || !$user->isManager()) {
            abort(403, 'Chỉ tài khoản Quản lý (Manager) mới có quyền thực hiện thao tác này.');
        }

        return $next($request);
    }
}
