# BÁO CÁO PHÂN TÍCH KỸ THUẬT CHI TIẾT

**Đối tượng:** `PHP_COUCHDB` — website bán thời trang bằng PHP và Apache, dùng CouchDB làm cơ sở dữ liệu document.  
**Cơ sở phân tích:** mã chạy trong `public/`, `src/`, giao diện `templates/`, cấu hình triển khai, seed/schema/index và scripts trong chính thư mục dự án. Tài liệu thiết kế được dùng để đối chiếu; chức năng chỉ được coi là hiện hữu khi có route và mã xử lý tương ứng. Các tham chiếu dòng dưới đây tính từ file hiện tại.

## 1. TỔNG QUAN KIẾN TRÚC & CÔNG NGHỆ SỬ DỤNG

### 1.1 Cây thư mục có chú thích

Dự án có 376 file (bao gồm ảnh, font, vendor, tài liệu và dữ liệu fixture). Cây dưới đây liệt kê đầy đủ các nhánh mã nguồn/cấu hình và các thư mục tài nguyên; các file ảnh sản phẩm được nhóm theo thư mục để cây không lặp hàng trăm tên ảnh.

```text
PHP_COUCHDB/
├── .env.example                 Mẫu biến môi trường (không chứa secret thật)
├── composer.json / composer.lock Khai báo và khóa phụ thuộc PHP
├── Dockerfile                   Dựng PHP 8.3 + Apache, cài extension, Composer
├── docker-compose.yml           Chạy app + CouchDB + volume/network/healthcheck
├── README.md                    Hướng dẫn chạy, phạm vi và vận hành
├── docker/
│   ├── apache-vhost.conf        DocumentRoot/rewrite cho front controller
│   └── php/uploads.ini          Giới hạn cấu hình upload PHP
├── public/
│   ├── index.php                Front controller, khởi tạo session/DI và đăng ký route
│   └── assets/
│       ├── css/catalog.css      CSS tùy chỉnh
│       ├── js/catalog.js        Tương tác ảnh/chọn biến thể trên catalog
│       ├── js/order-realtime.js EventSource cho cập nhật/tracking đơn
│       ├── vendors/bootstrap/   Bootstrap CSS và bundle JS cục bộ
│       ├── vendors/fontawesome/ Font Awesome CSS và webfonts
│       └── img/                 Banner, ảnh giới thiệu và ảnh sản phẩm mẫu
├── src/
│   ├── Core/                    Router.php, Response.php, StreamedResponse.php,
│   │                             View.php, Csrf.php
│   ├── Infrastructure/CouchDB/ CouchDbClient.php, CouchDbResponse.php
│   ├── Auth/                    AccountRepository/Service, AuthController,
│   │                             CustomerPasswordController, Jwt* và AuthException
│   ├── Catalog/                 CatalogController/API, ProductRepository,
│   │                             ProductAdminController/Service, ProductImageStorage,
│   │                             ProductMediaController
│   ├── Cart/                    GuestCartService, MemberCartRepository/Service,
│   │                             CartController, CustomerCommerceApiController,
│   │                             CartServiceInterface, CartInputException
│   ├── Checkout/                CheckoutController/Repository/Service,
│   │                             CheckoutException, PricingService,
│   │                             OrderMarkerCompactionService
│   ├── Orders/                  OrderController, OrderAdminController,
│   │                             OrderRealtimeController, OrderWorkflowService,
│   │                             OrderWorkflowException
│   ├── Reviews/                 ReviewController/AdminController,
│   │                             ReviewRepository/Service, ReviewException
│   └── Reporting/               ReportingController, RevenueReportService
├── templates/
│   ├── layout.php, helpers.php  Layout chung, helper escape/ảnh/session
│   ├── auth/                    login, register, password help/change
│   ├── catalog/                 danh sách, thẻ và chi tiết sản phẩm
│   ├── cart/                    giao diện giỏ
│   ├── checkout/                form checkout
│   ├── orders/                  danh sách, chi tiết, timeline giao hàng
│   ├── admin/products/          danh sách/form sản phẩm
│   ├── admin/orders/            danh sách/chi tiết đơn
│   ├── admin/revenue/           trang doanh thu
│   ├── admin/reviews/           quản trị đánh giá
│   ├── pages/                   trang tĩnh giới thiệu
│   └── errors/                  trang 404
├── database/couchdb/
│   ├── seeds/                   Fixture JSON schema v2
│   ├── indexes/                 Định nghĩa Mango index
│   ├── queries/                 Ví dụ request Mango
│   ├── design-docs/             Validator document domain
│   ├── demo_queries/            Các truy vấn phục vụ demo
│   ├── migrations/              SQL mẫu cho snapshot nguồn
│   ├── tools/                    Python transform/validate fixture SQL
│   ├── docs/                     Schema và mapping SQL → document
│   └── README.md                Quy trình seed, index, backup, restore
├── scripts/                     import_seed.php, import_sql_snapshot.php,
│                                 export_sql_server_snapshot.ps1, seed_phase06_demo.php,
│                                 database_backup.php và các smoke/verify tương ứng,
│                                 create_catalog_indexes.php, couchdb_query_diagnostics.php,
│                                 recover_checkout.php, compact_order_markers.php,
│                                 update_domain_validator.php, provision_*_password.php,
│                                 checkout_smoke_worker.php, jwt_token_smoke.php
├── docs/                         Hướng dẫn deploy, bàn giao, báo cáo từng phase,
│   ├── final_report/             Bản thảo PHP/NoSQL, source map, checklist, demo,
│   │                             QA defense, handoff và figures
│   ├── PHASE_01..08_*.md         Báo cáo/đề xuất các giai đoạn phát triển
│   ├── PROJECT_*.md              Bàn giao và hoàn thành dự án
│   ├── *BACKUP* / *RESTORE*      Hướng dẫn/xác minh sao lưu phục hồi
│   ├── RENDER_*.md               Hướng dẫn và checklist triển khai Render
│   └── openapi.yaml               Đặc tả các HTTP API
├── var/                          Bằng chứng fixture; runtime uploads/sessions dùng volume
└── vendor/                       Thư viện Composer đã cài (không phải mã tự viết)
```

Các file xử lý trọng yếu: `public/index.php`; `src/Infrastructure/CouchDB/CouchDbClient.php`; `src/Auth/{AuthController,AccountService,AccountRepository}.php`; `src/Catalog/{ProductRepository,CatalogController,ProductAdminService,ProductImageStorage}.php`; `src/Cart/{GuestCartService,MemberCartService}.php`; `src/Checkout/{CheckoutController,CheckoutService,CheckoutRepository,PricingService}.php`; `src/Orders/OrderWorkflowService.php`.

### 1.2 Mô hình tổ chức

Đây không phải MVC framework đầy đủ như Laravel. Dự án dùng cấu trúc tự định nghĩa, hướng module: `public/index.php` là điểm vào/router; controller điều phối request; service giữ luật nghiệp vụ; repository truy cập document; template PHP render HTML. Ví dụ route catalog được nối tới `CatalogController`, route quản trị sản phẩm tới `ProductAdminController`, còn các service/repository tương ứng được khởi tạo tại `public/index.php` (khoảng dòng 55–128 và 162–202). `src/Core/Router.php`, `View.php`, `Response.php` cung cấp hạ tầng tối giản.

Một số trang dùng render phía server; các API JSON độc lập phục vụ catalog/cart/checkout/order. Session PHP dùng cho website; JWT Bearer là cơ chế riêng cho API.

