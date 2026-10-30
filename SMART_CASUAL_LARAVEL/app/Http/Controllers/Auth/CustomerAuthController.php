<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\KhachHang;
use App\Services\CartService;

class CustomerAuthController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
    }

    public function showLoginForm()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('home');
        }
        return view('auth.login', ['title' => 'Đăng nhập tài khoản']);
    }

    public function login(LoginRequest $request)
    {
        $loginInput = trim($request->input('login'));
        $password = $request->input('password');

        // Look up by email or username
        $customer = KhachHang::where('Email', $loginInput)
            ->orWhere('TenDangNhap', $loginInput)
            ->first();

        if (!$customer || !Hash::check($password, $customer->MatKhau)) {
            return redirect()->route('login')
                ->withInput($request->only('login'))
                ->withErrors(['login' => 'Tên đăng nhập / Email hoặc mật khẩu không chính xác.']);
        }

        if (!$customer->TrangThai) {
            return redirect()->route('login')
                ->withInput($request->only('login'))
                ->withErrors(['login' => 'Tài khoản của bạn đã bị tạm khóa. Vui lòng liên hệ hỗ trợ.']);
        }

        // Login via guard
        Auth::guard('web')->login($customer, $request->filled('remember'));
        $request->session()->regenerate();

        // Merge guest cart items into customer cart
        $this->cartService->mergeGuestCartToCustomer($customer->MaKH);

        return redirect()->intended(route('home'))->with('success', "Chào mừng bạn trở lại, {$customer->HoTen}!");
    }

    public function showRegisterForm()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('home');
        }
        return view('auth.register', ['title' => 'Đăng ký tài khoản']);
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $newId = 'KH' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));

        $customer = KhachHang::create([
            'MaKH' => $newId,
            'TenDangNhap' => $validated['TenDangNhap'],
            'MatKhau' => Hash::make($validated['MatKhau']),
            'HoTen' => $validated['HoTen'],
            'Email' => $validated['Email'],
            'SoDienThoai' => $validated['SoDienThoai'] ?? null,
            'DiaChi' => $validated['DiaChi'] ?? null,
            'TrangThai' => true,
        ]);

        Auth::guard('web')->login($customer);
        $request->session()->regenerate();

        $this->cartService->mergeGuestCartToCustomer($customer->MaKH);

        return redirect()->route('home')->with('success', 'Đăng ký tài khoản thành công! Chào mừng bạn đến với Smart Casual.');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Bạn đã đăng xuất thành công.');
    }
}
