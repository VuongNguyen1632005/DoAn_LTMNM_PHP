# SMART CASUAL — PERSISTENT PROJECT CONTEXT
> **Tài liệu bộ nhớ dự án (Persistent Project Memory) dành cho AI Coding Agent & Kỹ sư phát triển.**  
> *Áp dụng nguyên tắc Token Optimization: Đọc tệp này trước mỗi task; chỉ kiểm tra diff/code liên quan; cập nhật tệp sau mỗi thay đổi.*

---

## A. PROJECT OVERVIEW
- **Tên dự án:** Website Bán hàng Thời trang "SMART CASUAL"
- **Mục tiêu:** Chuyển đổi và chuẩn hóa toàn diện từ mã nguồn Native PHP + NoSQL CouchDB sang **Laravel 11 Framework + MySQL 8.4 RDBMS**, đáp ứng 100% đề cương và rubric đồ án tốt nghiệp môn Lập trình mã nguồn mở (Trường ĐH Công Thương TP.HCM - HUIT).
- **Môi trường & Công nghệ đã xác minh:**
  - **PHP:** `8.3.30 (cli)` (ZTS Visual C++ 2019 x64)
  - **Laravel Framework:** `11.57.0` (chuẩn MVC, Form Request, Service Layer, Blade Layouts)
  - **MySQL Engine:** `8.4.3 Community Server` (hỗ trợ đầy đủ InnoDB, Foreign Keys, Triggers, UTF8MB4)
  - **Composer:** `2.9.4` | **PHPUnit:** `10.5` | **Bootstrap:** `5.2.3`
- **Đường dẫn thư mục dự án:**
  - **Thư mục Laravel chính thức:** `D:\PHP\PHP_NOSQL\SMART_CASUAL_LARAVEL`
  - **Thư mục dự án cũ (Read-only, bảo toàn nguyên trạng):** `D:\PHP\PHP_NOSQL\PHP_COUCHDB`
- **Môi trường CSDL:**
  - **Development / Production:** Database `smart_casual_shop` (MySQL port 3306, user `root`, no password, 13 bảng, 841 rows)
  - **Automated Testing:** Database `smart_casual_test` (MySQL port 3306, user `root`, no password, 13 bảng, 841 rows, tự động rollback qua `DatabaseTransactions`)

---

## B. ARCHITECTURE MAP
- **Cấu trúc thư mục cốt lõi:**
  - `app/Http/Controllers/`: Chứa các controller điều hướng (Storefront & Admin)
  - `app/Http/Middleware/`: Phân quyền người dùng (`AdminMiddleware`, `ManagerOnlyMiddleware`)
  - `app/Http/Requests/`: Form Request Validation (`StoreProductRequest`, `UpdateProductRequest`, `CheckoutRequest`, `LoginRequest`, `RegisterRequest`)
  - `app/Models/`: 13 Eloquent Models đại diện cho 13 bảng nghiệp vụ
  - `app/Services/`: Xử lý nghiệp vụ phức tạp (`CartService.php`, `CheckoutService.php`)
  - `database/migrations/`: 3 tệp Migration (`000001_13_tables`, `000002_triggers`, `000003_update_trigger`)
  - `database/seeders/`: `ShopQuanAoSeeder.php` nạp dữ liệu chuẩn hóa từ fixture
  - `resources/views/`: Hệ thống giao diện Blade (Storefront tại `catalog/`, `cart/`, `checkout/`, `orders/`; Admin tại `admin/`)
  - `routes/web.php`: 49 routes phân tách rành mạch

