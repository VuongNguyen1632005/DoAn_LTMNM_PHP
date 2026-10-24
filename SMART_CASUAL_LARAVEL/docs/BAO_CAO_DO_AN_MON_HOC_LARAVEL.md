# BÁO CÁO ĐỒ ÁN CUỐI MÔN HỌC
## HỌC PHẦN: LẬP TRÌNH MÃ NGUỒN MỞ
### ĐỀ TÀI: XÂY DỰNG WEBSITE BÁN HÀNG THỜI TRANG "SMART CASUAL" BẰNG LARAVEL FRAMEWORK VÀ CƠ SỞ DỮ LIỆU MYSQL

---

**TRƯỜNG ĐẠI HỌC CÔNG THƯƠNG TP. HỒ CHÍ MINH (HUIT)**  
**KHOA CÔNG NGHỆ THÔNG TIN — BỘ MÔN KỸ THUẬT PHẦN MỀM**  

- **Sinh viên thực hiện:**
  1. Nguyễn Huy Hoàng — MSSV: [2001230266] — Lớp: [14DHTH02] — Trưởng nhóm
  2. [Thành viên 2 - Điền họ tên] — MSSV: [Điền MSSV] — Thành viên
  3. [Thành viên 3 - Điền họ tên] — MSSV: [Điền MSSV] — Thành viên
- **Giảng viên hướng dẫn:** [Thầy/Cô phụ trách học phần LTMNM]
- **Thời gian thực hiện:** Học kỳ 1 — Năm học 2026 – 2027

---

## MỤC LỤC

1. GIỚI THIỆU ĐỀ TÀI
   - 1.1. Lý do chọn đề tài
   - 1.2. Mục tiêu đề tài
   - 1.3. Phạm vi thực hiện
2. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG
   - 2.1. Phân tích đối tượng và vai trò người dùng
   - 2.2. Sơ đồ UML Use Case tổng quan
   - 2.3. Sơ đồ và thiết kế Cơ sở dữ liệu (ERD)
   - 2.4. Danh mục 13 bảng nghiệp vụ chi tiết
   - 2.5. Thiết kế Triggers và tính toán tự động
3. CÔNG NGHỆ VÀ KIẾN TRÚC TRIỂN KHAI
   - 3.1. Kiến trúc tổng thể hệ thống
   - 3.2. Danh mục công nghệ và thư viện sử dụng
   - 3.3. Cơ chế xác thực và phân quyền (Multi-Auth & Middleware)
   - 3.4. Mô hình ORM Eloquent và bảo vệ toàn vẹn dữ liệu
4. HƯỚNG DẪN CÀI ĐẶT VÀ SỬ DỤNG
   - 4.1. Yêu cầu môi trường hệ thống
   - 4.2. Các bước cài đặt chi tiết từ mã nguồn
   - 4.3. Danh sách tài khoản thử nghiệm hệ thống
5. KẾT QUẢ ĐẠT ĐƯỢC VÀ CHỨC NĂNG HỆ THỐNG
   - 5.1. Phân hệ Khách hàng (Customer Storefront)
   - 5.2. Phân hệ Quản trị viên (Admin Portal)
   - 5.3. Bảng đối chiếu yêu cầu kỹ thuật (Traceability Matrix)
6. KIỂM THỬ VÀ ĐÁNH GIÁ CHẤT LƯỢNG SẢN PHẨM
   - 6.1. Phương pháp kiểm thử tự động (Feature Tests)
   - 6.2. Kết quả kiểm thử 19 kịch bản kiểm thử tự động
7. PHÂN CÔNG CÔNG VIỆC VÀ ĐÁNH GIÁ ĐÓNG GÓP NHÓM
   - 7.1. Bảng phân công nhiệm vụ chi tiết
   - 7.2. Tỷ lệ hoàn thành công việc
8. HẠN CHẾ VÀ HƯỚNG PHÁT TRIỂN
   - 8.1. Các hạn chế hiện tại
   - 8.2. Kế hoạch nâng cấp và phát triển mở rộng
9. KẾT LUẬN
10. TÀI LIỆU THAM KHẢO

---

## 1. GIỚI THIỆU ĐỀ TÀI

### 1.1. Lý do chọn đề tài
Trong thời đại chuyển đổi số và phát triển mạnh mẽ của thương mại điện tử, việc mua sắm quần áo và trang phục qua các nền tảng trực tuyến đã trở thành thói quen phổ biến của người tiêu dùng hiện đại. Đối với các cửa hàng thời trang vừa và nhỏ, việc sở hữu một website bán hàng chuyên nghiệp, tải nhanh, trực quan, hỗ trợ quản lý tồn kho theo từng kích cỡ (size) và theo dõi tiến độ giao nhận là yếu tố sống còn để nâng cao năng lực cạnh tranh.

Đề tài **"Xây dựng Website Bán hàng Thời trang Smart Casual bằng Laravel Framework và MySQL"** được nhóm lựa chọn nhằm giải quyết trọn vẹn bài toán thương mại điện tử thực tế. Dự án không chỉ là cơ hội để vận dụng toàn diện các kiến thức đã học trong học phần Lập trình mã nguồn mở (PHP 8.3, Laravel Framework, MySQL, Eloquent ORM, Blade Template, Bootstrap 5), mà còn hướng đến việc xây dựng một sản phẩm phần mềm có tính ứng dụng cao, cấu trúc mã nguồn sáng sủa và khả năng mở rộng bền vững.

### 1.2. Mục tiêu đề tài
- **Về chuẩn đầu ra học phần (CLOs):**
  - **CLO1:** Nắm vững và làm chủ kỹ thuật xây dựng ứng dụng web hiện đại với Laravel Framework, PHP hướng đối tượng và cơ sở dữ liệu quan hệ MySQL.
  - **CLO2:** Vận dụng các tiêu chuẩn thiết kế giao diện (UI/UX), kiểm tra tính đáp ứng (Responsive) và tối ưu trải nghiệm mua sắm của người dùng.
  - **CLO3:** Thể hiện tinh thần phối hợp làm việc nhóm, phân công rõ ràng, theo dõi tiến độ qua hệ thống quản lý phiên bản Git/GitHub.
  - **CLO4:** Hoàn thiện một phần mềm có khả năng vận hành thực tế cho ngành hàng thời trang với đầy đủ quy trình bán hàng, từ catalog sản phẩm, giỏ hàng, đặt hàng đến quản trị đơn và phân tích doanh thu.
  - **CLO5:** Tự chủ, chịu trách nhiệm và tuân thủ các nguyên tắc liêm chính học thuật trong toàn bộ quá trình phát triển dự án.

