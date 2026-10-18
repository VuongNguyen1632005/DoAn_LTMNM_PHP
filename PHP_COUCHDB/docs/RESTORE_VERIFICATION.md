# Restore Verification

- Date: 2026-10-10
- Source database: `shopquan_ao_phase06_demo_test`
- Backup file: `var/backups/nosql-demo-20261010.json` (84.910 bytes)
- Target database: `nosql_report_restore_check_test` — trước khi restore trả HTTP 404; được giữ lại để chụp hình trong Fauxton.
- Backup document count: **60** (58 business, 2 design documents)
- Restored document count: **60**
- Restore command: `docker compose exec -T app composer db:restore -- nosql-demo-20261010.json nosql_report_restore_check_test`
- Restore exit code: **0**; `_bulk_docs` imported **60**, failed **0**.
- JSON validation: **PASS**
- Target creation: **PASS**
- Bulk import: **PASS** — từng kết quả có ID, `ok` và revision mới; design index được nhập riêng, business documents tiếp theo, validator được cài sau cùng vì rule của nó từ chối tracking lịch sử khi coi là document mới.
- Document count parity: **PASS** (60/60)
- Data parity: **PASS** — so sánh toàn bộ `_id` và nội dung JSON sau khi bỏ metadata revision; không thiếu hoặc dư document, giữ thứ tự mảng lồng.
- Design documents: **PASS** — `_design/catalog_indexes`, `_design/domain_validation`.
- Mango Query: **PASS** — order theo status/customer/tracking trả lần lượt 5/5/1 kết quả và tìm đúng ID trong backup; `_explain` chọn đúng index tương ứng.
- Indexes: **PASS** — 9 JSON indexes và primary `_all_docs`; tên, định nghĩa và fields nguồn/đích khớp.
- Validator: **PASS** — thử tạo order có status sai trên database đích bị từ chối HTTP 403; document thử không được tạo.
- Sample IDs: `product:P06-001`, `order:P06-0001`, `order:P06-0014` có tracking; kiểm tra type, schema version và các field lồng quan trọng, không in dữ liệu cá nhân.
- Source unchanged: **PASS** — `doc_count` 60 và `update_seq` giống nhau trước/sau restore; nội dung nguồn vẫn khớp backup.
- Target safety/negative tests: **PASS** — file thiếu, JSON sai, documents rỗng, `_id` trùng, count sai đều bị từ chối; chạy lại vào target đã có không ghi đè; lỗi từng document trong `_bulk_docs` không được coi là thành công.
- Local services: Docker app và CouchDB đều healthy; `/health/ready` trả HTTP 200.

Overall: **PASS**. BACKUP/RESTORE ROUND-TRIP: **PASS** trong phạm vi database demo local và logical backup.

## Ảnh cho báo cáo

1. **File backup:** chụp thông tin tệp `var/backups/nosql-demo-20261010.json` (tên và dung lượng), không mở nội dung account/hash.
2. **Terminal restore:** lệnh gốc đã chạy thành công và target cố định hiện tồn tại. Nếu cần ảnh terminal mới, chọn tên đích `_test` khác chưa tồn tại, ví dụ `$photoTarget = "nosql_report_restore_photo_$(Get-Date -Format yyyyMMddHHmmss)_test"`; chạy `docker compose exec -T app composer db:restore -- nosql-demo-20261010.json $photoTarget`, rồi `docker compose exec -T app composer db:restore-verify -- nosql-demo-20261010.json $photoTarget`. Không chạy lại vào target đã có.
3. **Fauxton database và All Documents:** mở `http://127.0.0.1:5984/_utils/#/database/nosql_report_restore_check_test/_all_docs`; chụp database và danh sách 60 document.
4. **Order và design document:** mở `order:P06-0014` để thấy `delivery_tracking`; mở `_design/domain_validation` để thấy validator. Chụp riêng hai màn hình, không chụp account/hash.
5. **Mango Query:** trong database restore mở Mango Query, dán nội dung `database/couchdb/demo_queries/02_shipping_orders.json`, chạy và chụp query cùng kết quả.

## Limits

Restore phục hồi nội dung document, còn CouchDB tạo revision mới ở database đích. Đây không phải phục hồi physical database: chưa chứng minh giữ full revision tree, deleted history, cấu hình server, `_security` object, replication jobs, Docker volume, ảnh/file upload trên filesystem hoặc trạng thái cluster. `_bulk_docs` không phải transaction toàn database; nếu lỗi sau khi tạo đích, database test có thể chứa một phần dữ liệu và script báo thất bại thay vì tự xóa.