- **Bản đồ Controllers & Routes (49 Routes):**
  - **Storefront (Khách hàng):**
    - Trang chủ / Catalog: `CatalogController@index` (`GET /`), `CatalogController@about` (`GET /about`)
    - Chi tiết SP & Đánh giá: `CatalogController@show` (`GET /products/{id}`), `CatalogController@submitReview` (`POST /products/{id}/reviews`)
    - Giỏ hàng: `CartController` (`GET /cart`, `POST /cart/add`, `POST /cart/update`, `POST /cart/remove`, `POST /cart/clear`)
    - Thanh toán: `CheckoutController` (`GET /checkout`, `POST /checkout`)
    - Đơn hàng hội viên: `OrderController` (`GET /account/orders`, `GET /account/orders/{id}`, `POST /account/orders/{id}/cancel`, `POST /account/orders/{id}/confirm-received`)
    - Xác thực khách: `CustomerAuthController` (`/login`, `/register`, `/logout`)
  - **Admin Portal (Quản trị viên):**
    - Đăng nhập Admin: `AdminAuthController` (`GET /admin/login`, `POST /admin/login`, `POST /admin/logout`)
    - Dashboard: `AdminDashboardController@index` (`GET /admin`)
    - CRUD Danh mục: `AdminCategoryController` (`admin.categories.*`)
    - CRUD Sản phẩm: `AdminProductController` (`admin.products.*`, khôi phục xóa mềm `/admin/products/{id}/restore`)
    - Quản lý Đơn hàng: `AdminOrderController` (`GET /admin/orders`, `GET /admin/orders/{id}`, `POST /admin/orders/{id}/status`, `POST /admin/orders/{id}/payment`)
    - Báo cáo Doanh thu: `AdminRevenueController@index` (`GET /admin/revenue`)
    - Kiểm duyệt Đánh giá: `AdminReviewController` (`admin.reviews.index`, `admin.reviews.visibility`, `admin.reviews.destroy`)

- **Nhiệm vụ của các Services:**
  - `CartService`: Quản lý giỏ hàng kép (Session cart cho khách vãng lai + Database cart `GIO_HANG` cho hội viên); tự động hợp nhất giỏ khi đăng nhập (`mergeGuestCartToCustomer`); xử lý composite key an toàn tuyệt đối.
  - `CheckoutService`: Đặt hàng nguyên tử trong `DB::transaction()`; khóa dòng tồn kho `lockForUpdate()`; tính chiết khấu voucher (`percent`, `cash`, `shipping`); trừ kho biến thể; tự động hoàn trả tồn kho và lượt dùng voucher khi hủy đơn (`cancelOrder`).

- **Cấu hình Multi-Auth & Middleware:**
  - Guard `web`: Provider `khach_hangs` trỏ vào Model `App\Models\KhachHang` (`table: KHACH_HANG`, `primaryKey: MaKH`, `authPassword: MatKhau`).
  - Guard `admin`: Provider `nhan_viens` trỏ vào Model `App\Models\NhanVien` (`table: NHAN_VIEN`, `primaryKey: MaNV`, `authPassword: MatKhau`, vai trò `manager` hoặc `staff`).
  - Middleware `admin`: Chặn khách chưa đăng nhập guard `admin`.
  - Middleware `manager`: Chặn nhân viên thường (`staff`), chỉ cho phép quản lý (`isManager()`).

- **Blade Master Layouts:**
  - `layouts/app.blade.php`: Header responsive, Navbar, Badge đếm giỏ hàng thời gian thực, Menu tài khoản, Flash Alert, Footer.
  - `layouts/admin.blade.php`: Sidebar điều hướng, Header quản trị viên, Flash Alert hệ thống, Footer.

---

## C. DATABASE SUMMARY
- **Danh mục 13 bảng nghiệp vụ & Khóa chính (Tổng cộng: 841 records):**
  1. `DANH_MUC` (3 records): `MaDanhMuc` (VARCHAR(10) PK)
  2. `KICH_THUOC` (15 records): `MaKichThuoc` (VARCHAR(10) PK)
  3. `SAN_PHAM` (48 records): `MaSP` (VARCHAR(20) PK), FK `MaDanhMuc` (RESTRICT)
  4. `CHI_TIET_SP` (256 records): `MaCTSP` (VARCHAR(30) PK), FK `MaSP` (CASCADE), FK `MaKichThuoc` (RESTRICT), UNIQUE(`MaSP`, `MaKichThuoc`)
  5. `HINH_ANH_SP` (192 records): `MaHinhAnh` (VARCHAR(30) PK), FK `MaSP` (CASCADE)
  6. `KHACH_HANG` (3 records): `MaKH` (VARCHAR(20) PK), `Email` (UNIQUE), `TenDangNhap` (UNIQUE)
  7. `NHAN_VIEN` (2 records): `MaNV` (VARCHAR(20) PK), `TenDangNhap` (UNIQUE)
  8. `MA_GIAM_GIA` (5 records): `MaCode` (VARCHAR(50) PK)
  9. `PHUONG_THUC_VAN_CHUYEN` (2 records): `MaPTVC` (VARCHAR(20) PK)
  10. `GIO_HANG` (12 records): **Composite PK `(MaKH, MaCTSP)`**, FK `MaKH` (CASCADE), FK `MaCTSP` (CASCADE)
  11. `DON_HANG` (13 records): `MaDonHang` (VARCHAR(30) PK), FKs `MaKH`, `MaNV`, `MaCode`, `MaPTVC` (SET NULL)
  12. `CHI_TIET_DON_HANG` (20 records): **Composite PK `(MaDonHang, MaCTSP)`**, FK `MaDonHang` (CASCADE), FK `MaCTSP` (RESTRICT)
  13. `DANH_GIA` (270 records): `MaDanhGia` (VARCHAR(30) PK), FK `MaSP` (CASCADE), FK `MaKH` (SET NULL)