- **Về mục tiêu kỹ thuật phần mềm:**
  - Thiết kế và chuẩn hóa cơ sở dữ liệu quan hệ gồm 13 bảng nghiệp vụ, đảm bảo chuẩn hóa 3NF, toàn vẹn khóa ngoại và tự động hóa qua Database Triggers.
  - Áp dụng triệt để mô hình MVC của Laravel, phân tách mạch lạc giữa Model (Eloquent), Controller, Request Validation, Service Layer và Blade View.
  - Xây dựng hệ thống bảo mật nhiều lớp: Chống tấn công CSRF, ngăn chặn SQL Injection qua PDO Parameter Binding, lọc XSS qua Blade Escaping, băm mật khẩu an toàn với thuật toán Bcrypt và chống lỗi leo thang đặc quyền (IDOR).
  - Đảm bảo chất lượng bằng bộ kiểm thử tự động (Automated Feature Tests) đạt tỷ lệ vượt qua 100%.

### 1.3. Phạm vi thực hiện
- **Đối tượng phục vụ:**
  - *Khách hàng mua sắm (Customer / Guest):* Người tiêu dùng tìm kiếm trang phục, xem chi tiết kích cỡ, chọn mua vào giỏ hàng, áp dụng mã ưu đãi, đặt hàng thanh toán khi nhận hàng (COD) và tra cứu lịch sử mua hàng.
  - *Nhân viên bán hàng (Staff):* Tiếp nhận đơn hàng, cập nhật các giai đoạn giao nhận (chờ duyệt, xác nhận, đóng gói, xuất kho giao hàng) và xác nhận thu tiền.
  - *Quản trị viên (Manager):* Toàn quyền quản trị danh mục, thêm mới, điều chỉnh giá bán/tồn kho sản phẩm, ẩn sản phẩm khỏi storefront, xem báo cáo doanh thu tài chính.
- **Phạm vi chức năng:**
  - Quản lý danh mục và kích cỡ trang phục.
  - Quản lý sản phẩm, hình ảnh và chi tiết biến thể kích cỡ/giá/kho.
  - Tìm kiếm thời gian thực, lọc đa tiêu chí (danh mục, mức giá) và phân trang dữ liệu.
  - Giỏ hàng phiên làm việc (Session Cart) và giỏ hàng thành viên lưu trữ CSDL (`GIO_HANG`), tự động đồng bộ khi đăng nhập.
  - Thanh toán nguyên tử (Atomic Checkout) với cơ chế khóa dòng tồn kho (`lockForUpdate`), mã giảm giá và chi phí vận chuyển.
  - Đánh giá sản phẩm (Review & Rating) có kiểm duyệt.
  - Thống kê doanh thu theo trạng thái và theo tháng.

---

## 2. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG

### 2.1. Phân tích đối tượng và vai trò người dùng
Hệ thống phân tách mạch lạc 3 nhóm người dùng chính:
1. **Khách hàng vãng lai (Guest):** Có thể duyệt danh mục, tìm kiếm, xem chi tiết sản phẩm, thêm hàng vào giỏ hàng phiên, đăng ký tài khoản mới và đặt hàng không bắt buộc đăng nhập.
2. **Khách hàng thành viên (Customer):** Được cấp tài khoản, tự động đồng bộ giỏ hàng cá nhân, theo dõi lịch sử và trạng thái xử lý của toàn bộ đơn hàng cá nhân, xác nhận đã nhận hàng thành công và gửi đánh giá nhận xét sao cho sản phẩm.
3. **Nhân viên & Quản trị viên (Staff & Manager):** Đăng nhập tại cổng quản trị `/admin/login`. Nhân viên có quyền xem và xử lý đơn hàng, điều chỉnh trạng thái giao nhận và quản lý đánh giá. Quản trị viên (Manager) có thêm quyền tối cao về CRUD danh mục, CRUD sản phẩm, ẩn sản phẩm và truy cập báo cáo tài chính doanh thu.

### 2.2. Sơ đồ UML Use Case tổng quan

```
+-----------------------------------------------------------------------------------+
|                        HỆ THỐNG WEBSITE SMART CASUAL                              |
|                                                                                   |
|  [Khách vãng lai]                                                                 |
|         |-- (Xem Catalog, Tìm kiếm & Lọc sản phẩm)                                |
|         |-- (Xem Chi tiết sản phẩm & Kích thước)                                  |
|         |-- (Thêm sản phẩm vào Giỏ hàng)                                          |
|         |-- (Đăng ký tài khoản mới)                                               |
|         +-- (Đăng nhập hệ thống)                                                  |
|                                                                                   |
|  [Khách hàng thành viên]                                                          |
|         |-- [Kế thừa mọi quyền của Khách vãng lai]                                |
|         |-- (Đồng bộ giỏ hàng đa phiên)                                           |
|         |-- (Đặt hàng & Áp dụng mã giảm giá)                                      |
|         |-- (Xem lịch sử đơn hàng cá nhân)                                        |
|         |-- (Xác nhận đã nhận hàng / Hủy đơn chờ duyệt)                           |
|         +-- (Đánh giá nhận xét sản phẩm)                                          |
|                                                                                   |
|  [Nhân viên bán hàng (Staff)]                                                     |
|         |-- (Đăng nhập khu vực quản trị /admin)                                   |
|         |-- (Xem danh sách & chi tiết đơn hàng)                                   |
|         |-- (Cập nhật tiến trình giao nhận đơn hàng)                              |
|         |-- (Xác nhận tình trạng thanh toán đơn hàng)                             |
|         +-- (Kiểm duyệt hiển thị đánh giá của khách)                              |
|                                                                                   |
|  [Quản trị viên (Manager)]                                                        |
|         |-- [Kế thừa mọi quyền của Nhân viên]                                     |
|         |-- (Quản lý Danh mục - Thêm / Sửa / Xóa)                                 |
|         |-- (Quản lý Sản phẩm - Thêm / Sửa / Ẩn / Khôi phục)                      |
|         |-- (Upload hình ảnh sản phẩm vào hệ thống)                               |
|         +-- (Xem Báo cáo & Thống kê Doanh thu)                                    |
|                                                                                   |
+-----------------------------------------------------------------------------------+
```
*Hình 2.1: Sơ đồ Use Case tổng quan thể hiện tương tác của từng vai trò trong hệ thống Smart Casual.*

### 2.3. Sơ đồ và thiết kế Cơ sở dữ liệu (ERD)
Cơ sở dữ liệu của hệ thống được xây dựng trên hệ quản trị CSDL quan hệ MySQL 8.x, bao gồm 13 bảng nghiệp vụ liên kết chặt chẽ thông qua các ràng buộc khóa chính và khóa ngoại:

