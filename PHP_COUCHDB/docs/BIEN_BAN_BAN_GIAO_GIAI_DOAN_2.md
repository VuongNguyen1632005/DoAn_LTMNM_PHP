# Biên bản bàn giao công việc Giai đoạn 2

**Dự án:** PHP CouchDB - Smart Casual  
**Ngày bàn giao:** 27/10/2026  
**Người bàn giao:** ........................................................  
**Người tiếp nhận:** ........................................................

## 1. Mục đích và kết luận

Biên bản ghi nhận trạng thái công việc bổ sung chức năng quản trị người dùng. Source hiện có phần triển khai chính trong working tree và các file PHP liên quan đã qua kiểm tra cú pháp. Chưa thể xác nhận chức năng chạy thành công trên CouchDB trong phiên này vì Docker API không truy cập được; chưa chạy smoke test hoặc kiểm thử giao diện.

Báo cáo Word được dùng làm tài liệu mô tả và **không chỉnh sửa**. Rubric PDF và prompt Giai đoạn 2 chưa đọc được qua công cụ tài liệu của phiên, nên đối chiếu rubric còn mở.

## 2. Phạm vi đã có trong source

Khu vực quản trị tại `/admin/users` giới hạn cho tài khoản staff có role manager. Source hiện có:

- Tìm kiếm và lọc tài khoản customer/staff theo loại, vai trò và trạng thái.
- Xem chi tiết tài khoản.
- Sửa tên và số điện thoại; không cho sửa username/email, role hoặc thông tin xác thực từ form này.
- Khóa/mở khóa tài khoản customer và staff; không cho manager tự khóa hoặc khóa tài khoản manager.
- Tạo tài khoản nhân viên mới với role cố định `staff`, kiểm tra đầu vào và lưu mật khẩu dạng hash.
- Bảo vệ POST bằng CSRF; kiểm tra quyền manager ở server; cập nhật CouchDB có xử lý xung đột revision HTTP 409.
- Đồng bộ trạng thái tài khoản vào session ở request tiếp theo; khi CouchDB không sẵn sàng, request đã đăng nhập trả 503.

Các thành phần chính: `public/index.php`, `src/Auth/AccountRepository.php`, `src/Auth/AccountService.php`, `src/Auth/UserAdministrationController.php`, `src/Auth/UserAdministrationService.php`, và `templates/admin/users/`.

## 3. Đối chiếu báo cáo và trạng thái xác minh

| Nội dung | Báo cáo / source | Trạng thái trong phiên |
|---|---|---|
| Fauxton | Chương 3 mô tả All Documents, JSON/Design Document, 3 query chạy trực tiếp, 9 Mango Index và `_explain`. | Không chạy lại; Docker API không truy cập được. |
| 12 Mango Query | Mục 3.4 ghi 12 request `_find` từng trả HTTP 200; source có 12 file JSON query. | Chưa xác minh lại trên CouchDB hiện tại. |
| 9 Mango Index | Mục 3.5 và source `catalog_indexes.json` có 9 định nghĩa index. Báo cáo nêu số liệu hiện có không phải so sánh trước/sau cùng selector. | Source đã đối chiếu; không tạo lại index. |
| MapReduce View | Báo cáo mô tả `order_statistics` với view đếm order và tổng giá trị theo trạng thái. | Chưa tìm thấy asset tương ứng trong source; cần xác nhận trên Design Document của CouchDB. |
| Database/demo | Chương 4 phân biệt database ứng dụng `shopquan_ao_sql_migration_test` và demo `shopquan_ao_phase06_demo_test`; số lượng tài liệu trong báo cáo là số liệu của dataset tại thời điểm viết. | Chưa xác nhận database hoặc số lượng hiện tại. |
| Kiểm thử Chương 5 | Báo cáo liệt kê kiểm thử trước đây, đồng thời ghi giao diện admin CRUD người dùng chưa đầy đủ. | Không lặp lại các kiểm thử cũ. Smoke test quản trị người dùng chưa chạy. |

**Kiểm tra đã chạy trong phiên:** `php -l` thành công cho 9 file PHP liên quan, gồm route, service, controller, script smoke và ba template.

**Kiểm tra chưa chạy:** `docker compose ps` không kết nối được Docker Engine qua named pipe `dockerDesktopLinuxEngine`. Vì vậy chưa có kết quả thực thi mới từ CouchDB hoặc trình duyệt.

## 4. Tài liệu và file liên quan

- Báo cáo tham chiếu: `BAOCAO_NOSQL-main.docx`. Chương 5 ghi thiếu giao diện quản trị người dùng đầy đủ; báo cáo không bị sửa.
- Tài liệu thang điểm: `DANH SACH DE TAI CUOI KY VA THANG DIEM_NOSQL.pdf`. Nội dung chưa trích xuất được; cần đối chiếu lại trước khi chốt mức đáp ứng rubric.
- Mô tả chức năng hiện tại: `PHP_COUCHDB/docs/USER_ADMINISTRATION.md`.
- Smoke script đã có, chưa chạy: `PHP_COUCHDB/scripts/smoke_user_administration.php`; Composer script là `users:admin-smoke`.
- README và các file PHP của chức năng đang có thay đổi chưa commit. Working tree còn nhiều thay đổi khác ngoài phạm vi PHP CouchDB; không stage hoặc commit toàn bộ working tree.

## 5. Việc tiếp theo

1. Khôi phục khả năng truy cập Docker Engine rồi xác nhận `COUCHDB_DATABASE` của app trước mọi thao tác. Không chạy test trên database demo hoặc database ứng dụng.
2. Khi Docker hoạt động, chạy smoke test chỉ trên database cô lập `user_admin_feature_test` theo hướng dẫn trong `docs/USER_ADMINISTRATION.md`; ghi lại kết quả thực tế.
3. Kiểm thử qua HTTP: manager được phép, staff/customer bị chặn, POST thiếu CSRF bị từ chối, cập nhật trạng thái thu hồi session ở request tiếp theo.
4. Trên database demo, chỉ đọc để xác nhận hiện trạng 12 query, 9 index và MapReduce View; không tạo lại nội dung đã tồn tại.
5. Đọc được PDF thang điểm và prompt Giai đoạn 2, rồi lập bảng đối chiếu tiêu chí với source và bằng chứng chạy. Không chỉnh sửa báo cáo Word nếu chưa có yêu cầu mới.

## 6. Trạng thái bàn giao

**Đã bàn giao:** source quản trị người dùng hiện có, tài liệu kỹ thuật, kiểm tra cú pháp PHP.  
**Chưa nghiệm thu:** kiểm thử cô lập trên CouchDB, kiểm thử HTTP/browser, xác nhận Design Document MapReduce và đối chiếu rubric.  
**Quyết định commit/push:** chưa thực hiện.

**Người bàn giao:** ____________________    **Người tiếp nhận:** ____________________

**Ngày:** ____ / ____ / ______