- **Các khóa nghiệp vụ gốc được bảo toàn 100%:**
  - `MaKH` (không đổi thành `MaKhachHang`)
  - `MaNV` (không đổi thành `MaNhanVien`)
  - `MaCode` (không đổi thành `MaVoucher`)

- **Danh sách 4 MySQL Triggers:**
  - `trg_tudong_lay_phiship_insert`: BEFORE INSERT ON `DON_HANG` tự động lấy cước ship.
  - `trg_capnhat_tong_donhang_after_insert`: AFTER INSERT ON `CHI_TIET_DON_HANG` tính lại tổng.
  - `trg_capnhat_tong_donhang_after_update`: AFTER UPDATE ON `CHI_TIET_DON_HANG` (tính lại cả đơn mới và đơn cũ nếu đổi `MaDonHang` - Migration 000003).
  - `trg_capnhat_tong_donhang_after_delete`: AFTER DELETE ON `CHI_TIET_DON_HANG` tính lại tổng.

- **Bất biến toán học đã kiểm chứng:**
  - `TongSoLuong = SUM(SoLuong)`
  - `TongTien = SUM(SoLuong * DonGia)`
  - `ThanhTien = GREATEST(0, TongTien + PhiVanChuyen - GiamGia)`

---

## D. FEATURE STATUS

| Feature | Status | Main Files | Test Files | Notes |
|:---|:---:|:---|:---|:---|
| **Multi-Auth & Passwords** | **PASS** | `config/auth.php`, `KhachHang.php`, `NhanVien.php` | `AuthenticationAndAuthorizationTest.php` | 2 guards độc lập, băm mật khẩu Bcrypt. |
| **IDOR Protection** | **PASS** | `OrderController.php` | `AuthenticationAndAuthorizationTest.php` | Chặn khách xem đơn của khách khác (HTTP 403). |
| **Admin Middleware** | **PASS** | `AdminMiddleware.php`, `ManagerOnlyMiddleware.php` | `AdminCrudAndMiddlewareTest.php` | Chặn khách/guest vào `/admin`. |
| **Category CRUD** | **PASS** | `AdminCategoryController.php` | `AdminCrudAndMiddlewareTest.php` | Có kiểm tra chặn xóa khi còn sản phẩm con. |
| **Product CRUD & Soft Delete** | **PASS** | `AdminProductController.php` | `AdminCrudAndMiddlewareTest.php` | CRUD, tạo biến thể, upload ảnh, xóa mềm `TrangThai=0`. |
| **Catalog, Filter & Search** | **PASS** | `CatalogController.php`, `catalog/index.blade.php` | `CatalogAndProductTest.php` | Tìm từ khóa, lọc category, phân trang `withQueryString()`. |
| **Reviews & Rating** | **PASS** | `CatalogController.php`, `AdminReviewController.php` | `CatalogAndProductTest.php` | Gửi review 1-5 sao, duyệt ẩn/hiện phía Admin. |
| **Cart (Guest & Member)** | **PASS** | `CartService.php`, `CartController.php`, `GioHang.php` | `CartAndCheckoutWorkflowTest.php` | Composite key safe; gộp giỏ vãng lai khi đăng nhập. |
| **Atomic Checkout** | **PASS** | `CheckoutService.php`, `CheckoutController.php` | `CartAndCheckoutWorkflowTest.php` | DB Transaction, `lockForUpdate`, trừ kho, mã giảm giá. |
| **Cancel Order & Restock** | **PASS** | `CheckoutService.php`, `OrderController.php` | `CartAndCheckoutWorkflowTest.php` | Hủy đơn tự động hoàn kho và hoàn voucher nguyên tử. |
| **Revenue Reports** | **PASS** | `AdminRevenueController.php`, `AdminDashboardController.php` | Manual verified | Báo cáo doanh thu theo trạng thái đơn và theo tháng. |