```
[DANH_MUC] 1 ----- N [SAN_PHAM] 1 ----- N [CHI_TIET_SP] 1 ----- N [GIO_HANG]
                         |                      |                     |
                         |                      |                     |
                         1                      1                     N
                         |                      |                     |
                         N                      N                     |
                   [HINH_ANH_SP]        [CHI_TIET_DON_HANG]           |
                         |                      |                     |
                         |                      N                     |
                         |                 [DON_HANG] N ------------- 1 [KHACH_HANG]
                         |                      |                          |
                         |                      | 1                        |
                         |                      |                          |
                         |                      N                          |
                    [DANH_GIA] N ---------------|--------------------------+
                                                |
                                                N ----- 1 [PHUONG_THUC_VAN_CHUYEN]
                                                |
                                                N ----- 1 [MA_GIAM_GIA]
                                                |
                                                N ----- 1 [NHAN_VIEN]
```
*Hình 2.2: Sơ đồ quan hệ thực thể (ERD) của hệ thống thương mại điện tử Smart Casual.*

### 2.4. Danh mục 13 bảng nghiệp vụ chi tiết

1. **DANH_MUC (Danh mục sản phẩm):**
   - `MaDanhMuc` (VARCHAR(10), PK): Mã danh mục chữ in hoa ('A': Áo, 'Q': Quần, 'G': Giày).
   - `TenDanhMuc` (VARCHAR(100)): Tên hiển thị của danh mục.
   - `TrangThai` (BOOLEAN): Trạng thái hoạt động (1: Hoạt động, 0: Tạm khóa).

2. **KICH_THUOC (Bảng kích cỡ chuẩn):**
   - `MaKichThuoc` (VARCHAR(10), PK): Ký hiệu size (S, M, L, XL, 38, 39, 40...).
   - `TenKichThuoc` (VARCHAR(50)): Tên hiển thị đầy đủ của kích cỡ.
   - `TrangThai` (BOOLEAN): Trạng thái kích hoạt.

3. **SAN_PHAM (Sản phẩm gốc):**
   - `MaSP` (VARCHAR(20), PK): Mã sản phẩm (A01, A02, Q01, G01...).
   - `MaDanhMuc` (VARCHAR(10), FK): Liên kết bảng `DANH_MUC`.
   - `TenSanPham` (VARCHAR(200)): Tên gọi thương mại của sản phẩm.
   - `MoTa` (TEXT): Mô tả kiểu dáng, chất liệu, hướng dẫn bảo quản.
   - `TrangThai` (BOOLEAN): Trạng thái kinh doanh (1: Đang bán, 0: Ẩn).

4. **CHI_TIET_SP (Biến thể chi tiết sản phẩm theo Size):**
   - `MaCTSP` (VARCHAR(30), PK): Mã biến thể duy nhất (CTA01SzS, CTA01SzM...).
   - `MaSP` (VARCHAR(20), FK): Liên kết `SAN_PHAM`.
   - `MaKichThuoc` (VARCHAR(10), FK): Liên kết `KICH_THUOC`.
   - `GiaBan` (DECIMAL(18,2)): Đơn giá bán chính thức của kích cỡ đó.
   - `SoLuong` (INT UNSIGNED): Số lượng tồn kho sẵn sàng bán.
   - `TrangThai` (BOOLEAN): Trạng thái khả dụng.
   - *Ràng buộc:* UNIQUE KEY (`MaSP`, `MaKichThuoc`) — Mỗi sản phẩm chỉ có một bản ghi duy nhất cho mỗi kích cỡ.

5. **HINH_ANH_SP (Bộ sưu tập hình ảnh sản phẩm):**
   - `MaHinhAnh` (VARCHAR(30), PK): Mã hình ảnh.
   - `MaSP` (VARCHAR(20), FK): Thuộc về sản phẩm nào.
   - `DuongDan` (VARCHAR(255)): Đường dẫn tương đối lưu trữ ảnh.
   - `LaAnhChinh` (BOOLEAN): Đánh dấu 1 nếu là ảnh đại diện chính của sản phẩm.
   - `TrangThai` (BOOLEAN): Trạng thái sử dụng.

6. **KHACH_HANG (Tài khoản khách hàng thành viên):**
   - `MaKH` (VARCHAR(20), PK): Mã khách hàng (KH001, KH002...).
   - `TenDangNhap` (VARCHAR(100), UNIQUE): Tên đăng nhập duy nhất.
   - `MatKhau` (VARCHAR(255)): Mật khẩu đã băm bằng thuật toán Bcrypt.
   - `HoTen` (VARCHAR(100)): Họ và tên đầy đủ của khách hàng.
   - `SoDienThoai` (VARCHAR(20)): Số điện thoại nhận hàng.
   - `Email` (VARCHAR(100), UNIQUE): Địa chỉ email nhận thông báo.
   - `DiaChi` (VARCHAR(255)): Địa chỉ mặc định.
   - `TrangThai` (BOOLEAN): Trạng thái tài khoản (1: Bình thường, 0: Bị khóa).

7. **NHAN_VIEN (Tài khoản nhân viên và ban quản lý):**
   - `MaNV` (VARCHAR(20), PK): Mã nhân sự (NV001, NV002...).
   - `TenDangNhap` (VARCHAR(100), UNIQUE): Tên đăng nhập portal quản trị.
   - `MatKhau` (VARCHAR(255)): Mật khẩu Bcrypt an toàn.
   - `HoTen` (VARCHAR(100)): Họ tên nhân sự.
   - `ChucVu` (VARCHAR(50)): Phân vai chức vụ ('manager': Quản lý, 'staff': Nhân viên).
   - `TrangThai` (BOOLEAN): Trạng thái nhân sự.

8. **MA_GIAM_GIA (Mã khuyến mãi & Voucher):**
   - `MaCode` (VARCHAR(50), PK): Ký tự mã giảm giá (SAVE10, FREESHIP...).
   - `GiaTri` (DECIMAL(18,2)): Tỷ lệ % giảm hoặc số tiền khấu trừ trực tiếp.
   - `LoaiGiamGia` (VARCHAR(20)): 'percent' (phần trăm), 'cash' (tiền mặt), 'shipping' (miễn giảm ship).
   - `SoLuong` (INT): Số lượt sử dụng còn lại.
   - `TrangThai` (BOOLEAN): Trạng thái áp dụng.

9. **PHUONG_THUC_VAN_CHUYEN (Đơn vị và phương thức giao hàng):**
   - `MaPTVC` (VARCHAR(20), PK): Ký hiệu phương thức ('HT': Hỏa tốc, 'TC': Tiêu chuẩn).
   - `TenPTVC` (VARCHAR(100)): Tên phương thức vận chuyển.
   - `GiaVanChuyen` (DECIMAL(18,2)): Cước phí vận chuyển quy định.
   - `TrangThai` (BOOLEAN): Trạng thái cung cấp dịch vụ.

10. **GIO_HANG (Giỏ hàng thành viên lưu trữ CSDL):**
    - `MaKH` (VARCHAR(20), FK): Khách hàng sở hữu.
    - `MaCTSP` (VARCHAR(30), FK): Biến thể kích cỡ sản phẩm được chọn.
    - `SoLuong` (INT): Số lượng đặt mua.
    - *Khóa chính tổng hợp:* PRIMARY KEY (`MaKH`, `MaCTSP`).

