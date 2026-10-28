# BIÊN BẢN BÀN GIAO PHASE 3: VALIDATOR, MANGO INDEX, BACKUP/RESTORE

**Ngày kiểm tra:** 28/10/2026 (Asia/Saigon)  
**Dự án:** `D:\PHP_NOSQL\PHP_COUCHDB`  
**Trạng thái tổng thể:** **CHƯA HOÀN TẤT** — đối chiếu tập `_id` Backup/Restore đã hoàn thành ở chế độ chỉ đọc. Kiểm thử Validator HTTP 403 đang **BLOCKED**. Kết quả Mango Index PASS được giữ nguyên từ checkpoint trước và không chạy lại.

## 1. Tóm tắt kết quả

| Hạng mục | Trạng thái | Kết quả |
|---|---|---|
| Môi trường Docker/CouchDB | PASS | PowerShell của người dùng xác nhận context `desktop-linux`, container PHP và CouchDB healthy; preflight CouchDB trả HTTP 200. Trong lượt tiếp tục này không chạy Docker CLI. |
| Đọc validator source | PASS | Đã đọc đầy đủ `database/couchdb/design-docs/domain_validation.json`. |
| Validator HTTP 403 | BLOCKED | Chưa có bằng chứng HTTP 403. `GET /_session` trả HTTP 200 cho user `webthoitrang_dev`, role `_admin`; nhưng GET design document trong database test trả HTTP 401 và CouchDB báo không được phép truy cập database. Không thay đổi credential, ACL hoặc cấu hình bảo mật. |
| Mango Index | PASS (bằng chứng GUI checkpoint trước) | Giữ nguyên kết quả checkpoint, không kiểm thử lại theo yêu cầu. Dataset nhỏ nên số liệu thời gian chỉ có tính minh họa, không phải benchmark. |
| Đối chiếu Backup/Restore theo `_id` | PASS (chỉ đọc) | So sánh seed, ba file backup, database demo hiện tại và hai database restore test. Các sai khác cụ thể ở mục 3. |
| Ghi vào database ứng dụng/demo | KHÔNG | Không có thao tác ghi. Demo và hai database restore chỉ được đọc. Database ứng dụng không được truy vấn trong lượt tiếp tục này. |
| Sửa Word / Git | KHÔNG THỰC HIỆN | Không sửa báo cáo Word, không commit, push hoặc deploy. |

## 2. Validator: kết quả và giới hạn

Validator đầy đủ trong `database/couchdb/design-docs/domain_validation.json` kiểm tra loại document được hỗ trợ và `schema_version: 2`. Với Order, validator kiểm tra status, receiver, totals, status history, quy tắc payment history, bảo vệ đơn đã thanh toán và các trường item. Validator cũng kiểm tra delivery tracking, lịch sử/trạng thái giao hàng và các quy tắc riêng cho customer, staff, product, cart, voucher, shipping method và review.

Theo checkpoint trước, validator được cài qua Fauxton trong `phase03_validator_index_backup_test` là **bản rút gọn**, chỉ chứa nhánh kiểm tra status, receiver và items không rỗng của Order. Fauxton đã chấp nhận Order `pending`; khi chỉ đổi status sang `invalid_status`, giao diện hiện thông báo rule và document vẫn giữ status `pending`, revision không đổi. Fauxton không hiển thị mã HTTP. Order đó chưa được chứng minh là hợp lệ theo validator đầy đủ từ source.

Trong lượt này, các phản hồi HTTP được ghi nhận như sau:

| Request | HTTP | Kết quả |
|---|---:|---|
| `GET http://127.0.0.1:5984/_session` | 200 | CouchDB trả userCtx `webthoitrang_dev`, role `_admin`. |
| `GET http://127.0.0.1:5984/phase03_validator_index_backup_test/_design%2Fdomain_validation` | 401 | Body: `{"error":"unauthorized","reason":"You are not authorized to access this db."}`. |
| Thử qua hostname nội bộ Docker lấy từ `.env` | 502 | Body: `upstream failure`; đây là lỗi định tuyến/transport, không dùng làm phản hồi CouchDB. |