### 1.3 Công nghệ và cách chạy

- **PHP:** `composer.json` yêu cầu `^8.3`; Dockerfile dùng `php:8.3-apache`, cài `curl`, `mbstring`, bật Apache `rewrite` và `headers`. JSON là extension tích hợp (`composer.json`, `Dockerfile`).
- **Thư viện ngoài:** chỉ có `firebase/php-jwt` `^7.2.1` trong `require` để ký/xác minh JWT. Không khai báo Laravel/Symfony, Guzzle, ORM hay SDK CouchDB. PSR-4 ánh xạ `App\` → `src/`.
- **Cơ sở dữ liệu:** CouchDB image `3.5.0` (`docker-compose.yml`). Các named volume riêng lưu dữ liệu CouchDB, vendor, uploads và sessions. App chỉ expose loopback host mặc định ở port 8081; CouchDB loopback ở 5984.
- **Frontend:** PHP templates xuất HTML, Bootstrap CSS/JS và Font Awesome được vendored; CSS/JS tùy chỉnh trong `public/assets`. JavaScript là vanilla JS (`catalog.js`, `order-realtime.js`); không thấy jQuery/Tailwind. `order-realtime.js` dùng `EventSource`/SSE; thao tác catalog cơ bản không phụ thuộc Ajax. JSON API có sẵn để client gọi.
- **Cấu hình:** sao chép `.env.example` thành `.env`, đặt `COUCHDB_USER`, `COUCHDB_PASSWORD`, `JWT_SECRET` tối thiểu 32 byte; biến mặc định database là `retail_order_delivery_test`. Chạy trong `PHP_COUCHDB`: `docker compose up --build -d`, sau đó `docker compose exec -T app composer seed:import` và `docker compose exec -T app composer catalog:indexes`; truy cập `http://localhost:8081`. Lệnh seed có điều kiện an toàn và chỉ chấp nhận tên database `_test` (README.md, `scripts/import_seed.php`). `/health` kiểm tra tiến trình, `/health/ready` kiểm tra database.

## 2. CƠ CHẾ KẾT NỐI & TƯƠNG TÁC VỚI COUCHDB

### 2.1 Client và cấu hình kết nối

`src/Infrastructure/CouchDB/CouchDbClient.php:11–59` là HTTP client dùng **PHP cURL thuần**. `request($method, $path, $payload)` nối `COUCHDB_URL` và path, nhận mọi HTTP method tùy caller; khi có payload thì JSON encode, đặt `Content-Type: application/json`. Client bật Basic Auth qua `CURLOPT_HTTPAUTH => CURLAUTH_BASIC`, `CURLOPT_USERPWD`; TLS peer/host verification bật, không theo redirect, timeout kết nối 5 giây/request 30 giây. `CouchDbResponse.php:10–33` giữ status/body và giải mã JSON. Không dùng Guzzle hay CouchDB SDK.

`public/index.php:60–65` đọc URL/user/password/database từ env; compose cấp `COUCHDB_URL=http://couchdb:5984`, user/password, database fallback `retail_order_delivery_test`. Endpoint document có dạng:

```text
{COUCHDB_URL}/{urlencode(COUCHDB_DATABASE)}/{urlencode(document_id)}
{COUCHDB_URL}/{urlencode(COUCHDB_DATABASE)}/_find
```

Tài khoản ứng dụng **không** đăng nhập bằng CouchDB auth. Basic Auth chỉ là credential backend để PHP gọi CouchDB. Browser dùng PHP session; REST API bảo vệ bằng JWT.

### 2.2 CRUD, `_id`, `_rev`

Client trung tâm có một phương thức `request`, không định nghĩa bốn wrapper CRUD tách biệt. Repository gọi `GET` để đọc, `PUT` vào `/{db}/{_id}` để tạo/cập nhật, `POST /{db}/_find` để query; `DELETE` dùng ở script quản trị xóa index/database/document theo trường hợp. Ví dụ `ProductAdminService.php:42–54, 174–184`, `AccountRepository.php:36–90`, `CheckoutRepository.php:19–49`.

`_id` được mã hóa trong path và thường ổn định theo loại: `product:A01`, `customer:KH001`, `cart:KH001`, `order:<id>`, `review:<id>`. Khi tạo một document với ID có sẵn, CouchDB trả conflict nếu ID đã tồn tại. Khi sửa, code đọc document hiện tại, giữ `_id` và `_rev`, rồi PUT document đó; CouchDB yêu cầu revision hiện hành, trả 409 khi có xung đột. `MemberCartService.php` thử lại xung đột tối đa 4 lần; checkout và workflow cũng đọc mới rồi thử cập nhật theo `_rev`. Response `PUT` trả `rev`, code cập nhật `_rev` trong đối tượng trả về.

### 2.3 Cơ chế truy vấn

Mã ứng dụng truy vấn danh sách/chỉ mục bằng **Mango Query `/{db}/_find`**, có `use_index`; không thấy dùng MapReduce view để phục vụ catalog/đơn. Ví dụ:

- sản phẩm active: `src/Catalog/ProductRepository.php:17–20, 45–55` dùng index `active_products`;
- username: `src/Auth/AccountRepository.php:17–22` dùng `account_usernames`;
- danh sách order: `src/Checkout/CheckoutRepository.php:78–101, 176–195` dùng `customer_orders`/`admin_orders` và bookmark;
- review và tracking code: `src/Reviews/ReviewRepository.php`, `src/Checkout/CheckoutRepository.php:27–42`.

Index được khai báo ở `database/couchdb/indexes/catalog_indexes.json`, tạo/cập nhật bằng `scripts/create_catalog_indexes.php`. Các JSON trong `database/couchdb/queries/` là ví dụ Mango. `/_all_docs` xuất hiện ở script seed/demo/backup để duyệt document, không phải đường query chính cho request người dùng. `database/couchdb/design-docs/domain_validation.json` là design document chứa `validate_doc_update`, **không phải** MapReduce view.

## 3. THIẾT KẾ CƠ SỞ DỮ LIỆU NOSQL (JSON DOCUMENT SCHEMAS)

Quy ước chung: dữ liệu nghiệp vụ có `type`, `schema_version: 2`, `_id`, thường có `legacy_id` và `meta`. `_id` và `_rev` thuộc CouchDB; `_rev` được CouchDB cấp/cập nhật, không phải field nghiệp vụ. Seed mẫu tại `database/couchdb/seeds/retail_order_delivery.json`; quy tắc mapping tại `database/couchdb/docs/schema-v2-and-mapping.md`; validator tại `database/couchdb/design-docs/domain_validation.json`.

### 3.1 Sản phẩm, danh mục, biến thể và ảnh

Document sản phẩm thực tế (`product:A01`) có dạng:

```json
{
  "_id": "product:A01", "type": "product", "schema_version": 2,
  "legacy_id": "A01", "name": "Áo NY Thêu Ngực", "description": "...",
  "category": {"code": "A", "name": "Áo"},
  "variants": [
    {"variant_id": "CTA01SzS", "size": "S", "price": 150000, "stock": 50, "active": true},
    {"variant_id": "CTA01SzM", "size": "M", "price": 150000, "stock": 25, "active": true}
  ],
  "images": [
    {"legacy_id": "A01H01", "path": "assets/img/.../product-1-1.png", "is_primary": true, "active": true}
  ],
  "active": true, "meta": {"source": "...", "source_tables": ["SAN_PHAM", "DANH_MUC", "CHI_TIET_SP", "KICH_THUOC", "HINH_ANH_SP"]}
}
```