11. **DON_HANG (Hóa đơn và thông tin đặt hàng tổng quát):**
    - `MaDonHang` (VARCHAR(30), PK): Mã đơn hàng duy nhất theo định dạng ngày + chuỗi ngẫu nhiên.
    - `MaKH` (VARCHAR(20), FK, NULLABLE): Liên kết khách hàng nếu là thành viên đã đăng nhập.
    - `MaNV` (VARCHAR(20), FK, NULLABLE): Nhân viên tiếp nhận xử lý đơn.
    - `TenNguoiNhan` (VARCHAR(100)): Họ tên người nhận tại thời điểm đặt hàng.
    - `SoDienThoai` (VARCHAR(20)): Số điện thoại nhận hàng.
    - `Email` (VARCHAR(100), NULLABLE): Email nhận thông báo tình trạng đơn.
    - `MaCode` (VARCHAR(50), FK, NULLABLE): Mã giảm giá được áp dụng.
    - `MaPTVC` (VARCHAR(20), FK): Phương thức giao hàng đã chọn.
    - `NgayDat` (DATETIME): Thời điểm tạo đơn hàng.
    - `DiaChiGiao` (VARCHAR(255)): Địa chỉ giao hàng thực tế.
    - `TongSoLuong` (INT): Tổng số lượng mặt hàng trong đơn.
    - `TongTien` (DECIMAL(18,2)): Tổng giá trị tiền hàng trước giảm giá và cước vận chuyển.
    - `GiamGia` (DECIMAL(18,2)): Số tiền giảm giá được hưởng.
    - `PhiVanChuyen` (DECIMAL(18,2)): Cước phí giao hàng tính vào đơn.
    - `ThanhTien` (DECIMAL(18,2)): Tổng số tiền thực thu cuối cùng.
    - `PhuongThucThanhToan` (VARCHAR(50)): Hình thức thanh toán ('COD', 'BANK', 'MOMO').
    - `DaThanhToan` (BOOLEAN): Tình trạng thanh toán tiền hàng (0: Chưa thu, 1: Đã thu).
    - `TrangThaiDonHang` (VARCHAR(50)): Tiến trình đơn hàng ('pending', 'confirmed', 'packing', 'shipping', 'delivered', 'cancelled').

12. **CHI_TIET_DON_HANG (Chi tiết các mặt hàng trong đơn):**
    - `MaDonHang` (VARCHAR(30), FK): Thuộc về đơn hàng nào.
    - `MaCTSP` (VARCHAR(30), FK): Mặt hàng biến thể nào.
    - `SoLuong` (INT): Số lượng mua.
    - `DonGia` (DECIMAL(18,2)): Giá bán snapshot tại thời điểm mua hàng (độc lập với giá tương lai của sản phẩm).
    - `ThanhTien` (DECIMAL(18,2)): Đơn giá × Số lượng.
    - *Khóa chính tổng hợp:* PRIMARY KEY (`MaDonHang`, `MaCTSP`).

13. **DANH_GIA (Đánh giá và nhận xét từ khách hàng):**
    - `MaDanhGia` (VARCHAR(30), PK): Mã đánh giá.
    - `MaSP` (VARCHAR(20), FK): Đánh giá cho sản phẩm nào.
    - `MaKH` (VARCHAR(20), FK, NULLABLE): Khách hàng đánh giá (nếu có tài khoản).
    - `TenKhachHang` (VARCHAR(100)): Tên người đánh giá hiển thị công khai.
    - `SoSao` (TINYINT UNSIGNED): Điểm đánh giá chất lượng (1 đến 5 sao).
    - `NoiDung` (TEXT): Nội dung phản hồi chi tiết.
    - `TrangThai` (BOOLEAN): Trạng thái kiểm duyệt (1: Cho phép hiển thị, 0: Ẩn vi phạm).

### 2.5. Thiết kế Triggers và tính toán tự động
Để đáp ứng triệt để yêu cầu bảo toàn toàn vẹn dữ liệu từ hệ thống gốc và loại bỏ rủi ro sai lệch số tiền do tính toán thủ công, hệ thống cài đặt song song 2 tầng kiểm soát:
- **Tầng Database Triggers (MySQL Triggers):**
  1. `trg_tudong_lay_phiship_insert` (BEFORE INSERT ON `DON_HANG`): Khi một đơn hàng mới được tạo mà trường phí ship bằng 0 hoặc chưa khai báo, trigger tự động tra cứu bảng `PHUONG_THUC_VAN_CHUYEN` để lấy mức giá chuẩn gán vào đơn hàng, đồng thời thiết lập `ThanhTien = GREATEST(0, TongTien + PhiVanChuyen - GiamGia)`.
  2. `trg_capnhat_tong_donhang_after_insert` (AFTER INSERT ON `CHI_TIET_DON_HANG`): Tự động tính toán lại `TongSoLuong` và `TongTien` bằng lệnh `SUM` từ chi tiết đơn và cập nhật ngay vào bảng cha `DON_HANG`.
  3. `trg_capnhat_tong_donhang_after_update` (AFTER UPDATE ON `CHI_TIET_DON_HANG`): Cập nhật tự động lại tổng số lượng và số tiền nếu có sự thay đổi về số lượng/đơn giá chi tiết.
  4. `trg_capnhat_tong_donhang_after_delete` (AFTER DELETE ON `CHI_TIET_DON_HANG`): Điều chỉnh lại tổng đơn hàng khi một dòng chi tiết bị xóa bỏ.
- **Tầng Application Service (`CheckoutService`):**
  Sử dụng cơ chế `DB::transaction()` bao bọc toàn bộ chu trình đặt hàng. Mọi thao tác kiểm tra số lượng tồn kho đều dùng `lockForUpdate()`, đảm bảo tính nguyên tử tuyệt đối (Atomicity) và ngăn chặn hoàn toàn hiện tượng Overselling (bán vượt tồn kho khi có nhiều khách hàng cùng bấm đặt hàng tại một mili-giây).

---

## 3. CÔNG NGHỆ VÀ KIẾN TRÚC TRIỂN KHAI

### 3.1. Kiến trúc tổng thể hệ thống
Dự án được xây dựng theo kiến trúc **Model-View-Controller (MVC)** chuẩn mực của Laravel 11 kết hợp với **Service Layer Pattern**:

```
[Khách hàng / Quản trị viên]
             │ (HTTP Requests)
             ▼
     [public/index.php]
             │
             ▼
      [Laravel Router] ──────> [Middleware Pipeline]
     (routes/web.php)         (Auth, AdminMiddleware)
             │
             ▼
     [Controller Layer]
(Catalog, Cart, Checkout, Admin)
             │
      ┌──────┴────────────────┐
      ▼                       ▼
[Service Layer]        [Eloquent ORM Models]
(CheckoutService,      (SanPham, DanhMuc, DonHang...)
 CartService)                 │
      │                       ▼
      └──────────────> [MySQL 8.x Database]
```
*Hình 3.1: Sơ đồ luồng kiến trúc phần mềm hệ thống Smart Casual.*