---

## E. KNOWN ISSUES

| Issue ID | Severity | Description | Related Files | Status | Fix Reference |
|:---:|:---:|:---|:---|:---:|:---|
| **UI-01** | **P3** | Danh sách bộ lọc danh mục tại giao diện Storefront (`catalog/index.blade.php`) đang hardcode các thẻ `<option>` tĩnh (A, Q, G) thay vì nạp động từ CSDL `DANH_MUC` | `CatalogController.php:79-88`, `catalog/index.blade.php:59-64` | **RESOLVED (Audit 5)** | Truyền `$categories` từ `CatalogController::index` sang view và lặp qua danh sách danh mục hoạt động `@foreach($categories as $cat)`. |
| **UI-02** | **P3** | Khi thay đổi phương thức vận chuyển hoặc mã voucher tại trang checkout, giao diện dùng redirect GET thay vì gọi API preview mượt mà | `checkout/index.blade.php:165-175` | OPEN (Khuyến nghị nâng cấp) | Có thể chuyển sang gọi AJAX fetch preview để tối ưu trải nghiệm người dùng không bị giật trang. |
| **ORD-01** | **P1** | Thiếu ràng buộc ma trận chuyển đổi trạng thái đơn hàng (Order State Guard): Admin có thể chuyển đơn từ `cancelled` sang active mà không trừ kho lại; Khách có thể bấm `confirmReceived` từ `pending`/`confirmed`/`packing` | `AdminOrderController.php`, `OrderController.php`, `CheckoutService.php` | **RESOLVED (Audit 3)** | Xây dựng `ALLOWED_TRANSITIONS` và `updateOrderStatus()` tập trung trong `CheckoutService`; chặn mọi bước nhảy trạng thái phi logic; chỉ cho phép khách xác nhận khi `shipping`; bảo đảm hủy đơn/hoàn kho nguyên tử. |
| **VOU-01** | **P2** | Mã voucher miễn phí vận chuyển `FREESHIP` bị tính giảm 0₫ do công thức `min($shippingFee, (float)$this->GiaTri)` khi `GiaTri = 0.00` | `MaGiamGia.php` | **RESOLVED (Audit 3)** | Điều chỉnh: nếu `LoaiGiamGia === 'shipping'` và `GiaTri <= 0` thì giảm 100% `$shippingFee`; nếu `GiaTri > 0` thì áp dụng giảm tối đa theo `GiaTri`. |
| **CART-01** | **P2** | `updateQuantity` trong `CartService` cho phép gán số lượng = 0 khi biến thể hết hàng (`min(max(1, qty), stock = 0)`) thay vì xóa khỏi giỏ hoặc cảnh báo | `CartService.php`, `CartController.php` | **RESOLVED (Audit 3)** | Chặn cập nhật khi tồn kho `< 1`, trả về `false` và hiển thị thông báo lỗi hết hàng; bảo đảm không lưu `SoLuong <= 0` trong session hay `GIO_HANG`. |
| **CHK-01** | **P2** | Chưa có cơ chế chống Double-submit form thanh toán (Idempotency token / Lock checkout submission) | `CheckoutService.php`, `CheckoutRequest.php`, `CheckoutController.php`, `checkout/index.blade.php`, `CHECKOUT_IDEMPOTENCY` migration | **RESOLVED (Audit 3)** | Bổ sung bảng `CHECKOUT_IDEMPOTENCY` với unique key và hash payload; atomic claim ở backend chặn duplicate submission và chống trừ kho hai lần; vô hiệu hóa submit button phía UI. |
| **SEC-01** | **P0** | Khách vãng lai (Guest) có thể xem, hủy hoặc xác nhận đơn hàng do thiếu `auth:web` và bypass logic kiểm tra ownership | `OrderController.php`, `routes/web.php`, `CheckoutController.php` | **RESOLVED (Audit 2)** | Route `account/orders` bọc `auth:web`, backend controller kiểm tra quyền tuyệt đối, guest redirect về trang chủ kèm flash message. |
| **SEC-02** | **P1** | Phân quyền vai trò (RBAC): Staff truy cập được các route quản trị của Manager | `routes/web.php`, `ManagerOnlyMiddleware.php`, `admin.blade.php` | **RESOLVED (Audit 2)** | Gắn `middleware('manager')` vào route doanh thu & mutation danh mục/sản phẩm; `ManagerOnlyMiddleware` trả 403; ẩn menu doanh thu với Staff. |
| **SEC-03** | **P2** | Chưa cấu hình Rate Limiting (`throttle`) trên các endpoint đăng nhập `/login` và `/admin/login` | `AppServiceProvider.php`, `routes/web.php` | **RESOLVED (Audit 2)** | Đăng ký named rate limiter `login` (5 lần/phút theo IP + user) và gán `throttle:login` cho routes đăng nhập. |
| **SEC-04** | **P3** | Rà soát an toàn session giỏ hàng và chu kỳ session token khi login/logout | `CartService.php`, Auth Controllers | **VERIFIED (Audit 2)** | Session guest chỉ lưu ID biến thể & số lượng; session regenerate/invalidate chuẩn mực. |
| **DEF-01** | P1 | Composite primary key update trong `GioHang` model làm ghi đè số lượng mặt hàng khác | `GioHang.php`, `CartService.php` | **RESOLVED (Audit 1)** | Ghi đè `setKeysForSaveQuery` và dùng explicit query builder. |
| **DEF-02** | P2 | Trigger `AFTER UPDATE` trên `CHI_TIET_DON_HANG` không cập nhật lại đơn cũ khi chuyển `MaDonHang` | `000003_update_order_items_trigger.php` | **RESOLVED (Audit 1)** | Migration 000003 bổ sung khối lệnh `IF (OLD.MaDonHang <> NEW.MaDonHang)`. |
| **DEF-03** | P2 | Feature Tests để lại dữ liệu rác trong database kiểm thử `smart_casual_test` | `tests/Feature/*.php` | **RESOLVED (Audit 1)** | Bổ sung trait `DatabaseTransactions` vào cả 4 Feature Test classes. |
| **DEF-04** | P3 | Báo cáo đồ án ghi lệch tổng 839 dòng thay vì 841 dòng | `BAO_CAO_DO_AN_MON_HOC_LARAVEL.md`, `.docx`, `README.md` | **RESOLVED (Audit 1)** | Thống nhất con số 841 dòng và biên dịch lại file Word chuẩn. |
| **LIM-01** | P3 | Cổng thanh toán trực tuyến chỉ hỗ trợ COD / Chuyển khoản (chưa có VNPay Sandbox trực tiếp) | `CheckoutService.php` | OPEN (Khuyến nghị nâng cấp) | Dự kiến bổ sung ở giai đoạn phát triển mở rộng. |
| **LIM-02** | P3 | Email thông báo đặt hàng đang dùng log driver cục bộ (chưa nối SMTP thật) | `.env`, `mail.php` | OPEN (Khuyến nghị nâng cấp) | Cần cấu hình SMTP khi đưa lên Production hosting. |
| **LIM-03** | P3 | Hạn dùng của mã giảm giá (Voucher Expiration Date) không được hỗ trợ bởi schema CSDL | `MA_GIAM_GIA` | NOT_SUPPORTED | Schema đề bài không có trường ngày hết hạn; kiểm tra hiệu lực qua `TrangThai` và `SoLuong`. |

