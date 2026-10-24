<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DanhGia;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');

        $query = DanhGia::with('sanPham');

        if ($status === 'active') {
            $query->where('TrangThai', true);
        } elseif ($status === 'inactive') {
            $query->where('TrangThai', false);
        }

        $reviews = $query->latest()->paginate(20)->withQueryString();

        return view('admin.reviews.index', [
            'title' => 'Quản lý Đánh giá sản phẩm',
            'reviews' => $reviews,
            'selectedStatus' => $status,
        ]);
    }

    public function toggleVisibility(string $id)
    {
        $review = DanhGia::where('MaDanhGia', $id)->firstOrFail();
        $review->update(['TrangThai' => !$review->TrangThai]);

        $msg = $review->TrangThai ? 'Đã cho phép hiển thị đánh giá.' : 'Đã ẩn đánh giá.';
        return redirect()->back()->with('success', $msg);
    }

    public function destroy(string $id)
    {
        $review = DanhGia::where('MaDanhGia', $id)->firstOrFail();
        $review->delete();

        return redirect()->back()->with('success', 'Đã xóa đánh giá thành công.');
    }
}