### 3.2. Danh mục công nghệ và thư viện sử dụng
- **Ngôn ngữ lập trình chính:** PHP 8.3 (Tận dụng typed properties, match expressions, readonly classes).
- **Web Framework:** Laravel Framework 11.57.0 (Phiên bản LTS hiện đại được Composer hỗ trợ chính thức).
- **Hệ quản trị CSDL:** MySQL Community Server 8.4.3.
- **Data Access:** Laravel Eloquent ORM (Thực thi truy vấn thông qua PDO Parameter Binding an toàn).
- **Template Engine:** Blade Templating Engine (`resources/views/**/*.blade.php`).
- **Giao diện & UI Toolkit:** HTML5, CSS3, JavaScript Vanilla, Bootstrap 5.2.3, FontAwesome 6 Free.
- **Hệ thống kiểm thử tự động:** PHPUnit 10.5.66.
- **Quản lý gói & Thư viện:** Composer 2.9.4.

### 3.3. Cơ chế xác thực và phân quyền (Multi-Auth & Middleware)
Hệ thống triển khai mô hình **Multi-Auth Guards** trong tệp cấu hình `config/auth.php`:
- Guard `web`: Sử dụng provider `khach_hangs` trỏ vào Model `App\Models\KhachHang`, khóa chính `MaKH`, trường mật khẩu `MatKhau`.
- Guard `admin`: Sử dụng provider `nhan_viens` trỏ vào Model `App\Models\NhanVien`, khóa chính `MaNV`, trường mật khẩu `MatKhau`.

Các Middleware tự chế bảo vệ route được đăng ký định danh trong `bootstrap/app.php`:
- `admin` (`App\Http\Middleware\AdminMiddleware`): Kiểm tra xem người dùng đã đăng nhập tài khoản nhân sự hay chưa (`Auth::guard('admin')->check()`) và tài khoản có đang ở trạng thái kích hoạt hay không. Nếu người dùng chưa đăng nhập hoặc là khách hàng thường, hệ thống tự động chặn và chuyển hướng về `/admin/login` kèm thông báo lỗi.
- `manager` (`App\Http\Middleware\ManagerOnlyMiddleware`): Kiểm tra quyền hạn cấp cao; chỉ cho phép nhân sự có chức vụ `manager` thực hiện các thao tác nhạy cảm như xóa danh mục hoặc điều chỉnh cấu hình hệ thống.

---

## 4. HƯỚNG DẪN CÀI ĐẶT VÀ SỬ DỤNG

### 4.1. Yêu cầu môi trường hệ thống
- Máy tính chạy hệ điều hành Windows 10/11, macOS hoặc Linux.
- Đã cài đặt PHP 8.2 trở lên (Khuyến nghị dùng Laragon hoặc XAMPP với PHP 8.3).
- Đã cài đặt Composer phiên bản 2.2 trở lên.
- Đã kích hoạt dịch vụ MySQL (cổng 3306 mặc định).

### 4.2. Các bước cài đặt chi tiết từ mã nguồn
- **Bước 1: Giải nén hoặc mở mã nguồn trong Terminal:**
  ```bash
  cd SMART_CASUAL_LARAVEL
  ```
- **Bước 2: Cài đặt các gói thư viện Composer:**
  ```bash
  composer install --prefer-dist --no-interaction
  ```
- **Bước 3: Thiết lập cấu hình môi trường (.env):**
  Tạo tệp `.env` từ `.env.example`, điền thông tin kết nối CSDL MySQL:
  ```env
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=smart_casual_shop
  DB_USERNAME=root
  DB_PASSWORD=
  ```
- **Bước 4: Sinh khóa bí mật ứng dụng (App Key) & Liên kết lưu trữ ảnh:**
  ```bash
  php artisan key:generate
  php artisan storage:link
  ```
- **Bước 5: Khởi tạo database và nạp dữ liệu mẫu hoàn chỉnh:**
  Mở MySQL tạo database: `CREATE DATABASE smart_casual_shop CHARACTER SET utf8mb4;`
  Chạy lệnh migration và seed dữ liệu mẫu:
  ```bash
  php artisan migrate:fresh --seed
  ```
- **Bước 6: Khởi chạy máy chủ thử nghiệm:**
  ```bash
  php artisan serve
  ```
  Truy cập hệ thống tại: `http://localhost:8000`

### 4.3. Danh sách tài khoản thử nghiệm hệ thống
Để phục vụ công tác chấm điểm và bảo vệ đồ án, hệ thống cung cấp sẵn các tài khoản demo đã được nạp sẵn vào cơ sở dữ liệu:
- **Tài khoản Khách hàng (Customer):**
  - Đăng nhập tại: `http://localhost:8000/login`
  - Tên đăng nhập / Email: `demo1@gmail.com`
  - Mật khẩu: `password123`
- **Tài khoản Khách hàng số 2 (Dùng kiểm thử phân quyền IDOR):**
  - Email: `demo2@gmail.com` | Mật khẩu: `password123`
- **Tài khoản Quản lý cửa hàng (Manager / Admin):**
  - Đăng nhập tại: `http://localhost:8000/admin/login`
  - Tên đăng nhập: `admin` | Mật khẩu: `admin123`
- **Tài khoản Nhân viên bán hàng (Staff):**
  - Đăng nhập tại: `http://localhost:8000/admin/login`
  - Tên đăng nhập: `staff` | Mật khẩu: `staff123`

---

## 5. KẾT QUẢ ĐẠT ĐƯỢC VÀ CHỨC NĂNG HỆ THỐNG

### 5.1. Phân hệ Khách hàng (Customer Storefront)
- **Trang chủ & Catalog sản phẩm (`/`):**
  - Hiển thị banner nhận diện thương hiệu Smart Casual thời trang chỉn chu.
  - Mục sản phẩm thịnh hành (Trending Items) lọc theo mã ưu tiên.
  - Toàn bộ danh mục 48 sản phẩm hiển thị dạng lưới thẻ (Product Card), hiển thị ảnh đại diện chính, tên, giá bán nhỏ nhất, danh mục và điểm sao đánh giá trung bình.
  - Thanh tìm kiếm và bộ lọc đa tiêu chí: Lọc tức thì theo từ khóa tên sản phẩm, phân loại theo danh mục (Áo, Quần, Giày), chọn phân khúc mức giá và sắp xếp giá tăng/giảm dần.
  - Phân trang chuẩn 16 sản phẩm/trang, bảo toàn toàn bộ tham số tìm kiếm khi chuyển trang qua `withQueryString()`.
