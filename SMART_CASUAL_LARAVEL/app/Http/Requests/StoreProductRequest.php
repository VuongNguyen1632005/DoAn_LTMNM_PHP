<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'MaSP' => 'required|string|max:20|unique:SAN_PHAM,MaSP',
            'MaDanhMuc' => 'required|string|exists:DANH_MUC,MaDanhMuc',
            'TenSanPham' => 'required|string|max:200',
            'MoTa' => 'nullable|string',
            'GiaBan' => 'required|numeric|min:0',
            'SoLuong' => 'required|integer|min:0',
            'MaKichThuoc' => 'required|string|exists:KICH_THUOC,MaKichThuoc',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ];
    }

    public function messages(): array
    {
        return [
            'MaSP.required' => 'Vui lòng nhập mã sản phẩm.',
            'MaSP.unique' => 'Mã sản phẩm này đã tồn tại trong hệ thống.',
            'MaSP.max' => 'Mã sản phẩm không được vượt quá 20 ký tự.',
            'MaDanhMuc.required' => 'Vui lòng chọn danh mục.',
            'MaDanhMuc.exists' => 'Danh mục đã chọn không hợp lệ.',
            'TenSanPham.required' => 'Vui lòng nhập tên sản phẩm.',
            'TenSanPham.max' => 'Tên sản phẩm không được vượt quá 200 ký tự.',
            'GiaBan.required' => 'Vui lòng nhập giá bán.',
            'GiaBan.numeric' => 'Giá bán phải là số hợp lệ.',
            'GiaBan.min' => 'Giá bán không được nhỏ hơn 0.',
            'SoLuong.required' => 'Vui lòng nhập số lượng tồn kho.',
            'SoLuong.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'SoLuong.min' => 'Số lượng tồn kho không được nhỏ hơn 0.',
            'MaKichThuoc.required' => 'Vui lòng chọn kích thước mặc định.',
            'MaKichThuoc.exists' => 'Kích thước đã chọn không hợp lệ.',
            'image.image' => 'File tải lên phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh chỉ hỗ trợ định dạng jpeg, png, jpg, webp.',
            'image.max' => 'Dung lượng hình ảnh không được vượt quá 4MB.',
        ];
    }
}
