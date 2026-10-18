# Backup Verification

- Ngày kiểm thử: 2026-10-10
- Trạng thái: **BACKUP VERIFIED**
- Database nguồn: `shopquan_ao_phase06_demo_test`
- Command đã chạy: `docker compose exec -T -e COUCHDB_DATABASE=shopquan_ao_phase06_demo_test app composer db:backup -- nosql-demo-20261010.json`
- File backup: `var/backups/nosql-demo-20261010.json`
- File size: **84.910 bytes**
- `doc_count` đọc trực tiếp từ CouchDB: **60**
- `document_count` và số phần tử `documents` trong file: **60**
- JSON parse: **PASS**
- Document count parity: **PASS**
- Design document included: **YES** (`_design/domain_validation`)
- Backup command result: **PASS**, exit code 0
- Targeted test: **PASS** (`composer db:backup-smoke`), gồm database thiếu/không kết nối được, credentials sai, tên file không hợp lệ, file đã tồn tại, trang API lỗi hoặc thiếu dữ liệu, và phân trang 501/1.000/1.001 document giả lập.
- Kiểm tra độc lập: `docker compose exec -T -e COUCHDB_DATABASE=shopquan_ao_phase06_demo_test app composer db:backup-verify -- nosql-demo-20261010.json` → **PASS**.
- Database nguồn: lệnh backup so sánh `doc_count` và `update_seq` trước/sau quá trình đọc; smoke test xác nhận hai giá trị này không đổi.
- Git: `.gitignore` đã loại trừ `var/backups/`; không commit hoặc chia sẻ file backup vì nó có thể chứa dữ liệu cá nhân và password hash.

## Giới hạn

Đây là logical export các document hiện còn trong một database CouchDB, không phải physical backup. File giữ nội dung document và `_id`, có thể gồm `_rev` hiện tại cùng design document; điều đó không chứng minh giữ toàn bộ revision tree, deleted history, server configuration, security object hoặc replication configuration. Ảnh/file upload trong filesystem hay Docker volume nằm ngoài phạm vi backup này. Quy trình phân trang không tạo snapshot nguyên tử nếu có ứng dụng khác ghi đồng thời; script phát hiện thay đổi `doc_count` hoặc `update_seq` trong thời gian đọc và từ chối xuất file. Chưa chạy restore trong lần kiểm thử này, nên không có kết luận về khả năng phục hồi.