- **Trang Chi tiết sản phẩm (`/products/{id}`):**
  - Thư viện ảnh chi tiết với tính năng xem ảnh phóng to và chuyển đổi ảnh thu nhỏ (Thumbnails) mượt mà bằng JavaScript.
  - Hiển thị danh sách kích thước (Size) trực quan kèm trạng thái còn hàng/hết hàng và giá bán tương ứng từng size.
  - Form thêm vào giỏ hàng với lựa chọn size và nút tăng giảm số lượng.
  - Khu vực đánh giá nhận xét của khách hàng kèm điểm sao trung bình, cho phép gửi nhận xét mới và hiển thị phản hồi của cửa hàng.
  - Danh sách 4 sản phẩm liên quan trong cùng danh mục gợi ý mua kèm.
- **Trang Giỏ hàng (`/cart`):**
  - Liệt kê các sản phẩm đã chọn, hiển thị ảnh thu nhỏ, tên, kích cỡ, đơn giá, số lượng và tổng tiền từng dòng.
  - Cho phép thay đổi số lượng mua trực tiếp (tự động kiểm tra trần số lượng tồn kho của size đó).
  - Nút xóa từng món hàng và nút làm trống toàn bộ giỏ hàng có hộp thoại xác nhận.
  - Tự động gộp giỏ hàng khách vãng lai vào tài khoản thành viên ngay khi đăng nhập.
- **Trang Thanh toán (`/checkout`):**
  - Bảng tổng kết đơn hàng bên phải hiển thị rõ tiền hàng, cước vận chuyển, số tiền được giảm giá và tổng thanh toán cuối cùng.
  - Form nhập thông tin người nhận hàng (Họ tên, số điện thoại, email, địa chỉ chi tiết, ghi chú). Tự động điền thông tin nếu khách đã có tài khoản.
  - Lựa chọn phương thức giao nhận (Hỏa tốc 30.000₫ hoặc Tiêu chuẩn 15.000₫).
  - Ô nhập mã giảm giá (áp dụng kiểm tra mã hợp lệ như SAVE10 giảm 10%).
  - Lựa chọn hình thức thanh toán: COD (Thanh toán khi nhận hàng) hoặc chuyển khoản ngân hàng.
- **Trang Lịch sử & Chi tiết đơn hàng (`/account/orders` và `/account/orders/{id}`):**
  - Bảng theo dõi toàn bộ các đơn hàng đã đặt của cá nhân với ngày đặt, tổng tiền và nhãn trạng thái trực quan bằng màu sắc.
  - Xem chi tiết từng món đồ trong đơn, thông tin người nhận, cước phí và mã vận đơn.
  - Nút bấm *"Xác nhận đã nhận được hàng"* đối với đơn đang giao và nút *"Hủy đơn hàng"* đối với đơn đang chờ duyệt (tự động hoàn trả số lượng vào kho).

### 5.2. Phân hệ Quản trị viên (Admin Portal)
- **Bảng điều khiển tổng quan (`/admin`):**
  - Thẻ thống kê (Stat Cards) chỉ số kinh doanh: Tổng doanh thu thực tế, Tổng đơn hàng, Số đơn chờ xử lý, Tổng số sản phẩm và số lượng khách hàng thành viên.
  - Bảng 5 đơn hàng mới nhất và danh sách các đánh giá khách hàng mới gửi.
- **Quản lý Sản phẩm (`/admin/products`):**
  - Danh sách toàn bộ sản phẩm kèm bộ lọc tìm kiếm theo tên/mã, danh mục và trạng thái đang bán/đang ẩn.
  - Chức năng thêm mới sản phẩm (`/admin/products/create`): Upload file ảnh qua Laravel Storage, tự động tạo biến thể size và thiết lập giá bán.
  - Chỉnh sửa thông tin sản phẩm (`/admin/products/{id}/edit`): Cập nhật tên, danh mục, giá bán, tồn kho và thay thế ảnh đại diện.
  - Chức năng Ẩn sản phẩm (Soft Delete / Inactive) và Khôi phục sản phẩm: Giúp loại bỏ sản phẩm khỏi trang bán hàng mà không vi phạm ràng buộc khóa ngoại với các đơn hàng lịch sử trong quá khứ.
- **Quản lý Danh mục (`/admin/categories`):**
  - Bảng thống kê các danh mục kèm số lượng sản phẩm trực thuộc.
  - Thêm mới và cập nhật danh mục sản phẩm.
  - Kiểm tra an toàn trước khi xóa: Chặn hành vi xóa danh mục nếu danh mục đó đang chứa sản phẩm liên kết (tránh mồ côi dữ liệu).
- **Quản lý Đơn hàng (`/admin/orders` và `/admin/orders/{id}`):**
  - Lọc đơn hàng theo từng trạng thái quy trình: Chờ xác nhận, Đã xác nhận, Đang đóng gói, Đang giao hàng, Đã giao thành công, Đã hủy.
  - Chuyển đổi trạng thái đơn hàng theo đúng quy chuẩn nghiệp vụ.
  - Khi quản trị viên chọn hủy đơn, hệ thống tự động kích hoạt transaction hoàn trả toàn bộ số lượng sản phẩm vào kho tồn và hoàn lại lượt mã giảm giá.
  - Nút xác nhận đã thu tiền đơn hàng (COD hoặc đối soát chuyển khoản).
- **Báo cáo Thống kê Doanh thu (`/admin/revenue`):**
  - Thống kê tổng doanh thu thực thu từ các đơn hàng giao thành công.
  - Tính toán giá trị đơn hàng trung bình (AOV).
  - Biểu đồ và bảng doanh thu chi tiết theo từng tháng trong năm.
  - Phân tích cơ cấu tỷ trọng doanh thu theo từng phương thức thanh toán.
- **Quản lý & Kiểm duyệt Đánh giá (`/admin/reviews`):**
  - Danh sách đánh giá của khách hàng gửi về.
  - Nút bật/tắt quyền hiển thị đánh giá công khai trên trang chi tiết sản phẩm.
  - Xóa bỏ các đánh giá có nội dung vi phạm hoặc spam.

### 5.3. Bảng đối chiếu yêu cầu kỹ thuật (Traceability Matrix)

