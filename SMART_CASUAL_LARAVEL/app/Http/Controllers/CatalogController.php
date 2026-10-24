<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SanPham;
use App\Models\DanhMuc;
use App\Models\DanhGia;
use App\Models\ChiTietSanPham;

class CatalogController extends Controller
{
    private const PAGE_SIZE = 16;

    public function index(Request $request)
    {
        $search = trim($request->input('searchName', ''));
        $categoryCode = trim($request->input('category', ''));
        $priceRange = trim($request->input('priceRange', ''));
        $sort = $request->input('sortType', 'default');

        // Query active products with relationships eager loaded to prevent N+1 query
        $query = SanPham::with(['danhMuc', 'anhChinh', 'chiTietSanPhams.kichThuoc', 'danhGias'])
            ->where('TrangThai', true);

        // Search by product name
        if ($search !== '') {
            $query->where('TenSanPham', 'LIKE', '%' . $search . '%');
        }

        // Filter by category
        if ($categoryCode !== '') {
            $query->where(function ($q) use ($categoryCode) {
                $q->where('MaDanhMuc', $categoryCode)
                  ->orWhereHas('danhMuc', function ($catQ) use ($categoryCode) {
                      $catQ->where('TenDanhMuc', $categoryCode);
                  });
            });
        }

        // Filter by price range
        if ($priceRange !== '') {
            [$minPrice, $maxPrice] = $this->parsePriceRange($priceRange);
            if ($maxPrice !== null) {
                $query->whereHas('chiTietSanPhams', function ($q) use ($minPrice, $maxPrice) {
                    $q->where('TrangThai', true)
                      ->whereBetween('GiaBan', [$minPrice, $maxPrice]);
                });
            }
        }

        // Sorting
        if ($sort === 'asc') {
            $query->withMin(['chiTietSanPhams as min_price' => function ($q) {
                $q->where('TrangThai', true);
            }], 'GiaBan')->orderBy('min_price', 'asc');
        } elseif ($sort === 'desc') {
            $query->withMin(['chiTietSanPhams as min_price' => function ($q) {
                $q->where('TrangThai', true);
            }], 'GiaBan')->orderBy('min_price', 'desc');
        } else {
            // Default sort: Áo -> Quần -> Giày, then by MaSP
            $query->orderBy('MaDanhMuc', 'asc')->orderBy('MaSP', 'asc');
        }

        $products = $query->paginate(self::PAGE_SIZE)->withQueryString();

        // Trending items
        $trendingOrder = ['A06', 'G09', 'Q09', 'G06'];
        $trending = SanPham::with(['danhMuc', 'anhChinh', 'chiTietSanPhams', 'danhGias'])
            ->whereIn('MaSP', $trendingOrder)
            ->where('TrangThai', true)
            ->get()
            ->sortBy(function ($item) use ($trendingOrder) {
                return array_search($item->MaSP, $trendingOrder, true);
            });

        $categories = DanhMuc::where('TrangThai', true)->orderBy('MaDanhMuc')->get();

        return view('catalog.index', [
            'title' => 'Thời trang Smart Casual',
            'products' => $products,
            'trending' => $trending,
            'categories' => $categories,
            'search' => $search,
            'category' => $categoryCode,
            'priceRange' => $priceRange,
            'sort' => $sort,
            'total' => $products->total(),
        ]);
    }

    public function show(string $id)
    {
        $product = SanPham::with([
            'danhMuc',
            'hinhAnhs' => function ($q) { $q->where('TrangThai', true)->orderBy('LaAnhChinh', 'desc'); },
            'chiTietSanPhams' => function ($q) { $q->where('TrangThai', true)->with('kichThuoc'); },
            'danhGias' => function ($q) { $q->where('TrangThai', true)->latest(); }
        ])->where('MaSP', $id)->firstOrFail();

        // Related products in the same category
        $related = SanPham::with(['danhMuc', 'anhChinh', 'chiTietSanPhams', 'danhGias'])
            ->where('MaDanhMuc', $product->MaDanhMuc)
            ->where('MaSP', '!=', $product->MaSP)
            ->where('TrangThai', true)
            ->take(4)
            ->get();

        return view('catalog.detail', [
            'title' => $product->TenSanPham,
            'product' => $product,
            'related' => $related,
            'reviews' => $product->danhGias,
        ]);
    }

    public function submitReview(Request $request, string $id)
    {
        $product = SanPham::where('MaSP', $id)->where('TrangThai', true)->firstOrFail();

        $validated = $request->validate([
            'reviewer_name' => 'required|string|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|max:2000',
        ], [
            'reviewer_name.required' => 'Vui lòng nhập họ tên của bạn.',
            'rating.required' => 'Vui lòng chọn số sao đánh giá.',
            'rating.min' => 'Điểm đánh giá tối thiểu là 1 sao.',
            'rating.max' => 'Điểm đánh giá tối đa là 5 sao.',
            'content.required' => 'Vui lòng nhập nội dung nhận xét.',
            'content.max' => 'Nội dung nhận xét không được vượt quá 2000 ký tự.',
        ]);

        $customerId = Auth::guard('web')->check() ? Auth::guard('web')->user()->MaKH : null;
        $reviewId = 'DG' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

        DanhGia::create([
            'MaDanhGia' => $reviewId,
            'MaSP' => $product->MaSP,
            'MaKH' => $customerId,
            'TenKhachHang' => $validated['reviewer_name'],
            'SoSao' => (int) $validated['rating'],
            'NoiDung' => $validated['content'],
            'TrangThai' => true,
        ]);

        return redirect()->back()->with('review_success', 'Cảm ơn bạn đã gửi đánh giá sản phẩm!');
    }

    public function about()
    {
        return view('pages.about', [
            'title' => 'Giới thiệu về Smart Casual',
        ]);
    }

    private function parsePriceRange(string $priceRange): array
    {
        return match ($priceRange) {
            '0-300000' => [0, 300000],
            '300000-600000' => [300000, 600000],
            '600000-1000000' => [600000, 1000000],
            '1000000-999999999' => [1000000, 999999999],
            default => [0, null],
        };
    }
}
