<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\DanhMuc;
use App\Models\SanPham;
use App\Models\ChiTietSanPham;
use App\Models\DanhGia;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CatalogAndProductTest extends TestCase
{
    use DatabaseTransactions;
    public function test_database_relationships_between_category_and_product(): void
    {
        $category = DanhMuc::first();
        $this->assertNotNull($category);

        // HasMany
        $products = $category->sanPhams;
        $this->assertTrue($products->isNotEmpty());

        // BelongsTo
        $firstProduct = $products->first();
        $this->assertEquals($category->MaDanhMuc, $firstProduct->danhMuc->MaDanhMuc);

        // Product has variants
        $this->assertTrue($firstProduct->chiTietSanPhams->isNotEmpty());
    }

    public function test_storefront_home_page_loads_with_products(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SMART');
        $response->assertSee('Tất cả sản phẩm');
    }

    public function test_search_products_by_keyword(): void
    {
        // Search for 'Polo' or existing product name
        $product = SanPham::where('TrangThai', true)->first();
        $keyword = mb_substr($product->TenSanPham, 0, 5);

        $response = $this->get('/?searchName=' . urlencode($keyword));
        $response->assertStatus(200);
        $response->assertSee($product->TenSanPham);
    }

    public function test_filter_products_by_category(): void
    {
        $response = $this->get('/?category=A');
        $response->assertStatus(200);
        $response->assertSee('Tất cả sản phẩm');
    }

    public function test_pagination_and_query_string_retention(): void
    {
        $response = $this->get('/?searchName=Áo&page=1');
        $response->assertStatus(200);
        $response->assertSee('searchName=%C3%81o');
    }

    public function test_product_detail_page_loads(): void
    {
        $product = SanPham::where('TrangThai', true)->first();
        $this->assertNotNull($product);

        $response = $this->get('/products/' . $product->MaSP);
        $response->assertStatus(200);
        $response->assertSee($product->TenSanPham);
        $response->assertSee('Kích thước có sẵn');
    }

    public function test_submit_product_review(): void
    {
        $product = SanPham::where('TrangThai', true)->first();

        $response = $this->post('/products/' . $product->MaSP . '/reviews', [
            'reviewer_name' => 'Nguyễn Test',
            'rating' => 5,
            'content' => 'Sản phẩm mặc rất vừa vặn và thoáng mát, đánh giá 5 sao!',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('review_success');

        $this->assertDatabaseHas('DANH_GIA', [
            'MaSP' => $product->MaSP,
            'TenKhachHang' => 'Nguyễn Test',
            'SoSao' => 5,
        ]);
    }
}
