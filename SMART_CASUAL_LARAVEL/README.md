# SMART CASUAL — HỆ THỐNG WEBSITE BÁN HÀNG THỜI TRANG (LARAVEL & MYSQL)

> **Đồ án cuối môn:** Lập trình mã nguồn mở  
> **Khoa:** Công nghệ Thông tin — Bộ môn Kỹ thuật Phần mềm  
> **Trường Đại học Công Thương TP. Hồ Chí Minh (HUIT)**  
> **Nền tảng công nghệ:** Laravel Framework 11.x, PHP 8.3, MySQL 8.x, Eloquent ORM, Blade Template, Bootstrap 5.2.3.

---

## 1. GIỚI THIỆU DỰ ÁN

Website **SMART CASUAL** là hệ thống thương mại điện tử chuyên cung cấp các sản phẩm thời trang (Áo, Quần, Giày) tối giản và hiện đại. Dự án được xây dựng và chuẩn hóa toàn diện từ kiến trúc cơ sở dữ liệu quan hệ gồm 13 bảng nghiệp vụ, tuân thủ nghiêm ngặt các quy chuẩn kỹ thuật của Laravel Framework và đề cương môn học:
- Kiến trúc MVC chuẩn của Laravel Framework.
- Cơ sở dữ liệu quan hệ MySQL với 13 bảng nghiệp vụ, ràng buộc khóa ngoại toàn vẹn và 4 MySQL Triggers tự động hóa tính toán tổng đơn hàng.
- Eloquent ORM với các quan hệ `hasMany`, `belongsTo`, `hasOne`, tính toán giá trị tự động (Accessors).
- Phân quyền người dùng đa vai trò (Multi-Auth): Khách hàng (`Customer`), Nhân viên (`Staff`), Quản lý (`Manager`) được bảo vệ bằng Laravel Middleware (`admin`, `manager`).
- Chức năng tìm kiếm thời gian thực theo từ khóa, lọc danh mục, lọc khoảng giá, sắp xếp và phân trang giữ tham số truy vấn (`withQueryString()`).
- Giỏ hàng linh hoạt hỗ trợ cả khách vãng lai (Session Cart) và khách hàng đã đăng nhập (Database Cart `GIO_HANG`), tự động đồng bộ gộp giỏ hàng khi đăng nhập.
- Quy trình thanh toán đặt hàng nguyên tử (Atomic Checkout Transaction) với kiểm tra khóa dòng tồn kho (`lockForUpdate`), trừ kho an toàn và hoàn trả tồn kho khi hủy đơn.

---

## 2. YÊU CẦU MÔI TRƯỜNG CHẠY

- **Hệ điều hành:** Windows 10/11, Linux hoặc macOS.
- **PHP:** Phiên bản `>= 8.2` (Khuyên dùng PHP 8.3).
  - Các extension bắt buộc: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`, `fileinfo`.
- **Composer:** Phiên bản `>= 2.2` (Khuyên dùng Composer 2.9).
- **Cơ sở dữ liệu:** MySQL 8.x hoặc MariaDB 10.4+.
- **Web Server (Tùy chọn):** Apache/Nginx hoặc sử dụng trực tiếp máy chủ tích hợp `php artisan serve`.

---

## 3. HƯỚNG DẪN CÀI ĐẶT VÀ CHẠY DỰ ÁN TRÊN MÁY KHÁC

### Bước 1: Sao chép thư mục mã nguồn và mở Terminal
Di chuyển vào thư mục dự án:
```bash
cd SMART_CASUAL_LARAVEL
```

### Bước 2: Cài đặt các thư viện phụ thuộc qua Composer
```bash
composer install --prefer-dist --no-interaction
```

### Bước 3: Thiết lập cấu hình môi trường (.env)
Sao chép file mẫu `.env.example` thành `.env` (nếu chưa có):
```bash
copy .env.example .env
```
Mở file `.env` và kiểm tra cấu hình kết nối cơ sở dữ liệu MySQL:
```env
APP_NAME="Smart Casual"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_casual_shop
DB_USERNAME=root
DB_PASSWORD=
```

### Bước 4: Khởi tạo mã khóa ứng dụng (Application Key)
```bash
php artisan key:generate
```

### Bước 5: Tạo liên kết thư mục lưu trữ ảnh (Storage Link)
```bash
php artisan storage:link
```

### Bước 6: Khởi tạo cơ sở dữ liệu và nạp dữ liệu mẫu (Seed Data)
Tạo database MySQL có tên `smart_casual_shop` (nếu chưa tạo):
```sql
CREATE DATABASE smart_casual_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
Chạy migration và seeder để tạo tự động toàn bộ 13 bảng nghiệp vụ, 4 triggers và nạp 841 bản ghi quan hệ mẫu (chuẩn hóa từ 347 documents):
```bash
php artisan migrate:fresh --seed
```
*(Hoặc có thể import trực tiếp file SQL kết xuất sẵn tại `database/smart_casual_shop_dump.sql` vào MySQL).*

### Bước 7: Khởi chạy máy chủ nội bộ
```bash
php artisan serve
```
Truy cập website tại địa chỉ: **`http://localhost:8000`**

---

## 4. DANH SÁCH TÀI KHOẢN TRẢI NGHIỆM VÀ KIỂM THỬ DEMO

| Vai trò (Role) | Cổng đăng nhập | Tài khoản / Email | Mật khẩu | Phạm vi quyền hạn |
|---|---|---|---|---|
| **Khách hàng (Customer)** | `/login` | `demo1@gmail.com` | `password123` | Xem catalog, tìm kiếm, giỏ hàng, checkout, lịch sử đơn, đánh giá sản phẩm. |
| **Khách hàng 2** | `/login` | `demo2@gmail.com` | `password123` | Dùng để kiểm thử phân quyền IDOR giữa các khách hàng khác nhau. |
| **Nhân viên (Staff)** | `/admin/login` | `staff` | `staff123` | Xem đơn hàng, cập nhật quy trình giao hàng, xác nhận thu tiền, xem đánh giá. |
| **Quản trị (Manager)** | `/admin/login` | `admin` | `admin123` | Toàn quyền: Quản lý danh mục, Thêm/Sửa/Ẩn sản phẩm, Thống kê doanh thu, Quản trị đơn. |

---

## 5. CHẠY BỘ KIỂM THỬ TỰ ĐỘNG (AUTOMATED TEST SUITE)

Dự án bao gồm 23 kịch bản kiểm thử tự động (Feature & Unit Tests) bao phủ 100% các yêu cầu nghiệp vụ:
```bash
php artisan test
```
Kết quả kiểm thử: **23 passed (118 assertions)**.
