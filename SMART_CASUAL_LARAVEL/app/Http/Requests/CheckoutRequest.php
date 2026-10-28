<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receiver_name' => 'required|string|max:100',
            'receiver_phone' => ['required', 'string', 'regex:/^[0-9]{10,11}$/'],
            'receiver_email' => 'nullable|email|max:100',
            'shipping_address' => 'required|string|max:255',
            'shipping_code' => 'required|string|exists:PHUONG_THUC_VAN_CHUYEN,MaPTVC',
            'payment_method' => 'required|string|in:COD,BANK,MOMO',
            'voucher_code' => 'nullable|string|max:50',
            'note' => 'nullable|string|max:500',
            'idempotency_key' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'receiver_name.required' => 'Vui lòng nhập họ tên người nhận.',
            'receiver_name.max' => 'Họ tên người nhận không được vượt quá 100 ký tự.',
            'receiver_phone.required' => 'Vui lòng nhập số điện thoại người nhận.',
            'receiver_phone.regex' => 'Số điện thoại không hợp lệ (phải gồm 10-11 chữ số).',
            'receiver_phone.max' => 'Số điện thoại không hợp lệ.',
            'receiver_email.email' => 'Email không đúng định dạng.',
            'shipping_address.required' => 'Vui lòng nhập địa chỉ nhận hàng chi tiết.',
            'shipping_address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'shipping_code.required' => 'Vui lòng chọn phương thức vận chuyển.',
            'shipping_code.exists' => 'Phương thức vận chuyển không hợp lệ.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in' => 'Phương thức thanh toán đã chọn không hỗ trợ.',
        ];
    }
}
