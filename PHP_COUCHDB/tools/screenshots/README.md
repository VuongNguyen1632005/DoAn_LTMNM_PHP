# Bộ công cụ chụp minh chứng

Cài phụ thuộc riêng tại đây: `npm install --prefix tools/screenshots`; tải Chromium: `npx --prefix tools/screenshots playwright install chromium`. Chạy Node từ thư mục dự án. Chỉ dùng ảnh giao diện thật; không dựng ảnh terminal. Các screenshot dùng 1440×900, scale 2, en-US, zoom mặc định 100%.

Đọc `docs/figures/MANUAL_CAPTURE_GUIDE.md` trước khi thử các lệnh ghi. `common.cjs` đọc bí mật từ `.env`, không in bí mật. storageState và override nằm trong `.auth/`, bị ignore; phải xóa sau phiên làm việc. Database chính chỉ được đọc. Các script scratch từ chối tên đã tồn tại và tự xóa database của chúng.

- `mango.cjs`: nạp đúng 20 JSON truy vấn, chụp từng trang, lưu payload/response và API đối chứng.
- `views.cjs`: chụp View; giữ kết quả FAILED nếu hết ba lần thử. View khách hàng map-only đang có lỗi selector; không tự lặp thêm để che lỗi.
- `final-static.cjs`, `refold.cjs`: các trang database/index/document. Ảnh restore cần chuẩn bị chuỗi manual trước; `refold.cjs` gập metadata mà không sửa document.
- `revisions.cjs`, `validator-gui.cjs`: revision, conflict và lỗi validator trên scratch.
- `extra.cjs replication|conflict|attachment`, `lifecycle.cjs`: các chức năng scratch. Compaction hiện được gọi bằng API vì menu không có mục tương ứng; manifest ghi FAILED cho yêu cầu menu.
- `redact-config.cjs`: chụp cấu hình với các hàng password hash và secret được che ngay khi chụp. Không dùng screenshot cấu hình chưa che.
- `verify-scratch.cjs`: ánh xạ hai tên kiểm thử Verify Installation sang scratch; không giả lập response.
- `php-ui.cjs`, `customer.cjs`, `checkout.cjs`: chỉ chạy sau khi seeder tạo `retail_ui_capture_demo`, container app dùng database đó và `ui-accounts.php` tạo thông tin xác thực tạm. Không chạy trên database gốc. Sau khi xong khôi phục app bằng `docker compose up -d --no-deps app`, xóa database UI riêng.
- `manual.php`/`composer.json`: wrapper cho nhóm chụp terminal. Các thay đổi trong bộ nhớ chỉ mở rộng bộ lọc tên scratch hoặc chuyển nơi lưu evidence; validator và các ca kiểm thử giữ nguyên.
- `audit.cjs`: fingerprint sau/cleanup, so với fingerprint trước đã ghi. Không ghi đè fingerprint trước của một đợt chụp đang diễn ra.
- `deliver.cjs`: sinh manifest Markdown, bảng ánh xạ, anomalies và tóm tắt; loại PNG thăm dò không còn được manifest tham chiếu.
- `verify-delivery.cjs`: kiểm tra hash/kích thước PNG, payload gốc, đủ kết quả/ID GUI–API và scan bí mật trong tài liệu/log văn bản.

Mọi thao tác tạo/xóa đều phải chạy trong phiên có quyền truy cập Docker và localhost. Không commit `.env`, `.auth/` hay `node_modules/`. Hướng dẫn MANUAL ghi rõ T6 chưa chạy vì cần ngoại lệ tạo user tạm trong `_users`.