Mỗi biến thể giữ size, giá, stock và active; tiền VND trong fixture là JSON number nguyên. **Không có thuộc tính `color`/màu trong schema và không thấy mã xử lý màu.** Ảnh là các object metadata/path, file ảnh nằm ngoài CouchDB. Sản phẩm có `category` nhúng `{code,name}`; không có document category riêng hay màn hình CRUD danh mục. Form admin hiện chọn cứng `A=Áo`, `Q=Quần`, `G=Giày` (`ProductAdminService.php:14`, `templates/admin/products/form.php:8`). Không tìm thấy document type/field quản lý thương hiệu riêng; chữ “brand” nếu có trong mô tả chỉ là nội dung văn bản.

### 3.2 Category/Brand

Danh mục nhúng vào product. Phần code hiện tại chỉ nhận ba code nêu trên; danh sách form hard-code, không CRUD reference data. Brand không có schema/domain object riêng. Vì vậy không thể báo cáo tính năng quản lý thương hiệu hay biến thể màu như tính năng đã làm.

### 3.3 Users / Admins

`customer` gồm `_id`, `type`, `schema_version`, `legacy_id`, `auth.username`, `auth.password_hash`, `auth.requires_password_reset`, `profile.name/phone/email/addresses[]`, `active`, `meta`. Tài khoản khách trong fixture nhập từ SQL có hash null và yêu cầu đặt lại mật khẩu. Đăng ký tạo customer ID xác định từ SHA-256 email, password lưu bằng `password_hash` (`AccountService.php:14–54`).

`staff` có cấu trúc tương tự, thêm `role` (chỉ `manager` hoặc `staff`). Seed demo tạo staff manager/staff; tài khoản legacy chưa provision hash không đăng nhập được. Không có document type `admin` riêng; admin là staff có role.

### 3.4 Cart

`cart:<customer_id>` lưu `customer_id`, `items[]`, `updated_at`, `meta`. Item đại diện variant, có `product_id`, `variant_id`, `size`, `quantity`, `unit_price_snapshot`; runtime có thêm `selected` để chọn mua. Giỏ guest lưu trong `$_SESSION['guest_cart']`, không phải document CouchDB. Khi đăng nhập, `MemberCartService` gộp dòng khách theo variant vào document cart và điều chỉnh theo tồn kho (`AuthController.php` login; `MemberCartService.php`).

### 3.5 Order

Order mẫu có: `legacy_id`; snapshot `customer` hoặc thông tin guest; `guest_order`; `receiver{name,phone,email,address}`; `items[]` gồm `product_id`, `product_name`, `variant_id`, `size`, `quantity`, `unit_price`, `line_total`, `currency`; `shipping{method_code,method_name,fee,currency}`; `discount{code,type,value,amount,currency}`; `payment{method,legacy_label,status,paid}`; `totals{total_quantity,subtotal,shipping_fee,discount_amount,grand_total,currency}`; `status`; `status_history[]`; `ordered_at`; `note`; `active`; `meta`.

Document order runtime còn có thể có `delivery_tracking{tracking_code,status,estimated_delivery_date,delivered_at,history[]}`; `payment_history[]`; `assigned_staff_id`; và `meta.checkout{operation_id,request_fingerprint,phase,stock_plan,voucher_code,...}` phục vụ idempotency/recovery. `schema-v2-and-mapping.md` ghi các field mở rộng này. Các trạng thái giao hàng tách với trạng thái order. Validator kiểm tra contract cục bộ của document, không tạo transaction nguyên tử giữa order/product/voucher.

### 3.6 Các document khác

- `voucher:<CODE>`: `code`, `value`, `discount_type` (`percent`, `cash`, `shipping`), `remaining_quantity`, `active`, `meta`; runtime có `meta.checkout_redemptions` marker.
- `shipping_method:<code>`: `code`, `name`, `fee`, `currency`, `active`, `meta`.
- `review:<id>`: `legacy_id`, `product_id`, `customer_id` (có thể null), `reviewer_name`, `rating` integer 1–5, `content`, `active`, tùy chọn `admin_response`, `meta`. Phản hồi của admin có thông tin nhân viên/time; review lưu thành document riêng.
- `_design/domain_validation`: design document dùng CouchDB validator. Không thấy document promotion/news riêng khác voucher; không có module tin tức.

Seed fixture hiện có 48 product, 3 cart, 13 order, 270 review, 3 customer, 2 staff, 5 voucher, 2 shipping_method (đếm từ JSON fixture). Đây là fixture/mẫu, không chứng minh số bản ghi production.

## 4. DANH SÁCH CHỨC NĂNG & PHÂN QUYỀN (USE CASES THỰC TẾ)

Route được đăng ký ở `public/index.php:130–202`; các đường xử lý lỗi khi CouchDB không sẵn sàng được đăng ký tiếp ở khoảng dòng 204–297.

### 4.1 Khách hàng / Client

| Chức năng | Route | File xử lý chính |
|---|---|---|
| Trang chủ, danh sách, search/filter/sort/pagination | `GET /` | `CatalogController.php`, `ProductRepository.php`; view `templates/catalog/index.php` |
| Chi tiết sản phẩm/biến thể/đánh giá | `GET /products/{id}` | `CatalogController.php`; view `templates/catalog/detail.php` |
| Ảnh sản phẩm media | `GET /media/{file}` | `ProductMediaController.php` |
| Đăng ký, đăng nhập, đăng xuất | `/register`, `/login`, `POST /logout` | `AuthController.php`, `AccountService.php`, `AccountRepository.php`; templates `auth/` |
| Đổi mật khẩu/tài khoản password help | `/account/password`, `/password-help` | `CustomerPasswordController.php`; không có chỉnh sửa hồ sơ/địa chỉ đầy đủ |
| Giỏ guest/member: thêm, cập nhật, đổi size, chọn, xóa | `/cart`, `/cart/items/...` | `CartController.php`, `GuestCartService.php`, `MemberCartService.php`; `templates/cart/index.php` |
| Checkout/preview form | `GET/POST /checkout` | `CheckoutController.php`, `CheckoutService.php`, `PricingService.php`; `templates/checkout/index.php` |
| Lịch sử/chi tiết đơn khách đã đăng nhập | `/account/orders`, `/account/orders/{id}` | `OrderController.php`; `CheckoutRepository.php`; templates `orders/` |
| Lịch sử/chi tiết đơn guest thuộc session | `/orders`, `/orders/{id}` | `OrderController.php` |
| Khách xác nhận đã nhận hàng; theo dõi sự kiện | POST confirm-received và GET events | `OrderController.php`, `OrderRealtimeController.php`, `OrderWorkflowService.php`; JS `order-realtime.js` |
| API catalog, review, cart, checkout, orders | `/api/v1/...` | `CatalogApiController.php`, `CustomerCommerceApiController.php`, `JwtApiController.php`; yêu cầu JWT ở endpoint được bảo vệ |
| Gửi review | `POST /products/{id}/reviews` hoặc API review | `ReviewController.php`, `ReviewService.php` |

Không thấy code reset mật khẩu qua email: `password-help` là trang trợ giúp/đường dẫn chỉ dẫn. Không thấy màn hình cho khách sửa các trường hồ sơ và quản lý nhiều địa chỉ; hồ sơ được dùng chủ yếu trong auth/checkout.

