<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'TenSanPham' => 'required|string|max:200',
            'MaDanhMuc' => 'required|string|exists:DANH_MUC,MaDanhMuc',
            'MoTa' => 'nullable|string',
            'GiaBan' => 'required|numeric|min:0',
            'SoLuong' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ];
    }

    public function messages(): array
    {
        return [
            'TenSanPham.required' => 'Vui lòng nhập tên sản phẩm.',
            'TenSanPham.max' => 'Tên sản phẩm không được vượt quá 200 ký tự.',
            'MaDanhMuc.required' => 'Vui lòng chọn danh mục.',
            'MaDanhMuc.exists' => 'Danh mục đã chọn không hợp lệ.',
            'GiaBan.required' => 'Vui lòng nhập giá bán.',
            'GiaBan.numeric' => 'Giá bán phải là số hợp lệ.',
            'GiaBan.min' => 'Giá bán không được nhỏ hơn 0.',
            'SoLuong.required' => 'Vui lòng nhập số lượng tồn kho.',
            'SoLuong.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'SoLuong.min' => 'Số lượng tồn kho không được nhỏ hơn 0.',
            'image.image' => 'File tải lên phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh chỉ hỗ trợ định dạng jpeg, png, jpg, webp.',
            'image.max' => 'Dung lượng hình ảnh không được vượt quá 4MB.',
        ];
    }
}