Phiên đã xác thực ở endpoint `_session`, nhưng endpoint database trả HTTP 401. Nguyên nhân cấu hình/chính sách cụ thể chưa được xác định. Không thay credential, quyền database hay cấu hình bảo mật. Vì không đọc được design document, chưa thể đối chiếu trực tiếp bản đang cài với source; cũng không gửi yêu cầu cài validator đầy đủ hoặc tạo/cập nhật Order trong lượt này. Do đó **HTTP 403 BLOCKED / CHƯA ĐƯỢC CHỨNG MINH**.

## 3. Đối chiếu Backup/Restore theo tập `_id`

Tập seed được dựng theo các ID xác định trong `scripts/seed_phase06_demo.php`: 18 product, 6 customer, 2 staff, 2 shipping method, 1 voucher, 27 order và 2 design document, tổng cộng **58 ID**. Fixture JSON 347 document là nguồn dữ liệu đầu vào rộng hơn, không phải tập 58 document sau khi seeder tạo dữ liệu demo.

Ba file backup dưới đây đều có 60 document và cùng tập `_id`:

- `var/backups/nosql-demo-20261010.json`
- `var/backups/nosql-demo-20261013.json`
- `var/backups/nosql-demo-20261013-2.json`

| So sánh | Chỉ có ở vế trái | Chỉ có ở vế phải | Kết quả |
|---|---|---|---|
| Seed (58) -> Backup (60) | Không có | `cart:P06-001` (cart); `product:P1B7B31E107F1` (product) | Backup chứa đủ ID seed và thêm hai ID trên. Không suy đoán nguyên nhân. |
| Backup (60) -> Demo hiện tại (61) | Không có | `_design/order_statistics` (design) | Demo hiện tại chứa đủ ID backup và thêm design document này. |
| Backup (60) -> `nosql_report_restore_check_test` (60) | Không có | Không có | Tập `_id` khớp chính xác. |
| Backup (60) -> `nosql_report_restore_photo_20261013223526_test` (60) | Không có | Không có | Tập `_id` khớp chính xác. |

Các database live được đọc bằng `GET /<database>/_all_docs?include_docs=true&limit=1000`: demo HTTP 200, 61 row; mỗi restore test HTTP 200, 60 row. Manifest đã lưu chỉ giữ `_id` và `type`, không ghi thông tin cá nhân vào bằng chứng. Không chạy thao tác restore. Kết quả xác nhận đối chiếu tập ID hiện tại, không khẳng định nội dung từng document giống từng byte hoặc trạng thái demo tại một thời điểm lịch sử trước đó.

## 4. Tệp bằng chứng

- `docs/phase03_validation_index_backup/evidence/validator-auth-http.json` — HTTP status/body, user context và phân biệt lỗi transport.
- `docs/phase03_validation_index_backup/evidence/backup_restore_id_diff.csv` — bảng ID chênh lệch, loại document và chiều so sánh.
- `docs/phase03_validation_index_backup/evidence/backup_restore_id_reconciliation.json` — số lượng, nguồn, database live và các sai khác.
- `docs/phase03_validation_index_backup/evidence/phase03-live-results.json` — trạng thái Phase 3, gồm kết quả Mango Index từ checkpoint trước.
- `docs/phase03_validation_index_backup/reconcile_backup_ids.ps1` — script dựng tập ID seed và lặp lại đối chiếu chỉ đọc; gửi GET, không in credential.

## 5. Gợi ý cập nhật báo cáo Word (chưa thực hiện)

1. Ghi kiểm thử Validator là BLOCKED: lần thử Fauxton trước chỉ dùng validator rút gọn, giao diện báo lỗi nhưng chưa xác nhận HTTP 403 và chưa xác minh validator đầy đủ đang cài.
2. Thay phần Backup/Restore chỉ nêu số lượng bằng bảng chênh lệch `_id` ở mục 3. Không tự kết luận nguyên nhân xuất hiện cart, product hoặc design document bổ sung.
3. Có thể nêu hai restore test hiện tại có cùng tập 60 ID với backup lịch sử. Không tuyên bố đã chạy một lần restore mới hoặc đã so sánh toàn bộ nội dung document.
4. Giữ kết quả Mango Index từ checkpoint trước; lượt này không chạy lại.

**Nghiệm thu:** Đối chiếu tập ID Backup/Restore: **PASS (read-only)**. Validator HTTP 403: **BLOCKED / NOT PROVEN**. Phase 3 **chưa hoàn tất**.