### 4.2 Quản trị

| Chức năng | Route | File xử lý chính |
|---|---|---|
| Bảng điều khiển mặc định chuyển vào quản lý sản phẩm | `/admin` | `ProductAdminController.php` |
| Xem sản phẩm, tạo/sửa, ẩn mềm/khôi phục | `/admin/products...` | `ProductAdminController.php`, `ProductAdminService.php`, `ProductImageStorage.php`; templates `admin/products/` |
| Quản lý đơn: danh sách, chi tiết, đổi trạng thái, delivery | `/admin/orders...` | `OrderAdminController.php`, `OrderWorkflowService.php`; templates `admin/orders/` |
| Ghi nhận COD, xác minh thủ công bank/card | payment routes dưới `/admin/orders/{id}` | `OrderAdminController.php`, `OrderWorkflowService.php` |
| Doanh thu | `/admin/revenue`, `/admin/revenue/stats` | `ReportingController.php`, `RevenueReportService.php`; template `admin/revenue/index.php` |
| Xem review, trả lời; ẩn/khôi phục review | `/admin/reviews...` | `ReviewAdminController.php`, `ReviewRepository.php`; template `admin/reviews/index.php` |
| JSON API quản trị qua JWT | `/api/admin/...` | `JwtApiController.php`, `JwtBearerAuthenticator.php` |

Guard controller kiểm tra session `type=staff` và role `staff|manager`; `managerGuard` chỉ cho manager sửa/xóa/khôi phục sản phẩm và ẩn/khôi phục review. Staff/manager quản lý order/revenue/reply review. Không có module CRUD quản lý tài khoản người dùng, danh mục hoặc thương hiệu trong route. API endpoint có guard role tương ứng.

## 5. LUỒNG NGHIỆP VỤ CỐT LÕI (CORE WORKFLOWS)

### 5.1 Đăng ký, đăng nhập, session và quyền

1. `POST /register` được định tuyến vào `AuthController::register`; controller kiểm tra CSRF rồi gọi `AccountService::register` (`public/index.php:156–158`, `AuthController.php:107–130`).
2. Service chuẩn hóa email lowercase, xác thực email/tên, đối chiếu password, yêu cầu mật khẩu 8–128 ký tự có chữ hoa/thường/số/ký tự đặc biệt; password được hash `PASSWORD_DEFAULT`. ID customer là `customer:REG:<sha256(email)>`; lưu document qua `AccountRepository::create` (`AccountService.php:14–54`). Thành công chuyển tới login.
3. `POST /login`: lookup cả `customer` và `staff` theo `auth.username` bằng Mango index; cần đúng một kết quả, tài khoản active và password hash hợp lệ; xác minh bằng `password_verify`, nâng cấp hash khi cần (`AccountService.php:59–91`, `AccountRepository.php:17–34`).
4. AuthController regenerate session ID, xóa token CSRF/checkout cũ, lưu `$_SESSION['auth_user']` gồm type, document/legacy ID, profile, role, cờ reset password (`AuthController.php:48–104`). Khách hợp lệ được merge guest cart vào cart document. Session cookie được cấu hình tại `public/index.php:46–53`; POST logout có CSRF, xóa auth và liên kết guest order, regenerate ID.
5. Quyền trang được kiểm tra tại controller (ví dụ `ProductAdminController::staffGuard/managerGuard`, `OrderAdminController::staffGuard`). Khách chỉ đọc đơn theo customer ID hoặc guest order IDs trong session; order detail không thuộc owner trả 404. API dùng JWT HS256 từ `firebase/php-jwt`, issuer/audience/expiry được kiểm tra; mỗi request tải lại account từ CouchDB để kiểm tra active/role (`JwtBearerAuthenticator.php`, `JwtTokenService.php`). Token mặc định hết hạn sau 900 giây, không có refresh token.

### 5.2 Từ giỏ hàng đến checkout, tạo order và cập nhật tồn kho

1. Khách thêm dòng vào giỏ bằng `CartController`; service lấy thông tin product/variant hiện tại, kiểm tra active và quantity không vượt stock. Guest cart nằm trong session; member cart ghi vào `cart:<customerId>` và xử lý 409 bằng cách đọc/sửa lại.
2. Checkout controller lấy danh sách dòng selected/available. `CheckoutService::preview` tính giá theo product hiện tại, xác thực phương thức ship active và voucher còn lượt. `PricingService` tính subtotal, shipping fee, discount (percent/cash/free shipping), grand total; client không được coi giá snapshot từ cart là authoritative.
3. Submit yêu cầu CSRF và token idempotency session; API yêu cầu `Idempotency-Key` 32 ký tự hex. Service chỉ cho `cod` khi tạo đơn mới (`CheckoutService.php:62–83`; API ở `CustomerCommerceApiController.php:163–176`). Tạo order snapshot receiver/items/shipping/discount/payment/totals/status pending; `payment` ban đầu `unpaid`; order đồng thời được lưu thành journal có `operation_id`, `request_fingerprint`, `phase=stock_reserving`, `stock_plan` (`CheckoutService.php:100–165`).
4. Với cùng scope + token, thao tác lặp lấy lại order journal; fingerprint khác bị từ chối. Service chuyển phase qua `stock_reserving` → `voucher_reserving` → `finalizing` → `completed` (`CheckoutService.php:227–255`).
5. `reserveStock` đọc product mới nhất, xác minh variant/stock, trừ tồn trên product document và ghi marker theo operation. PUT giữ `_rev`; conflict được retry có giới hạn. Voucher cũng giảm `remaining_quantity` và ghi redemption marker. Đây là nhiều lần PUT riêng giữa order/product/voucher, không phải một transaction CouchDB đa-document.
6. Khi thất bại giữa chừng, order chuyển `compensating`; marker làm cho việc cộng trả stock/voucher chỉ diễn ra một lần, sau đó phase thành failed. `recover_checkout.php` hỗ trợ phục hồi journal member; guest cần phiên trình duyệt gốc. Khi hoàn tất service xóa dòng đã mua khỏi cart rồi đánh dấu `completed`.

### 5.3 Thêm/sửa sản phẩm và upload ảnh

1. GET form và POST save ở `/admin/products/new` hoặc `/{id}/edit`; controller yêu cầu manager và CSRF (`ProductAdminController.php:31–68`). Form có category select cố định và `variants_json` (mỗi dòng `variant_id,size,price,stock,active`), enctype multipart (`templates/admin/products/form.php:6–11`).
2. `ProductAdminService::save` kiểm tra tên/mô tả, category code, JSON variants, size không trùng, giá không âm, stock số nguyên không âm, active boolean. Khi sửa, giữ ID variant cũ, không cho xóa/đổi size biến thể để tránh phá tham chiếu từ cart/order (`ProductAdminService.php:56–110`).
3. `ProductImageStorage::store` kiểm tra upload PHP, kích thước tối đa 5 MB, MIME JPEG/PNG/WebP qua `finfo`, nội dung/dimensions/pixel cap; tạo tên ngẫu nhiên và chuyển file ra `var/uploads` (được compose mount riêng), trả path `media/<filename>` (`ProductImageStorage.php:25–64`). Route `/media/{file}` đọc ảnh qua controller, whitelist filename và xác thực path.
4. Service giữ ảnh cũ, nếu có ảnh mới thì thêm metadata ảnh mới làm primary và gỡ cờ primary ảnh trước; tạo/cập nhật product bằng PUT CouchDB. Nếu PUT lỗi, xóa file mới vừa upload. Edit dùng `_rev` đọc từ document; conflict 409 hiện thông báo tải lại. Soft delete đặt `active=false`, không xóa vật lý; restore đặt true (`ProductAdminService.php:113–184`).

