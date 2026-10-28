# Backup, restore, import và export

## E1 — Replication backup

```powershell
docker compose exec -T app php scripts/backup_replication.php
```

Kết quả runtime: source `retail_order_delivery_demo` và target `retail_order_delivery_backup_scratch` đều có 150 documents; danh sách missing/extra/different đều rỗng; `_security` khớp. Do validator order là append-only, replication lọc riêng `_design/domain_validation`, phát lại các document còn lại, rồi chép nguyên design document validator và `_security` sang đích. Scratch target được xóa sau khi kiểm tra. JSON chi tiết ở `feature_evidence/backup_replication.json`.

## E1/E2 — Backup JSON, security và nội dung restore

Đã dùng một tên/mốc cho bằng chứng E: `phase3-evidence-20261028.json`.

```powershell
docker compose exec -T -e COUCHDB_DATABASE=retail_order_delivery_demo app composer db:backup -- phase3-evidence-20261028.json
docker compose exec -T -e COUCHDB_DATABASE=retail_order_delivery_demo app composer db:backup-verify -- phase3-evidence-20261028.json
docker compose exec -T -e COUCHDB_DATABASE=retail_order_delivery_demo app php scripts/verify_phase3_restore.php
```

Runtime đã xuất **150 documents**, **3 design documents** (`catalog_indexes`, `domain_validation`, `order_statistics`) và `_security`; file backup là **155365 bytes**. `restore-verify` so từng document bằng SHA-256 của JSON chuẩn hóa, loại `_rev` và các metadata revision-local; attachment metadata `revpos` cũng bị loại. Báo cáo đối chiếu missing/extra/different document IDs thành từng danh sách. Kết quả: source và restore đều **150/150**, cả ba danh sách rỗng, Mango indexes **16/16**, `_security` parity PASS, validator probe bị từ chối HTTP **403**. Target `retail_phase3_restore_test` bị xóa bởi wrapper sau kiểm tra. JSON output nằm ở `backup_restore_evidence.json`.

Backup JSON lưu toàn bộ design documents trong `documents`, và object `_security` ở trường `security`. `database_backup.php restore` áp dụng lại security object sau khi nhập document.

## E3 — CSV import/export

Sample: `data/samples/products_import.csv`. Chạy phép thử tích hợp có cleanup:

```powershell
docker compose exec -T -e COUCHDB_DATABASE=retail_csv_import_scratch app php scripts/verify_csv_exchange.php
```

Importer đọc `sku,name,category,price,stock`, tạo product schema v2 và gửi cả bốn dòng tới `_bulk_docs` trên DB có full `domain_validation`. Kết quả thật: **3 imported**, **1 row error** — dòng 5 (`CSV-BAD01`), `forbidden`, `product variant has invalid id, size, price, stock, or active flag`. Scratch DB bị xóa.

Lệnh export trực tiếp:

```powershell
docker compose exec -T app composer export:orders-csv -- orders_phase3_20261028.csv
```

File bằng chứng: `exports/orders_phase3_20261028.csv`; các cột là `id,date,customer,status,total,payment,delivery_status`. Query count dùng `_explain` chọn `admin_orders`, `_find` trả **65**, CSV có **65** data rows; danh sách ID CSV bằng danh sách ID query. `docs_examined=65`. Exporter từ chối ghi đè file có sẵn. Kết quả tích hợp được lưu tại `csv_exchange_evidence.json`.

## Số liệu runtime

| Hạng mục | Kết quả |
|---|---|
| Replication backup | 150/150 docs; hash và `_security` parity PASS |
| JSON backup | 150 docs; 3 design docs; `_security` có trong file |
| Restore content hashes | 150/150; missing=0, extra=0, different=0 |
| Restore Mango indexes | 16; source/target parity PASS |
| Import CSV | 3 accepted; 1 validator error ở dòng 5 |
| Export CSV | 65 rows = query count 65; `admin_orders`; docs_examined=65 |
