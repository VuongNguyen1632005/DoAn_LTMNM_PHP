<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\SanPham;
use App\Models\DanhMuc;
use App\Models\KichThuoc;
use App\Models\ChiTietSanPham;
use App\Models\HinhAnhSanPham;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $category = trim($request->input('category', ''));
        $status = $request->input('status', 'all');

        $query = SanPham::with(['danhMuc', 'anhChinh', 'chiTietSanPhams.kichThuoc']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('TenSanPham', 'LIKE', '%' . $search . '%')
                  ->orWhere('MaSP', 'LIKE', '%' . $search . '%');
            });
        }

        if ($category !== '') {
            $query->where('MaDanhMuc', $category);
        }

        if ($status === 'active') {
            $query->where('TrangThai', true);
        } elseif ($status === 'inactive') {
            $query->where('TrangThai', false);
        }

        $products = $query->orderBy('MaDanhMuc')->orderBy('MaSP')->paginate(15)->withQueryString();
        $categories = DanhMuc::all();

        return view('admin.products.index', [
            'title' => 'Quản lý Sản phẩm',
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $category,
            'status' => $status,
        ]);
    }

    public function create()
    {
        $categories = DanhMuc::where('TrangThai', true)->get();
        $sizes = KichThuoc::where('TrangThai', true)->get();

        return view('admin.products.create', [
            'title' => 'Thêm sản phẩm mới',
            'categories' => $categories,
            'sizes' => $sizes,
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated) {
            $sp = SanPham::create([
                'MaSP' => strtoupper($validated['MaSP']),
                'MaDanhMuc' => $validated['MaDanhMuc'],
                'TenSanPham' => $validated['TenSanPham'],
                'MoTa' => $validated['MoTa'] ?? null,
                'TrangThai' => true,
            ]);

            // Create initial variant
            $variantId = 'CT' . $sp->MaSP . 'Sz' . $validated['MaKichThuoc'];
            ChiTietSanPham::create([
                'MaCTSP' => $variantId,
                'MaSP' => $sp->MaSP,
                'MaKichThuoc' => $validated['MaKichThuoc'],
                'GiaBan' => (float) $validated['GiaBan'],
                'SoLuong' => (int) $validated['SoLuong'],
                'TrangThai' => true,
            ]);

            // Handle image upload via Laravel Storage
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('uploads/products', 'public');
                $imgId = $sp->MaSP . 'H01';
                HinhAnhSanPham::create([
                    'MaHinhAnh' => $imgId,
                    'MaSP' => $sp->MaSP,
                    'DuongDan' => 'storage/' . $path,
                    'LaAnhChinh' => true,
                    'TrangThai' => true,
                ]);
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', "Thêm sản phẩm '{$validated['TenSanPham']}' thành công!");
    }

    public function edit(string $id)
    {
        $product = SanPham::with(['chiTietSanPhams.kichThuoc', 'anhChinh'])->where('MaSP', $id)->firstOrFail();
        $categories = DanhMuc::where('TrangThai', true)->get();
        $sizes = KichThuoc::where('TrangThai', true)->get();

        $primaryVariant = $product->chiTietSanPhams->first();

        return view('admin.products.edit', [
            'title' => "Chỉnh sửa sản phẩm {$product->TenSanPham}",
            'product' => $product,
            'categories' => $categories,
            'sizes' => $sizes,
            'primaryVariant' => $primaryVariant,
        ]);
    }

    public function update(UpdateProductRequest $request, string $id)
    {
        $product = SanPham::where('MaSP', $id)->firstOrFail();
        $validated = $request->validated();

        DB::transaction(function () use ($request, $product, $validated) {
            $product->update([
                'TenSanPham' => $validated['TenSanPham'],
                'MaDanhMuc' => $validated['MaDanhMuc'],
                'MoTa' => $validated['MoTa'] ?? null,
            ]);

            // Update primary variant price & stock
            $primaryVariant = $product->chiTietSanPhams()->first();
            if ($primaryVariant) {
                $primaryVariant->update([
                    'GiaBan' => (float) $validated['GiaBan'],
                    'SoLuong' => (int) $validated['SoLuong'],
                ]);
            }

            // Image upload replacement
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('uploads/products', 'public');
                $existingImage = $product->anhChinh;
                if ($existingImage) {
                    $existingImage->update(['DuongDan' => 'storage/' . $path]);
                } else {
                    HinhAnhSanPham::create([
                        'MaHinhAnh' => $product->MaSP . 'H' . time(),
                        'MaSP' => $product->MaSP,
                        'DuongDan' => 'storage/' . $path,
                        'LaAnhChinh' => true,
                        'TrangThai' => true,
                    ]);
                }
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', "Cập nhật sản phẩm '{$product->TenSanPham}' thành công!");
    }

    public function destroy(string $id)
    {
        $product = SanPham::where('MaSP', $id)->firstOrFail();

        // Soft delete / Hide product to preserve foreign key constraints with orders
        $product->update(['TrangThai' => false]);

        return redirect()->route('admin.products.index')
            ->with('success', "Đã ẩn sản phẩm '{$product->TenSanPham}' (vẫn giữ toàn vẹn đơn hàng lịch sử).");
    }

    public function restore(string $id)
    {
        $product = SanPham::where('MaSP', $id)->firstOrFail();
        $product->update(['TrangThai' => true]);

        return redirect()->route('admin.products.index')
            ->with('success', "Đã khôi phục sản phẩm '{$product->TenSanPham}'!");
    }
}