## 6. ĐÁNH GIÁ HIỆN TRẠNG MÃ NGUỒN (ƯU ĐIỂM, HẠN CHẾ & HƯỚNG PHÁT TRIỂN)

### Ưu điểm

- Có phân lớp tương đối rõ: router/core, controller, service, repository và template; module chia theo domain thay vì dồn toàn bộ logic vào một file.
- Client CouchDB tập trung, dùng JSON, Basic Auth backend, TLS verify và timeout; code xử lý HTTP status, JSON lỗi và revision conflict.
- Dữ liệu v2 có `type`, `schema_version`, `legacy_id`, `meta`; product/order chứa snapshot phục vụ lịch sử; review tách document để truy vấn riêng.
- Mango indexes được định nghĩa riêng, query có `use_index`; một số danh sách đơn dùng bookmark phân trang.
- Authentication dùng password hash, CSRF cho thao tác form, session ID regeneration; quản trị phân role; API dùng JWT và xác minh tài khoản live.
- Checkout có idempotency journal, stock/voucher markers, kiểm tra `_rev`, retry và compensation/recovery; xử lý trường hợp nhiều document không có transaction chung một cách tường minh.
- Upload ảnh giới hạn định dạng/kích thước, đổi tên ngẫu nhiên, lưu ngoài web root; ảnh tách khỏi database và có volume riêng.
- Có validator CouchDB và tài liệu/công cụ seed, backup/restore, diagnostics, deploy; những thứ này giúp vận hành và bàn giao có quy trình.

### Hạn chế hiện thấy trong code

- Mô hình category/brand/variant chưa đầy đủ với thời trang: category hard-code ba lựa chọn, không CRUD danh mục; không có màu sắc hay brand field/module. Giá/tồn kho gắn variant size nhưng không hỗ trợ tổ hợp size–màu.
- Một số query catalog/admin lấy tối đa 2.000 document rồi lọc, sort, paginate trong PHP (`ProductRepository.php:45–55`, `CatalogController.php`, `CatalogApiController.php`). Khi dữ liệu tăng vượt limit, danh sách/tổng số có thể thiếu; tìm kiếm tên, lọc giá/category và sort không thực hiện hoàn toàn trong Mango.
- Fixture legacy có account `password_hash=null`; cần provision trước khi đăng nhập. `/password-help` không thể hiện luồng gửi email/reset token; không có MFA hoặc quy trình khôi phục tự động.
- Không có thanh toán online thật: checkout mới cho COD; chuyển khoản/thẻ chỉ được staff xác minh thủ công, không tích hợp cổng/ngân hàng; không thấy quy trình refund tự động.
- Chưa có CRUD user, category, brand, khuyến mãi linh hoạt ngoài voucher, hoặc module tin tức. Account khách mới có flow đổi mật khẩu, không có quản lý profile/address đầy đủ.
- Cart, order, product, voucher là nhiều document; CouchDB revision bảo vệ từng document nhưng không cung cấp giao dịch đa-document. Marker/compensation giảm rủi ro nhưng crash/conflict vẫn đòi hỏi vận hành recovery và compaction; marker tăng kích thước document cho tới khi dọn.
- Tài liệu README nêu backup ảnh riêng; ảnh lưu volume `php_uploads` nên backup database không đủ khôi phục toàn site.
- Không có framework ORM/migration framework; validator chỉ xác minh quy tắc cục bộ mỗi document, liên kết product/customer/variant và các phép tính liên-document vẫn cần service/import validation.
- Báo cáo doanh thu hiện là tổng `grand_total` của đơn delivered theo service; chưa phải hệ thống kế toán có đối soát, refund hay báo cáo chi phí/lợi nhuận.
- Quản lý sản phẩm hiện cho nhập biến thể dưới dạng JSON trong textarea; đây là giao diện quản trị kỹ thuật, dễ nhập sai hơn form dòng biến thể chuyên biệt.

### Hướng phát triển phù hợp với nền hiện tại

1. Bổ sung document/reference collection category và brand, cập nhật validator/index/form; mở rộng variant thành tổ hợp size/color với mã variant ổn định và chính sách migration.
2. Đưa lọc, sort và phân trang catalog vào Mango index/query hoặc thiết kế view phù hợp; tránh đọc toàn bộ danh mục vào bộ nhớ PHP khi tăng dữ liệu.
3. Hoàn thiện quy trình tài khoản: email xác minh, reset token có hạn, quản lý profile/địa chỉ, audit bảo mật và chính sách khóa/đổi mật khẩu.
4. Tích hợp cổng thanh toán theo webhook có xác minh chữ ký, idempotency và đối soát; bổ sung refund/hoàn tiền và giữ tách payment status với order status.
5. Thêm công cụ admin cho category/brand/user, form biến thể dễ dùng và kiểm tra ảnh/tồn kho; lưu audit actor/time cho các thao tác quan trọng.
6. Chuẩn hóa quy trình backup/restore cả CouchDB lẫn upload/session cần thiết; diễn tập phục hồi và kiểm soát dung lượng marker/journal.
7. Bổ sung quan sát vận hành (log có correlation ID, metrics, cảnh báo lỗi CouchDB/checkout), chính sách phân trang nhất quán và tối ưu cho khối lượng lớn.

Các hướng trên là đề xuất phát triển từ giới hạn quan sát được, không phải tính năng đã có sẵn.

---

**Kết luận:** `PHP_COUCHDB` là nền tảng website PHP module hóa, server-rendered, kết nối CouchDB qua cURL và sử dụng Mango Query. Các phần bán hàng chính gồm catalog, account/session + JWT API, cart guest/member, checkout COD có recovery, quản trị sản phẩm/đơn/review và báo cáo doanh thu. Schema hiện lưu category lồng trong product, variant theo size; không có màu sắc hay thương hiệu độc lập. Những giới hạn được nêu ở trên bám theo route, service và fixture hiện hành.

# PHỤ LỤC — TRÍCH XUẤT CHI TIẾT TỪ MÃ NGUỒN

Phụ lục này bổ sung các giá trị và đoạn code cụ thể để đối chiếu với sáu phần phân tích ở trên. Nguồn chính là `public/index.php`, `database/couchdb/indexes/catalog_indexes.json`, `scripts/create_catalog_indexes.php`, `src/Orders/OrderRealtimeController.php`, `src/Core/StreamedResponse.php`, `src/Orders/OrderWorkflowService.php`, `src/Catalog/ProductImageStorage.php`, `src/Checkout/PricingService.php`, `src/Infrastructure/CouchDB/CouchDbClient.php`, `src/Catalog/ProductRepository.php` và `src/Checkout/CheckoutService.php`.

## A. Danh sách route đầy đủ

Controller trong bảng là các object được khởi tạo tại `public/index.php:91–129`; route nghiệp vụ được đăng ký tại `:130–202`. `{id}` và `{file}` là tham số động của router.