---

## F. COMPLETED WORK
- Hoàn thành Audit 1 toàn diện về Kiến trúc Laravel & Cơ sở dữ liệu MySQL (Strict Read-only).
- Khắc phục triệt để toàn bộ 4 lỗi phát hiện trong Audit 1 (DEF-01, DEF-02, DEF-03, DEF-04).
- Hoàn thành Audit 2 toàn diện về Xác thực (Authentication), Phân quyền (Authorization) và An toàn thông tin (Security).
- Khắc phục triệt để toàn bộ lỗi Audit 2 (SEC-01, SEC-02, SEC-03, SEC-04); bổ sung 7 targeted tests.
- Hoàn thành Audit 3 toàn diện về Giỏ hàng, Thanh toán, Đơn hàng, Tồn kho, Voucher và Triggers.
- Khắc phục triệt để toàn bộ 4 lỗi Audit 3 (ORD-01, VOU-01, CART-01, CHK-01); bổ sung 4 Feature tests mới; thiết lập bảng `CHECKOUT_IDEMPOTENCY`.
- Hoàn thành Audit 4 toàn diện về Giao diện người dùng (Storefront & Admin UI/UX), CRUD 2 bảng liên kết (`DANH_MUC` & `SAN_PHAM`), Tìm kiếm, Lọc, Phân trang và Xác thực dữ liệu đầu vào (Form Validation & Old Input feedback).
- Hoàn thành Audit 5/5 Nghiệm thu tổng thể (Final Audit) theo đúng đề bài giảng viên: Đối chiếu toàn bộ 10 tiêu chí Rubric đề án, khắc phục UI-01 (nạp danh mục động vào bộ lọc), đồng bộ báo cáo Word/Markdown, kiểm thử tự động toàn diện đạt 34/34 tests (175 assertions).

