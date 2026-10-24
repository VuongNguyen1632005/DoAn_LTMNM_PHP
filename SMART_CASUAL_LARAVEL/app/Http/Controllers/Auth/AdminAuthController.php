<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\NhanVien;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login', ['title' => 'Đăng nhập Quản trị viên']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập quản trị.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $username = trim($request->input('username'));
        $password = $request->input('password');

        $staff = NhanVien::where('TenDangNhap', $username)->first();

        if (!$staff || !Hash::check($password, $staff->MatKhau)) {
            return redirect()->back()->withInput()->with('error', 'Thông tin đăng nhập quản trị không chính xác.');
        }

        if (!$staff->TrangThai || !$staff->isStaff()) {
            return redirect()->back()->with('error', 'Tài khoản của bạn không có quyền truy cập khu vực quản trị.');
        }

        Auth::guard('admin')->login($staff, $request->filled('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', "Xin chào {$staff->HoTen} ({$staff->ChucVu})!");
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Đã đăng xuất khỏi trang quản trị.');
    }
}