| Method | URL pattern | Controller::method |
|---|---|---|
| GET | `/login` | `AuthController::loginForm` |
| POST | `/login` | `AuthController::login` |
| POST | `/api/auth/token` | `JwtApiController::issueToken` |
| GET | `/api/v1/me` | `JwtApiController::me` |
| GET | `/api/admin/revenue/stats` | `JwtApiController::revenueStats` |
| GET | `/api/admin/orders` | `JwtApiController::staffOrders` |
| GET | `/api/admin/orders/{id}` | `JwtApiController::staffOrderDetail` |
| POST | `/api/admin/orders/{id}/status` | `JwtApiController::staffUpdateOrderStatus` |
| POST | `/api/admin/orders/{id}/delivery` | `JwtApiController::staffUpdateDeliveryStatus` |
| POST | `/api/admin/orders/{id}/payment/cod-collected` | `JwtApiController::staffRecordCodPayment` |
| POST | `/api/admin/orders/{id}/payment/verify` | `JwtApiController::staffVerifyManualPayment` |
| GET | `/api/v1/orders` | `JwtApiController::customerOrders` |
| GET | `/api/v1/orders/{id}` | `JwtApiController::customerOrderDetail` |
| POST | `/api/v1/orders/{id}/confirm-received` | `JwtApiController::customerConfirmReceived` |
| GET | `/api/v1/cart` | `CustomerCommerceApiController::cart` |
| POST | `/api/v1/cart/items` | `CustomerCommerceApiController::add` |
| POST | `/api/v1/cart/items/update` | `CustomerCommerceApiController::update` |
| POST | `/api/v1/cart/items/select` | `CustomerCommerceApiController::select` |
| POST | `/api/v1/cart/items/select-all` | `CustomerCommerceApiController::selectAll` |
| POST | `/api/v1/cart/items/change-size` | `CustomerCommerceApiController::changeVariant` |
| POST | `/api/v1/cart/items/remove` | `CustomerCommerceApiController::remove` |
| POST | `/api/v1/checkout/preview` | `CustomerCommerceApiController::checkoutPreview` |
| POST | `/api/v1/checkout` | `CustomerCommerceApiController::checkout` |
| GET | `/api/v1/products` | `CatalogApiController::index` |
| GET | `/api/v1/products/{id}` | `CatalogApiController::detail` |
| POST | `/api/v1/products/{id}/reviews` | `JwtApiController::submitCustomerReview` |
| GET | `/register` | `AuthController::registerForm` |
| POST | `/register` | `AuthController::register` |
| POST | `/logout` | `AuthController::logout` |
| GET | `/account/password` | `CustomerPasswordController::form` |
| POST | `/account/password` | `CustomerPasswordController::change` |
| GET | `/password-help` | `CustomerPasswordController::help` |
| GET | `/` | `CatalogController::index` |
| GET | `/products/{id}` | `CatalogController::detail` |
| GET | `/media/{file}` | `ProductMediaController::show` |
| POST | `/products/{id}/reviews` | `ReviewController::submit` |
| GET | `/admin/reviews` | `ReviewAdminController::index` |
| POST | `/admin/reviews/{id}/visibility` | `ReviewAdminController::setVisibility` |
| POST | `/admin/reviews/{id}/reply` | `ReviewAdminController::reply` |
| GET | `/cart` | `CartController::index` |
| POST | `/cart/items/add` | `CartController::add` |
| POST | `/cart/items/update` | `CartController::update` |
| POST | `/cart/items/change-size` | `CartController::changeVariant` |
| POST | `/cart/items/select` | `CartController::select` |
| POST | `/cart/items/select-all` | `CartController::selectAll` |
| POST | `/cart/items/remove` | `CartController::remove` |
| GET | `/checkout` | `CheckoutController::index` |
| POST | `/checkout` | `CheckoutController::submit` |
| GET | `/account/orders` | `OrderController::index` |
| GET | `/account/orders/{id}/events` | `OrderRealtimeController::customerEvents` |
| GET | `/account/orders/{id}` | `OrderController::detail` |
| POST | `/account/orders/{id}/confirm-received` | `OrderController::confirmReceived` |
| GET | `/orders` | `OrderController::guestIndex` |
| GET | `/orders/{id}/events` | `OrderRealtimeController::guestEvents` |
| GET | `/orders/{id}` | `OrderController::guestDetail` |
| POST | `/orders/{id}/confirm-received` | `OrderController::confirmGuestReceived` |
| GET | `/admin/orders` | `OrderAdminController::index` |
| GET | `/admin/orders/{id}` | `OrderAdminController::detail` |
| POST | `/admin/orders/{id}/status` | `OrderAdminController::updateStatus` |
| POST | `/admin/orders/{id}/delivery` | `OrderAdminController::updateDeliveryStatus` |
| POST | `/admin/orders/{id}/payment/collect` | `OrderAdminController::recordCodCollected` |
| POST | `/admin/orders/{id}/payment/verify` | `OrderAdminController::verifyManualPayment` |
| GET | `/admin/revenue` | `ReportingController::revenue` |
| GET | `/admin/revenue/stats` | `ReportingController::stats` |
| GET | `/admin` | `ProductAdminController::index` |
| GET | `/admin/products` | `ProductAdminController::index` |
| GET | `/admin/products/new` | `ProductAdminController::form` |
| POST | `/admin/products/new` | `ProductAdminController::save` |
| GET | `/admin/products/{id}/edit` | `ProductAdminController::form` |
| POST | `/admin/products/{id}/edit` | `ProductAdminController::save` |
| POST | `/admin/products/{id}/delete` | `ProductAdminController::delete` |
| POST | `/admin/products/{id}/restore` | `ProductAdminController::restore` |

Ngoài bảng trên, `public/index.php:55–90` đăng ký `GET /health`, `GET /about`, và `GET /health/ready`. Khi bootstrap CouchDB thất bại, nhóm route nghiệp vụ được đăng ký bằng handler lỗi/tạm không sẵn sàng tại khoảng dòng `204–297`.

**Phân biệt COD giữa web và API:** web quản trị dùng `POST /admin/orders/{id}/payment/collect`; API JWT dùng `POST /api/admin/orders/{id}/payment/cod-collected`.

## B. Cấu hình đầy đủ Mango index

Cả `database/couchdb/indexes/catalog_indexes.json` và `scripts/create_catalog_indexes.php` có 9 index. Ddoc chung: `_design/catalog_indexes`. Trong script, tất cả có `type: "json"`. `map.fields` dưới đây là cấu hình trong design document; `fields` là mảng field script gửi tới CouchDB. Bộ lọc trong file JSON dùng biểu thức Mango; script khai báo filter tương đương dạng rút gọn.