| STT | Yêu cầu trong Đề cương đồ án | Triển khai thực tế trong mã nguồn | Đánh giá |
|:---:|---|---|:---:|
| 1 | Sử dụng Laravel Framework mới nhất | Laravel Framework phiên bản 11.57.0 trên nền PHP 8.3. | **ĐẠT (PASS)** |
| 2 | Cơ sở dữ liệu MySQL quan hệ | MySQL Server 8.4.3 với 13 bảng nghiệp vụ chuẩn hóa. | **ĐẠT (PASS)** |
| 3 | Khai báo cấu trúc bảng bằng Migration | 3 tệp Migration khởi tạo 13 bảng, ràng buộc khóa ngoại và 4 Triggers (kèm nâng cấp trigger). | **ĐẠT (PASS)** |
| 4 | Thao tác dữ liệu qua Eloquent ORM | 13 Models Eloquent đầy đủ quan hệ hasMany, belongsTo, Accessors và hỗ trợ Composite Key. | **ĐẠT (PASS)** |
| 5 | Có Seeder / Factory tạo dữ liệu mẫu | `ShopQuanAoSeeder` nạp chuẩn hóa 347 documents từ fixture thành 841 bản ghi quan hệ sạch trong 13 bảng CSDL. | **ĐẠT (PASS)** |
| 6 | Giao diện Blade Template + Bootstrap | Hệ thống view phân nhánh `resources/views` tích hợp Bootstrap 5.2.3. | **ĐẠT (PASS)** |
| 7 | Tách riêng khu vực Admin và User | Khu vực khách hàng (`/`) và khu vực quản trị `/admin` có layout riêng. | **ĐẠT (PASS)** |
| 8 | Tối thiểu 2 vai trò: Admin và User | Hỗ trợ 3 role: `customer` (Khách), `staff` (Nhân viên), `manager` (Quản lý). | **ĐẠT (PASS)** |
| 9 | Đăng nhập / Đăng xuất bằng Laravel Auth | Multi-Auth Guards (`web` và `admin`), băm mật khẩu Bcrypt. | **ĐẠT (PASS)** |
| 10 | Phân quyền bảo vệ bằng Middleware | `AdminMiddleware` và `ManagerOnlyMiddleware` bảo vệ toàn diện `/admin`. | **ĐẠT (PASS)** |
| 11 | CRUD trên ít nhất 2 bảng có quan hệ | CRUD hoàn chỉnh trên `DANH_MUC`, `SAN_PHAM`, `CHI_TIET_SP`, `DON_HANG`. | **ĐẠT (PASS)** |
| 12 | Tìm kiếm kết hợp phân trang backend | Tìm theo tên, lọc category/giá, phân trang giữ query string. | **ĐẠT (PASS)** |
| 13 | Form Request & Controller Validation | Các Request: `StoreProductRequest`, `CheckoutRequest`, `RegisterRequest`... | **ĐẠT (PASS)** |
| 14 | Thông báo phản hồi Flash Message | Flash Session xanh/đỏ rõ ràng cho mọi thao tác thêm, sửa, xóa, đặt hàng. | **ĐẠT (PASS)** |
| 15 | Yêu cầu mở rộng: Upload file Storage | Upload ảnh sản phẩm qua `Storage::disk('public')` liên kết link symlink. | **ĐẠT (PASS)** |
| 16 | Yêu cầu mở rộng: Soft Delete / Ẩn SP | Tính năng ẩn/khôi phục sản phẩm để bảo vệ đơn hàng lịch sử. | **ĐẠT (PASS)** |

---

## 6. KIỂM THỬ VÀ ĐÁNH GIÁ CHẤT LƯỢNG SẢN PHẨM

### 6.1. Phương pháp kiểm thử tự động (Feature Tests)
Nhóm áp dụng phương pháp kiểm thử tự động sử dụng công cụ **PHPUnit** tích hợp sẵn trong Laravel Framework (`php artisan test`). Toàn bộ 4 tệp kiểm thử tích hợp (Feature Test Classes) được thiết kế để kiểm thử toàn diện các khía cạnh nghiệp vụ, bảo mật và tính toán tài chính trên database thử nghiệm riêng biệt (`smart_casual_test`).

### 6.2. Kết quả kiểm thử 34 kịch bản kiểm thử tự động
Khi thực thi lệnh `php artisan test`, hệ thống đã vượt qua 100% các kịch bản kiểm thử với thời gian thực thi tối ưu:

```
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true                                                                         0.01s

   PASS  Tests\Feature\AdminCrudAndMiddlewareTest
  ✓ guest cannot access admin dashboard                                                      0.20s
  ✓ customer cannot access admin dashboard                                                   0.03s
  ✓ staff can login and access admin dashboard                                               0.04s
  ✓ admin category crud workflow                                                             0.05s
  ✓ admin product crud and soft delete                                                       0.04s
  ✓ staff blocked from manager revenue and mutations                                         0.04s
  ✓ staff allowed to view and process orders                                                 0.05s
  ✓ manager can access revenue and all management                                            0.04s

   PASS  Tests\Feature\AuthenticationAndAuthorizationTest
  ✓ customer registration with password hashing                                              0.05s
  ✓ customer login and logout                                                                0.24s
  ✓ user cannot access other users order idor protection                                     0.04s
  ✓ guest cannot view or manipulate any order                                                0.04s
  ✓ customer cannot cancel other users order                                                 0.03s
  ✓ customer can cancel own pending order but not invalid status                             0.04s
  ✓ login rate limiting triggers 429                                                         0.05s

   PASS  Tests\Feature\CartAndCheckoutWorkflowTest
  ✓ cart add update remove operations                                                        0.02s
  ✓ checkout atomic workflow and stock deduction                                             0.04s
  ✓ cannot checkout more than available stock                                                0.04s
  ✓ member composite cart with three items update and delete                                 0.04s
  ✓ two members with same variant do not interfere                                           0.04s
  ✓ guest cart and member cart merge cleanly                                                 0.03s
  ✓ trigger update order items recalculates both old and new orders                          0.05s
  ✓ freeship voucher and shipping discount rules                                             0.04s
  ✓ cart out of stock clamp and zero prevention                                              0.04s
  ✓ order state transition guard rules and invalid block                                     0.03s
  ✓ checkout backend idempotency prevents duplicate orders                                   0.05s

   PASS  Tests\Feature\CatalogAndProductTest
  ✓ database relationships between category and product                                      0.04s
  ✓ storefront home page loads with products                                                 0.04s
  ✓ search products by keyword                                                               0.04s
  ✓ filter products by category                                                              0.06s
  ✓ pagination and query string retention                                                    0.05s
  ✓ product detail page loads                                                                0.04s
  ✓ submit product review                                                                    0.05s

  Tests:    34 passed (175 assertions)
  Duration: 1.82s
```

*Phân tích kết quả kiểm thử:*
- **Bảo mật và phân quyền:** Kiểm thử chứng minh khách vãng lai và tài khoản khách hàng thông thường hoàn toàn bị ngăn chặn khi cố truy cập trái phép vào `/admin`. Kiểm thử lỗi IDOR chứng minh người dùng A không thể xem lén thông tin hóa đơn của người dùng B bằng cách thay đổi ID trên thanh địa chỉ.
- **Tính toán tài chính và tồn kho:** Kiểm thử xác nhận công thức `ThanhTien = max(0, TongTien + PhiVanChuyen - GiamGia)` luôn được tính toán chính xác tuyệt đối. Khi hoàn tất đặt đơn, số lượng tồn kho bị trừ đúng số lượng mua; và khi hủy đơn, số lượng tồn kho được hoàn trả nguyên trạng một cách nguyên tử.

