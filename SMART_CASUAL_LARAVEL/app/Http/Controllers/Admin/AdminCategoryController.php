<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DanhMuc;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = DanhMuc::withCount('sanPhams')->get();

        return view('admin.categories.index', [
            'title' => 'Quản lý Danh mục sản phẩm',
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return view('admin.categories.create', [
            'title' => 'Thêm danh mục mới',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'MaDanhMuc' => 'required|string|max:10|unique:DANH_MUC,MaDanhMuc',
            'TenDanhMuc' => 'required|string|max:100',
        ], [
            'MaDanhMuc.required' => 'Vui lòng nhập mã danh mục.',
            'MaDanhMuc.unique' => 'Mã danh mục này đã tồn tại.',
            'TenDanhMuc.required' => 'Vui lòng nhập tên danh mục.',
        ]);

        DanhMuc::create([
            'MaDanhMuc' => strtoupper($validated['MaDanhMuc']),
            'TenDanhMuc' => $validated['TenDanhMuc'],
            'TrangThai' => true,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Thêm danh mục '{$validated['TenDanhMuc']}' thành công!");
    }

    public function edit(string $id)
    {
        $category = DanhMuc::where('MaDanhMuc', $id)->firstOrFail();

        return view('admin.categories.edit', [
            'title' => "Sửa danh mục {$category->TenDanhMuc}",
            'category' => $category,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $category = DanhMuc::where('MaDanhMuc', $id)->firstOrFail();

        $validated = $request->validate([
            'TenDanhMuc' => 'required|string|max:100',
            'TrangThai' => 'required|boolean',
        ], [
            'TenDanhMuc.required' => 'Vui lòng nhập tên danh mục.',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', "Cập nhật danh mục '{$category->TenDanhMuc}' thành công!");
    }

    public function destroy(string $id)
    {
        $category = DanhMuc::withCount('sanPhams')->where('MaDanhMuc', $id)->firstOrFail();

        if ($category->san_phams_count > 0) {
            return redirect()->back()
                ->with('error', "Không thể xóa danh mục '{$category->TenDanhMuc}' vì đang có {$category->san_phams_count} sản phẩm liên kết.");
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Đã xóa danh mục '{$category->TenDanhMuc}' thành công.");
    }
}