| `name` | `type` | `fields` (script / `options.def.fields`) | `map.fields` trong JSON | `partial_filter_selector` script | `partial_filter_selector` trong JSON `map` |
|---|---|---|---|---|---|
| `active_products` | `json` | `["type", "active"]` | `{"type":"asc","active":"asc"}` | `{"type":"product","active":true}` | `{"$and":[{"type":{"$eq":"product"}},{"active":{"$eq":true}}]}` |
| `admin_products` | `json` | `["type"]` | `{"type":"asc"}` | `{"type":"product"}` | `{"type":{"$eq":"product"}}` |
| `active_reviews` | `json` | `["type", "active"]` | `{"type":"asc","active":"asc"}` | `{"type":"review","active":true}` | `{"$and":[{"type":{"$eq":"review"}},{"active":{"$eq":true}}]}` |
| `admin_reviews` | `json` | `["type"]` | `{"type":"asc"}` | `{"type":"review"}` | `{"type":{"$eq":"review"}}` |
| `account_usernames` | `json` | `["type", "auth.username"]` | `{"type":"asc","auth.username":"asc"}` | `{"type":{"$in":["customer","staff"]}}` | `{"type":{"$in":["customer","staff"]}}` |
| `customer_orders` | `json` | `["type", "customer.customer_id", "ordered_at"]` | `{"type":"asc","customer.customer_id":"asc","ordered_at":"asc"}` | `{"type":"order"}` | `{"type":{"$eq":"order"}}` |
| `admin_orders` | `json` | `["type", "status", "ordered_at"]` | `{"type":"asc","status":"asc","ordered_at":"asc"}` | `{"type":"order"}` | `{"type":{"$eq":"order"}}` |
| `admin_orders_by_date` | `json` | `["type", "ordered_at"]` | `{"type":"asc","ordered_at":"asc"}` | `{"type":"order"}` | `{"type":{"$eq":"order"}}` |
| `delivery_tracking_code` | `json` | `["type", "delivery_tracking.tracking_code"]` | `{"type":"asc","delivery_tracking.tracking_code":"asc"}` | `{"type":"order"}` | `{"type":{"$eq":"order"}}` |

Trong JSON, `_id` là `_design/catalog_indexes`, `language` là `query`; mỗi view/index có `reduce: "_count"`. Script đọc danh sách index tại `GET /{db}/_index`, so field/filter; khi cần tạo thì gửi `POST /{db}/_index` với `ddoc: "_design/catalog_indexes"`, `name`, `type: "json"`, và `index: {fields, partial_filter_selector}`. Khi cần thay index, script gọi DELETE trên index cũ trước khi POST tạo lại.

## C. SSE theo dõi đơn hàng

Nguồn: `src/Orders/OrderRealtimeController.php`, `src/Core/StreamedResponse.php`, `public/assets/js/order-realtime.js`.

- `OrderRealtimeController` nhận `ORDER_SSE_CHANGES_TIMEOUT_MS` từ `public/index.php:121–122`; constructor mặc định **8.000 ms**, chặn giá trị trong khoảng **250–8.000 ms** (`OrderRealtimeController.php:17–24`).
- Không có vòng lặp polling vô hạn ở PHP. Mỗi kết nối HTTP SSE: lấy snapshot ban đầu, phát `order_updated`, sau đó gọi duy nhất một request CouchDB `POST /{db}/_changes` với `filter=_doc_ids`, `feed=longpoll`, `since=<sequence>`, `timeout=<changeWaitMs>`, body `{"doc_ids":["<order-id>"]}`. Nếu `results` có phần tử, controller đọc lại document một lần rồi so fingerprint payload. Một response có một lần long-poll, tối đa khoảng 8 giây cộng thời gian xử lý; không có “số vòng lặp tối đa” vì server không chạy loop ở đây (`:90–159`).
- SSE phát `retry: 5000`; phía trình duyệt dùng `EventSource`, tự kết nối lại sau khi response đóng. `StreamedResponse::send()` đặt status, Content-Type, `X-Content-Type-Options: nosniff`, `Cache-Control: no-cache, no-store, private`, các header thêm vào rồi gọi closure. Exception được ghi log (`StreamedResponse.php:23–41`).
- Event names: `order_updated`, `heartbeat`, `reconnecting`. Payload lần lượt là snapshot đơn; `{"alive":true}`; và `{"retry":true}`. Khi CouchDB trả HTTP 400 cho checkpoint/sequence, controller lấy sequence mới, đọc order mới và phát snapshot hoặc `reconnecting`.
- Snapshot `order_updated` (`OrderRealtimeController.php:182–244`) có các trường cấp cao: `order_id`, `status`, `payment_status`, `delivery`, `status_history`. `delivery` là `null` hoặc object có `tracking_code`, `status`, `estimated_delivery_date`, `delivered_at`, `history`; mỗi entry history chỉ lấy `status`, `at`, `note` nếu giá trị là string. `status_history` là danh sách `{status, at}`. `payment_status` được chuẩn hóa thành `paid` nếu flag `paid` là true hoặc status là `paid`, còn lại dùng chuỗi status hoặc mặc định `unpaid`.
- `order-realtime.js:112–134` đăng ký listener cho ba event trên; listener `order_updated` parse `event.data` bằng JSON và gọi `applySnapshot()`.

Ví dụ payload snapshot:

```json
{
  "order_id": "order:...",
  "status": "shipping",
  "payment_status": "unpaid",
  "delivery": {
    "tracking_code": "...",
    "status": "in_transit",
    "estimated_delivery_date": "YYYY-MM-DD",
    "delivered_at": null,
    "history": [{"status": "picked_up", "at": "...", "note": "..."}]
  },
  "status_history": [{"status": "shipping", "at": "..."}]
}
```

## D. Ma trận trạng thái thực tế

### D.1 Order `status`

`OrderWorkflowService.php:13–20` khai báo:

| Trạng thái hiện tại | Có thể chuyển tới |
|---|---|
| `pending` | `confirmed`, `cancelled` |
| `confirmed` | `packing`, `cancelled` |
| `packing` | `shipping`, `cancelled` |
| `shipping` | `delivered` |
| `delivered` | Không có |
| `cancelled` | Không có |

Ràng buộc bổ sung: đơn paid không thể hủy; đơn đang shipping có `delivery_tracking` chỉ chuyển order sang delivered khi tracking đã delivered. Chuyển order sang shipping sẽ khởi tạo tracking nếu chưa có (`:41–46, 91–100, 307–314`). Khách xác nhận đã nhận chỉ được làm khi order đang shipping, rồi service đổi status thành delivered (`:171–212, 219–248`).

### D.2 `delivery_tracking.status`

`OrderWorkflowService.php:23–30` khai báo:

| Trạng thái hiện tại | Có thể chuyển tới |
|---|---|
| `created` | `picked_up`, `failed_delivery` |
| `picked_up` | `in_transit`, `failed_delivery` |
| `in_transit` | `out_for_delivery`, `failed_delivery` |
| `out_for_delivery` | `delivered`, `failed_delivery` |
| `failed_delivery` | `in_transit`, `out_for_delivery` |
| `delivered` | Không có |

Chỉ cập nhật tracking khi order đang shipping. Nếu chưa có tracking, chỉ target `created` được phép để khởi tạo tracking. Khi tracking thành delivered, order cũng thành delivered và có event trong status history (`:107–167`).

### D.3 `payment.status` và `payment.method`

Workflow không khai báo enum payment tổng quát. Các giá trị và điều kiện được code thực hiện:

| Field/value | Hành vi trong code |
|---|---|
| `payment.method = cod` | Checkout mới chỉ nhận `cod`; khởi tạo `status=unpaid`, `paid=false`. Khi khách xác nhận đã nhận hàng, hoặc staff ghi COD đã thu cho đơn delivered, chuyển thành paid. |
| `payment.method = bank_transfer` | Staff xác minh thủ công qua `verifyManualPayment`; không bắt buộc delivered, nhưng đơn cancelled bị từ chối. |
| `payment.method = card` | Cùng luồng xác minh thủ công như bank transfer. |
| `payment.status = unpaid` | Trạng thái checkout ban đầu. |
| `payment.status = paid` | Được gán khi xác nhận/đối soát thanh toán; xử lý lặp lại không tạo audit trùng. |
| Đảo paid về unpaid | Không có workflow chuyển ngược; validator CouchDB từ chối ghi order paid thành unpaid. |