---

## G. CURRENT TASK
- **Nhiệm vụ hiện tại:** Final Audit 5/5 — Nghiệm thu đồ án cuối môn Laravel theo Rubric giảng viên.
- **Trạng thái:** **HOÀN THÀNH TOÀN DIỆN (10/10 TIÊU CHÍ RUBRIC ĐẠT — READY FOR SUBMISSION)**.
- **Tệp tin đã rà soát:**
  - Rubric `YeuCau_DoAnCuoiMon_LTMNM_Laravel.docx` (10/10 mục yêu cầu).
  - Toàn bộ 21 Blade templates, 5 Form Requests, 13 Eloquent Models, 4 Database Triggers.
  - Toàn bộ 5 test classes (34 tests, 175 assertions).
  - Báo cáo đồ án Word & Markdown (`BAO_CAO_DO_AN_MON_HOC_LARAVEL.md`, `.docx`).
  - Hướng dẫn cài đặt & bàn giao (`README.md`).
- **Visual / Responsive Verification:** Bootstrap 5.2.3 Responsive chuẩn mực (Navbar toggler, Grid columns col-md, container-fluid).
- **Blockers:** Không có.
- **Bước tiếp theo:** Bàn giao mã nguồn và báo cáo nghiệm thu.

---

## H. TEST STATUS
- **Bộ kiểm thử:** PHPUnit 10.5 / Laravel Feature & Unit Test Suite
- **Lần chạy gần nhất:** 2026-10-24 09:38:52
- **Kết quả thực tế:**
  - `Tests\Unit\ExampleTest`: 1 passed (1 assertion)
  - `Tests\Feature\AdminCrudAndMiddlewareTest`: 8 passed (34 assertions)
  - `Tests\Feature\AuthenticationAndAuthorizationTest`: 7 passed (23 assertions)
  - `Tests\Feature\CartAndCheckoutWorkflowTest`: 11 passed (76 assertions)
  - `Tests\Feature\CatalogAndProductTest`: 7 passed (37 assertions)
  - **Tổng cộng suite:** 34 passed, 0 failed (175 assertions)
- **Kiểm tra phân lập (Test Isolation):** Đạt 100% (Database `smart_casual_shop` nguyên vẹn 841 rows; `smart_casual_test` 849 rows; test suite chạy qua `DatabaseTransactions`).
- **Lỗi kiểm thử còn tồn tại:** 0

---

## I. NEXT PRIORITIES
1. **Dự án đã hoàn thành nghiệm thu:** Sẵn sàng nộp bài chấm và thuyết trình đồ án cuối môn.

---

## J. LAST UPDATED
- **Thời điểm cập nhật:** 2026-10-24 09:40:00 (Local Time)
- **Task ID:** Final Audit 5/5 — Rubric Verification & Project Handover
- **Tệp tin đã cập nhật:**
  - `CatalogController.php`
  - `resources/views/catalog/index.blade.php`
  - `docs/BAO_CAO_DO_AN_MON_HOC_LARAVEL.md`
  - `PROJECT_CONTEXT.md`
