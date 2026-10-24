<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'HoTen' => 'required|string|max:100',
            'Email' => 'required|email|max:100|unique:KHACH_HANG,Email',
            'TenDangNhap' => 'required|string|max:100|unique:KHACH_HANG,TenDangNhap',
            'MatKhau' => 'required|string|min:6|confirmed',
            'SoDienThoai' => 'nullable|string|max:20',
            'DiaChi' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'HoTen.required' => 'Vui lòng nhập họ và tên.',
            'HoTen.max' => 'Họ tên không được vượt quá 100 ký tự.',
            'Email.required' => 'Vui lòng nhập địa chỉ email.',
            'Email.email' => 'Địa chỉ email không đúng định dạng.',
            'Email.unique' => 'Địa chỉ email này đã được sử dụng để đăng ký.',
            'TenDangNhap.required' => 'Vui lòng nhập tên đăng nhập.',
            'TenDangNhap.unique' => 'Tên đăng nhập này đã được sử dụng.',
            'MatKhau.required' => 'Vui lòng nhập mật khẩu.',
            'MatKhau.min' => 'Mật khẩu phải có tối thiểu 6 ký tự.',
            'MatKhau.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ];
    }
}