Fixture có các method `cod`, `bank_transfer`, `card` và payment status `paid`, `unpaid`. Đây là các giá trị quan sát trong code/fixture; workflow không kiểm tra một danh sách enum toàn cục khác.

## E. Giới hạn ảnh và công thức giá

### E.1 `ProductImageStorage.php`

Hằng số tại dòng 11–17 và kiểm tra tại dòng 41–52:

- File tối đa: `5_242_880` byte (**5 MiB**, thông báo form ghi 5 MB).
- Chiều rộng tối đa: **10.000 px**.
- Chiều cao tối đa: **10.000 px**.
- Pixel cap: **40.000.000 pixel** (40 megapixel).
- MIME cho phép: `image/jpeg` → `jpg`, `image/png` → `png`, `image/webp` → `webp`.

### E.2 `PricingService.php:11–28`

- `percent`: giảm `round(subtotal * (value / 100), 0, PHP_ROUND_HALF_UP)`, nghĩa là làm tròn đến đồng, half-up.
- `cash`: giảm đúng `value`.
- `shipping`: giảm toàn bộ `shippingFee`.
- Clamp discount: `min(max(0, discount), subtotal + shippingFee)`; tổng thanh toán: `max(0, subtotal + shippingFee - discount)`.
- Phương thức này không đặt mức đơn hàng tối thiểu. Kiểm tra voucher tồn tại, active và còn số lượt được thực hiện ở `CheckoutService::preview`, bên ngoài `PricingService`.

## F. Trích nguyên văn code PHP

### F.1 `CouchDbClient::request()` — `src/Infrastructure/CouchDB/CouchDbClient.php:28–69`

```php
    public function request(string $method, string $path, ?array $payload = null): CouchDbResponse
    {
        $curl = curl_init(rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/'));
        if ($curl === false) {
            throw new RuntimeException('Could not initialize the CouchDB HTTP client.');
        }

        $headers = ['Accept: application/json'];
        $options = [
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeoutSeconds,
            CURLOPT_TIMEOUT => $this->requestTimeoutSeconds,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $this->username . ':' . $this->password,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ];

        if ($payload !== null) {
            $headers[] = 'Content-Type: application/json';
            $encodedPayload = json_encode(
                $payload,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
            );
            $options[CURLOPT_POSTFIELDS] = $encodedPayload;
        }

        $options[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($curl, $options);
        $body = curl_exec($curl);
        $curlError = curl_error($curl);
        $statusCode = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);

        if (!is_string($body)) {
            throw new RuntimeException('CouchDB request failed: ' . ($curlError !== '' ? $curlError : 'no response'));
        }

        return new CouchDbResponse($statusCode, $body);
    }
```

### F.2 Truy vấn Mango — `src/Catalog/ProductRepository.php:22–62`

```php
    /** @return list<array<string, mixed>> */
    public function activeReviews(): array
    {
        return $this->find(['type' => 'review', 'active' => true], 'active_reviews');
    }

    /** @return array<string, mixed>|null */
    public function productByLegacyId(string $legacyId): ?array
    {
        $response = $this->client->request('GET', rawurlencode($this->database) . '/' . rawurlencode('product:' . $legacyId));
        if ($response->statusCode === 404) {
            return null;
        }
        if ($response->statusCode < 200 || $response->statusCode >= 300) {
            throw new RuntimeException('CouchDB could not load product (' . $response->statusCode . ').');
        }
        $document = $response->json();
        return ($document['type'] ?? null) === 'product' && ($document['active'] ?? false) === true ? $document : null;
    }

    /** @param array<string, mixed> $selector
     *  @return list<array<string, mixed>>
     */
    private function find(array $selector, string $indexName): array
    {
        $response = $this->client->request('POST', rawurlencode($this->database) . '/_find', [
            'selector' => $selector,
            'use_index' => ['_design/catalog_indexes', $indexName],
            'limit' => 2000,
            'fields' => ['_id', 'type', 'legacy_id', 'name', 'description', 'category', 'variants', 'images', 'active', 'product_id', 'reviewer_name', 'rating', 'content', 'admin_response'],
        ]);
        if ($response->statusCode < 200 || $response->statusCode >= 300) {
            throw new RuntimeException('CouchDB catalog query failed (' . $response->statusCode . ').');
        }
        $result = $response->json();
        $documents = $result['docs'] ?? null;
        if (!is_array($documents)) {
            throw new RuntimeException('CouchDB catalog response is missing its docs list.');
        }
        return array_values(array_filter($documents, 'is_array'));
    }
```

### F.3 `CheckoutService::reserveStock()` — `src/Checkout/CheckoutService.php:262–301`

```php
    private function reserveStock(string $productId, array $order, array $variants): void
    {
        $operationId = (string) $order['meta']['checkout']['operation_id'];
        for ($attempt = 0; $attempt < 6; ++$attempt) {
            $product = $this->documents->get('product:' . $productId);
            if ($product === null || ($product['active'] ?? false) !== true) throw new CheckoutException('Sản phẩm ' . $productId . ' không còn khả dụng.');
            $product['meta'] = is_array($product['meta'] ?? null) ? $product['meta'] : [];
            $product['meta']['checkout_reservations'] = is_array($product['meta']['checkout_reservations'] ?? null) ? $product['meta']['checkout_reservations'] : [];
            $markers = $product['meta']['checkout_reservations'];
            $marker = $markers[$operationId] ?? ['order_id' => $order['_id'], 'status' => 'reserved', 'variants' => []];
            if (($marker['status'] ?? null) === 'released') throw new CheckoutException('Đơn hàng đã được bù trừ và không thể tiếp tục.');
            $changed = false;
            foreach ($variants as $variantId => $plan) {
                if (isset($marker['variants'][$variantId])) {
                    if ((int) $marker['variants'][$variantId]['quantity'] !== (int) $plan['quantity']) throw new RuntimeException('Stock marker does not match the order.');
                    continue;
                }
                $found = false;
                foreach ($product['variants'] as &$variant) {
                    if (($variant['variant_id'] ?? null) !== $variantId) continue;
                    $found = true;
                    if (($variant['active'] ?? false) !== true || (int) ($variant['price'] ?? -1) !== (int) $plan['unit_price']) throw new CheckoutException('Giá hoặc trạng thái sản phẩm đã thay đổi. Hãy kiểm tra lại giỏ hàng.');
                    if ((int) ($variant['stock'] ?? 0) < (int) $plan['quantity']) throw new CheckoutException('Sản phẩm ' . (string) ($product['name'] ?? $productId) . ' không đủ tồn kho.');
                    $variant['stock'] = (int) $variant['stock'] - (int) $plan['quantity'];
                    $marker['variants'][$variantId] = ['quantity' => (int) $plan['quantity'], 'status' => 'reserved'];
                    $changed = true;
                    break;
                }
                unset($variant);
                if (!$found) throw new CheckoutException('Kích thước sản phẩm không còn khả dụng.');
            }
            if (!$changed) return;
            $product['meta']['checkout_reservations'][$operationId] = $marker;
            $response = $this->documents->put($product);
            if ($response->statusCode === 409) continue;
            $this->assertWriteSucceeded($response, 'reserve stock');
            return;
        }
        throw new RuntimeException('Product stock changed repeatedly during checkout.');
    }
```
