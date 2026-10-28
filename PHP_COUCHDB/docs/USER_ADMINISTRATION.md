# Quản trị người dùng

## Phạm vi triển khai

Khu vực /admin/users chỉ dành cho tài khoản staff có vai trò manager. Manager có thể tìm kiếm theo tên, username/email hoặc mã; lọc theo loại tài khoản, vai trò và trạng thái; xem chi tiết; khóa/mở khóa tài khoản; tạo tài khoản nhân viên; và sửa tên/số điện thoại của customer hoặc staff.

Document tiếp tục dùng schema hiện tại: type, active, role, profile, auth, meta và _rev. Không có xóa cứng. Tài khoản staff mới có schema_version=2, role=staff, active=true, username duy nhất và password_hash được tạo bằng password_hash(). Biểu mẫu hồ sơ chỉ cập nhật profile.name/profile.phone; giữ nguyên username, email, role, auth, địa chỉ và các trường còn lại.

## Kiểm soát bảo mật

- Mọi route quản trị kiểm tra session và quyền manager ở server; các POST yêu cầu CSRF hợp lệ.
- Username được chuẩn hóa chữ thường và kiểm tra trùng với cả customer lẫn staff qua Mango index account_usernames. Nếu hai yêu cầu tạo cùng username đồng thời, ID ổn định cùng kiểm tra xung đột CouchDB chặn ghi trùng.
- Chỉ mật khẩu ban đầu của staff mới được nhập ở biểu mẫu tạo; yêu cầu tối thiểu 12 ký tự gồm chữ hoa, chữ thường, số và ký tự đặc biệt. Mật khẩu được hash, không giữ lại trong flash/session, không ghi log hoặc hiển thị.
- Form sửa không nhận các trường _id, _rev, auth, password hash, username hoặc role. Service chỉ cập nhật name/phone và metadata audit.
- Ghi CouchDB dùng _rev; HTTP 409 được báo để tải lại trang, không ghi đè bản mới hơn.
- Không cho manager tự khóa. Không cho khóa bất kỳ tài khoản manager nào để giữ ít nhất một manager hợp lệ cả khi có thao tác đồng thời. Manager bị khóa sẵn vẫn có thể được mở khóa.
- Session trình duyệt đọc trạng thái tài khoản hiện tại trước các request đã đăng nhập; tài khoản bị khóa sẽ bị đăng xuất ở request kế tiếp. JWT hiện kiểm tra trạng thái tài khoản live trên mỗi request. Nếu CouchDB không khả dụng, request session đã đăng nhập trả 503.
- Không thay đổi role hoặc username qua giao diện quản trị. Staff mới luôn có role staff.

## Smoke test cô lập

Script chỉ chấp nhận database tên user_admin_feature_*_test, tạo database/index nếu thiếu, thêm fixture ngẫu nhiên và xóa đúng fixture do lần chạy đó tạo. Không reset hoặc ghi vào migration database hay database demo.

    docker compose exec -T -e COUCHDB_DATABASE=user_admin_feature_test app composer users:admin-smoke

Script kiểm tra tạo staff mặc định role=staff, username trùng, xác thực sau tạo, lọc và projection không lộ credential, sửa hồ sơ nhưng giữ username/hash/role, khóa/mở khóa, chặn tự khóa và manager bị khóa, cùng xung đột _rev.

Kiểm tra quyền/CSRF qua HTTP, thu hồi session trình duyệt, phản hồi khi CouchDB mất kết nối và ảnh giao diện cần được xác minh trên app trỏ tới database thử riêng trước khi cập nhật Word/slide hoặc trình diễn.

## Kịch bản demo

1. Đăng nhập manager, mở Người dùng và lọc theo customer/staff/manager.
2. Tạo tài khoản nhân viên; xác nhận vai trò là Staff, không phải Manager. Thử username trùng để xem thông báo.
3. Mở chi tiết, chỉnh tên/số điện thoại; xác nhận username và vai trò không đổi.
4. Khóa một customer trong database thử. Đăng nhập mới bị từ chối; session đã mở bị thu hồi ở request tiếp theo.
5. Mở khóa rồi thử đăng nhập lại.
6. Dùng session staff/customer gọi trực tiếp route quản trị và thử POST thiếu CSRF; server phải từ chối.