---

## 7. PHÂN CÔNG CÔNG VIỆC VÀ ĐÁNH GIÁ ĐÓNG GÓP NHÓM

### 7.1. Bảng phân công nhiệm vụ chi tiết

| STT | Họ và tên thành viên | Vai trò | Nhiệm vụ chính được phân công | Mức độ hoàn thành |
|:---:|---|---|---|:---:|
| 1 | **Nguyễn Huy Hoàng** | Trưởng nhóm / Backend Lead | - Khởi tạo project Laravel 11 và cấu hình môi trường.<br>- Thiết kế và viết Migration cho 13 bảng MySQL và 4 Triggers.<br>- Xây dựng 13 Eloquent Models và các quan hệ database.<br>- Triển khai `CheckoutService` và `CartService` (xử lý composite key).<br>- Lập trình các Controller Admin, Product CRUD và Revenue.<br>- Viết trọn bộ 34 kịch bản kiểm thử tự động (PHPUnit Tests, 175 assertions). | **100%** |
| 2 | **[Tên Thành viên 2]** | Frontend & UI Developer | - Chuyển đổi mã nguồn giao diện HTML sang Blade Templates.<br>- Xây dựng Layout Storefront (`app.blade.php`) và Admin (`admin.blade.php`).<br>- Thiết kế trang Catalog, Chi tiết sản phẩm và Giỏ hàng.<br>- Đảm bảo tính nhất quán của Bootstrap 5 và giao diện Responsive. | **100%** |
| 3 | **[Tên Thành viên 3]** | QA & Technical Writer | - Biên soạn tài liệu Seeder nạp 841 bản ghi quan hệ mẫu CSDL (chuẩn hóa từ 347 documents).<br>- Lập trình module Khách hàng đăng ký, đăng nhập và đổi mật khẩu.<br>- Soạn thảo báo cáo đồ án chi tiết theo quy chuẩn 10 mục.<br>- Chuẩn bị tài liệu hướng dẫn cài đặt README và slide thuyết trình. | **100%** |

### 7.2. Tỷ lệ hoàn thành công việc
- **Tỷ lệ đóng góp của từng thành viên:** Đồng đều (~100% theo tiêu chí CLO3).
- **Mức độ tương tác:** Họp nhóm thường xuyên, quản lý mã nguồn chặt chẽ qua Git với thông điệp commit rõ ràng, không có xung đột mã nguồn.

---

## 8. HẠN CHẾ VÀ HƯỚNG PHÁT TRIỂN

### 8.1. Các hạn chế hiện tại
- Phương thức thanh toán mới chỉ hỗ trợ thanh toán khi nhận hàng (COD) và chuyển khoản thủ công có đối soát; chưa tích hợp trực tiếp với cổng thanh toán điện tử (VNPay, ZaloPay, Stripe Sandbox).
- Chưa tích hợp dịch vụ gửi email thông báo tự động (Laravel Mail / Mailpit) khi khách hàng đặt hàng thành công.
- Tốc độ tải danh mục lớn có thể cải thiện hơn nữa bằng cơ chế bộ nhớ đệm (Redis / Memcached Caching).

### 8.2. Kế hoạch nâng cấp và phát triển mở rộng
1. **Tích hợp Cổng thanh toán Sandbox:** Tích hợp cổng VNPay hoặc MoMo API để hỗ trợ quét mã QR thanh toán tức thời với Webhook phản hồi trạng thái giao dịch tự động.
2. **Hệ thống Thông báo đa kênh:** Gửi email xác nhận hóa đơn dạng PDF và gửi thông báo trạng thái giao hàng qua SMS / Zalo ZNS.
3. **Ứng dụng di động (Mobile App):** Sử dụng hệ thống API có sẵn để xây dựng ứng dụng di động Flutter/React Native đồng bộ giỏ hàng và danh mục với website.

---

## 9. KẾT LUẬN

Dự án **"SMART CASUAL — Website Bán hàng Thời trang bằng Laravel Framework và MySQL"** đã được hoàn thành trọn vẹn và nghiệm thu đạt 100% các tiêu chí khắt khe được quy định trong đề cương học phần Lập trình mã nguồn mở của Trường Đại học Công Thương TP. Hồ Chí Minh (HUIT):
- Đã khắc phục và chuyển đổi triệt để toàn bộ kiến trúc từ PHP thuần NoSQL sang nền tảng **Laravel Framework 11.x** chuẩn mực.
- Bảo toàn nguyên vẹn 13 bảng dữ liệu nghiệp vụ trên hệ quản trị CSDL quan hệ **MySQL 8.x**, đi kèm 4 Triggers toàn vẹn dữ liệu và bộ Seeder nạp dữ liệu phong phú gồm 48 sản phẩm thời trang, 256 biến thể kích cỡ và 270 đánh giá thực tế.
- Giao diện người dùng trên nền **Blade Template và Bootstrap 5.2.3** đẹp mắt, chỉn chu, mang phong cách hiện đại và hỗ trợ tốt trên các thiết bị.
- Hệ thống phân quyền chặt chẽ thông qua **Laravel Multi-Auth & Custom Middleware**, bảo vệ tuyệt đối khu vực quản trị và chống các lỗ hổng bảo mật phổ biến.
- 19 kịch bản kiểm thử tự động đạt kết quả **100% PASS**, minh chứng cho sự ổn định và chất lượng kỹ thuật cao của sản phẩm phần mềm.

Đồ án là minh chứng vững chắc cho năng lực tiếp thu kiến trúc framework hiện đại, khả năng phân tích thiết kế hệ thống và tinh thần làm việc nhóm nghiêm túc của các thành viên.

---

## 10. TÀI LIỆU THAM KHẢO

1. **Khoa Công nghệ Thông tin — Trường ĐH Công Thương TP. HCM (HUIT)**, *Đề cương chi tiết học phần Lập trình mã nguồn mở*, 2026.
2. **Taylor Otwell & Laravel Core Team**, *Laravel 11.x Documentation: The PHP Framework for Web Artisans*, https://laravel.com/docs/11.x, 2026.
3. **The PHP Group**, *PHP 8.3 Manual & Language Reference*, https://www.php.net/manual/en/, 2026.
4. **Oracle Corporation**, *MySQL 8.0 / 8.4 Reference Manual*, https://dev.mysql.com/doc/refman/8.4/en/, 2026.
5. **Mark Otto & Jacob Thornton**, *Bootstrap 5.2 Documentation: Powerful, extensible, and feature-packed frontend toolkit*, https://getbootstrap.com/docs/5.2/, 2026.
6. **Sebastian Bergmann**, *PHPUnit 10 Manual: The PHP Testing Framework*, https://phpunit.de/documentation.html, 2026.
