# Sai khác và giới hạn quan sát

- **C2_customer – FAILED:** GUI thất bại sau ba lần: selector Reduce không tồn tại ở View map-only; dừng mục này theo quy tắc prompt. API reduce=false/include_docs=true/startkey/endkey trả đủ 50, lưu logs/C2_customer_api.json.
- **C3_compaction_menu – FAILED:** Menu phiên bản đang chạy chỉ có Replicate Database, Delete và Add New; không tìm thấy Compact & Cleanup. Đã thử mở menu/kiểm tra điều khiển; compaction đo kích thước bằng API trên scratch.
- **C3_compaction_tasks – MISMATCH:** Database nhỏ hoàn thành nhanh; ảnh giữ trạng thái thật, không tạo tác vụ giả.
- **F3.7 – MISMATCH:** File Query 01 có limit=10. Đã tải hai trang 10/1, tổng 11 khớp API; ảnh hiện trang cuối có 1 kết quả. Không sửa limit để dựng ảnh 11 kết quả.
- **F3.8 – MISMATCH:** Ba trang thật 25/25/15, tổng 65 khớp API. Fauxton hiển thị số của từng trang, không có Show more hoặc tổng gộp 65 trên cùng ảnh. Dùng kèm ba ảnh trang.

## Mango và Execution Statistics

| Query | Các trang GUI | Tổng GUI / API | Documents examined theo trang | Cảnh báo |
|---|---|---|---|---|
| 01 | 10/1 | 11 / 11 | 11/1 | Không |
| 02 | 15 | 15 / 15 | 15 | Không |
| 03 | 10 | 10 / 10 | 10 | Không |
| 04 | 7 | 7 / 7 | 7 | Không |
| 05 | 4 | 4 / 4 | 4 | Không |
| 06 | 25/25/15 | 65 / 65 | 26/26/15 | Không |
| 07 | 8 | 8 / 8 | 11 | Không |
| 08 | 15 | 15 / 15 | 15 | Không |
| 09 | 5 | 5 / 5 | 5 | Không |
| 10 | 2 | 2 / 2 | 2 | Không |
| 11 | 3 | 3 / 3 | 3 | Không |
| 12 | 1 | 1 / 1 | 1 | Không |
| 13 | 1 | 1 / 1 | 4 | Không |
| 14 | 37 | 37 / 37 | 0 | Không |
| 15 | 48 | 48 / 48 | 65 | Không |
| 16 | 1 | 1 / 1 | 2 | Không |
| 17 | 15 | 15 / 15 | 65 | Không |
| 18 | 5 | 5 / 5 | 30 | Không |
| 19 | 10/10/10/10/0 | 40 / 40 | 24/16/14/14/0 | Không |
| 20 | 15 | 15 / 15 | 15 | Không |

Query 14 thực tế examined=0; Query 15/17/19 có examined vượt số 65 đơn vì có thể quét document thuộc loại khác. Giữ nguyên dữ liệu và số đo. Query 01 không có cảnh báo fallback trong lần chạy được chụp.

Fauxton mặc định 20 dòng/trang tự thu nhỏ request thành limit=21. Bộ ảnh cuối chọn 100 dòng/trang trước khi nạp truy vấn, giữ nguyên limit trong file. Pagination được thực hiện bằng bookmark, giữ nguyên selector/use_index/sort/fields/limit; thêm skip=0 là hành vi của Fauxton. Payload và response cuối lưu theo số truy vấn.

Verify Installation ban đầu bị chặn vì dùng verifytestdb không kết thúc bằng _scratch. Lần cuối ánh xạ tên kiểm tra sang hai database *_scratch, nhận đủ sáu success từ CouchDB thật; đã xóa cả hai.

Replication đưa business document vào trước, cài validator cuối để tránh validator chuyển trạng thái từ chối lịch sử đã tồn tại. Đích cuối có 150 document, so sánh nội dung không tính _rev đạt PASS.

Compaction thực tế giảm file từ 426336 xuống 37222 byte (logs/compaction_before.json và compaction_after.json); thao tác được gọi qua API vì menu GUI không có Compact & Cleanup. Không có ảnh giả cho mục menu hoặc tác vụ đang chạy.

T6 chưa chạy vì phải tạo tài khoản trong _users. Không tạo tài khoản hay sửa cấu hình xác thực để vượt ràng buộc scratch-only.
