# Kiểm thử HTTP trang quản trị người dùng và backup

## Cách chạy

```powershell
$db = "retail_admin_http_$([guid]::NewGuid().ToString('N').Substring(0,8))_test"
docker compose exec -T -e COUCHDB_DATABASE=$db app php tests/admin_users_integration.php
```

Script tự tạo database scratch, cài validator đầy đủ và index username, khởi chạy PHP built-in server riêng, gọi các route bằng HTTP thật với cookie session/CSRF, sau đó dừng server, xóa file backup vừa sinh và xóa database scratch.

## Kết quả runtime

Lần chạy ngày 2026-10-28 trên CouchDB 3.5: **25/25 PASS**, database `retail_admin_http_39f25e78_test` đã được xóa sau kiểm thử. Số liệu từng ca và tên database được lưu trong [`admin_users_http_evidence.json`](admin_users_http_evidence.json).

| Nhóm | Ca kiểm tra | Kết quả |
|---|---|---|
| F1 | Manager đăng nhập HTTP | PASS |
| F1 | Tìm kiếm và lọc theo loại tài khoản | PASS |
| F1 | Xem chi tiết, không lộ auth/password hash | PASS |
| F1 | Sửa hồ sơ với `_rev` hiện tại và xác minh lưu | PASS |
| F1 | Gửi `_rev` cũ: HTTP 409, thông báo xung đột, dữ liệu mới không bị ghi đè | PASS |
| F1 | Khóa và mở khóa tài khoản | PASS |
| F1 | Thiếu CSRF không làm thay đổi trạng thái | PASS |
| F1 | Tạo nhân viên mới với vai trò Staff | PASS |
| F1 | Staff và customer bị từ chối tại `/admin/users` | PASS |
| F2 | Manager tạo backup qua HTTP có CSRF, tải xuống JSON | PASS |
| F2 | Thiếu CSRF khi tạo backup bị HTTP 403 | PASS |
| F2 | Staff/customer bị từ chối xem, tạo và tải backup | PASS |
| F2 | Tên file kiểu traversal bị từ chối; file mới có tên duy nhất | PASS |

Mã nguồn kiểm thử: [`tests/admin_users_integration.php`](../../tests/admin_users_integration.php). Trang backup và user admin gọi lại `ManagerGuard` trên tài khoản đang hoạt động; endpoint backup chỉ chấp nhận database có hậu tố `_test` hoặc `_demo`. Script backup dùng cơ chế publish không ghi đè file tồn tại.

## Lỗi được phát hiện và sửa

Khi cập nhật hồ sơ, form trước đó không gửi `_rev` và service không so revision đã xem với revision hiện tại. Sửa hồ sơ vì vậy chưa phát hiện được người khác vừa cập nhật trước đó. Đã thêm `_rev` ẩn vào form, so sánh revision trong service, ánh xạ CouchDB HTTP 409 sang lỗi riêng và trả HTTP 409 cùng thông báo yêu cầu tải/kiểm tra dữ liệu mới. Ca HTTP stale revision xác nhận giá trị cập nhật mới vẫn còn nguyên.

## Giới hạn bằng chứng

Các payload lịch sử Fauxton của query 01 và 06 người dùng chưa lấy được. Việc đó không cản trở kiểm thử HTTP quản trị; sai lệch lịch sử hai query vẫn được ghi riêng là chưa xác minh, không suy diễn từ runtime mới.
