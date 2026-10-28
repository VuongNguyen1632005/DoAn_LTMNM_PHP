# Phase 1 – Inventory

Ngày khảo sát: 2026-10-28 (Asia/Saigon)

## Repository snapshot

| Hạng mục | Quan sát |
|---|---|
| README | `README.md`; mô tả ứng dụng PHP 8.3/Apache, CouchDB 3.5.0, seed fixture `_test` và demo Phase 6 riêng. |
| Compose | `docker-compose.yml`; services `app` và `couchdb`, CouchDB bind `127.0.0.1:5984`, app bind `127.0.0.1:8081`. |
| Environment template | `.env.example`; có cấu hình `COUCHDB_DATABASE=retail_order_delivery_test`; không có `DEMO_DATABASE`. `.env` tồn tại cục bộ, không đọc/ghi giá trị bí mật vào báo cáo. |
| Composer | `composer.json`; có `demo:seed`, `demo:verify`, `db:backup`, `db:restore`, verify commands; chưa có `db:seed-demo`. |
| Seeders | `scripts/import_seed.php`, `scripts/seed_phase06_demo.php`; importer giới hạn `_test`; demo seeder dùng `PHASE06_DEMO_DATABASE`, mặc định `shopquan_ao_phase06_demo_test`, có create-only và reset guard riêng. |
| Backup/restore | `scripts/database_backup.php`; cả source và target đều bị giới hạn hậu tố `_test`. |
| Indexes | `database/couchdb/indexes/catalog_indexes.json`; 9 Mango index, gồm `admin_orders`, `admin_orders_by_date`, `delivery_tracking_code`; chưa có index status delivery. |
| Validator | `database/couchdb/design-docs/domain_validation.json`; schema_version 2, tám type nghiệp vụ và các rule order/tracking/history. |
| Order statistics | Không tìm thấy file design document `order_statistics` trong `database/couchdb/design-docs`; `rg` chỉ tìm thấy nhắc tới ở tài liệu/bằng chứng các giai đoạn trước. |
| Queries | `database/couchdb/demo_queries/01..12_*.json`; query 01, 02, 06 và 12 đã ghim `use_index`; query 01/02 sort `ordered_at desc`; query 06 có range ISO date, `limit: 50`, `execution_stats: true`. Query 09–11 chưa ghim index. |

## Seed fixture counts

Đếm trực tiếp từ `database/couchdb/seeds/retail_order_delivery.json` bằng PHP CLI 8.3.30:

| Type | Documents |
|---|---:|
| customer | 3 |
| staff | 2 |
| product | 48 |
| cart | 3 |
| order | 13 |
| voucher | 5 |
| shipping_method | 2 |
| review | 270 |
| design document (`_design/domain_validation`) | 1 |
| **Tổng** | **347** |

Tất cả 48 product trong fixture có ít nhất 4 variant. Đây là số trong file fixture, không phải số đã xác minh trên bất kỳ database CouchDB nào.

## Runtime / database inventory

Lần chạy đầu bằng sandbox không có quyền mở Docker named pipe. Sau khi xin quyền Docker runtime, `docker compose up --build -d` thành công; CouchDB 3.5.0 healthy và `/health/ready` trả HTTP 200 từ bên trong app container.

Đếm bằng `GET /_all_dbs`, sau đó `GET /{db}/_all_docs?include_docs=true&limit=10000`; `total_rows` bằng `returned_rows` ở mọi database:

| Database | Tổng docs | Design docs | Business docs theo type |
|---|---:|---:|---|
| `nosql_report_restore_check_test` | 60 | 2 | cart 1, customer 6, order 27, product 19, shipping_method 2, staff 2, voucher 1 |
| `nosql_report_restore_photo_20261013223526_test` | 60 | 2 | cart 1, customer 6, order 27, product 19, shipping_method 2, staff 2, voucher 1 |
| `phase03_validator_index_backup_test` | 4 | 2 | order 2 |
| `retail_order_delivery_test` | 348 | 2 | cart 3, customer 3, order 13, product 48, review 270, shipping_method 2, staff 2, voucher 5 |
| `revision_screenshot_test` | 1 | 0 | product 1 |
| `shopquan_ao_phase06_demo_test` | 61 | 3 | cart 1, customer 6, order 27, product 19, shipping_method 2, staff 2, voucher 1 |
| `shopquan_ao_sql_migration_test` | 354 | 2 | cart 5, customer 5, order 14, product 48, review 270, shipping_method 2, staff 3, voucher 5 |

Các counts này là trạng thái lúc kiểm kê 2026-10-28; không tạo, sửa hoặc xóa database nào trong bước này.

Sau inventory, trong các bước Phase 1 đã tạo thêm hai database tên mới theo yêu cầu kiểm thử:

| Database | Tổng docs | Design docs | Business docs theo type | Mục đích |
|---|---:|---:|---|---|
| `retail_order_delivery_demo` | 150 | 3 | cart 8, customer 15, order 65, product 30, review 20, shipping_method 3, staff 3, voucher 3 | Dataset demo Phase 1 |
| `retail_restore_check_test` | 150 | 3 | cart 8, customer 15, order 65, product 30, review 20, shipping_method 3, staff 3, voucher 3 | Target restore mới; được giữ lại để Fauxton/review |
| `user_admin_feature_phase1_test` | 1 | 1 | không có business document | Tạo bởi `users:admin-smoke`; test để lại design doc |

Không ghi hoặc xóa database có sẵn nào khác ngoài target demo mới; `user_admin_feature_phase1_test` là target riêng do smoke suite tạo trong bước kiểm thử.

Metadata Git nằm ở thư mục cha `D:\PHP_NOSQL\.git`, ngoài writable root; Git chỉ hoạt động khi dùng quyền elevated. Working tree hiện có nhiều thay đổi không thuộc Phase 1; sẽ không gom chúng vào commit.

## Chưa xác minh

- Database hiện có trên CouchDB và số document theo `type` (runtime chưa lên).
- Nguyên nhân gốc 27/21 của query 06; payload thực tế và database Fauxton đang dùng chưa thu được. File query hiện hành đã đặt `limit: 50`, cao hơn CouchDB mặc định 25, nên riêng mặc định 25 không giải thích được query file hiện tại trả thiếu 6 kết quả.
- Nguyên nhân query 01 fallback; cần `_explain` thực tế của đúng payload Fauxton và cùng database.
- Số liệu explain/execution_stats trước/sau delivery index.

Tài liệu lịch sử xác nhận giới hạn của chứng cứ: `docs/PHASE_07_NOSQL_FAUXTON_REPORT.md` ghi query 06 API trả 27 và Fauxton GUI chưa xác minh; cùng tài liệu ghi `_explain` của query 01 chọn `admin_orders`. Đây không phải bằng chứng cho payload/database đang báo fallback hiện nay.

Không suy diễn các mục này từ fixture hay tài liệu cũ.
