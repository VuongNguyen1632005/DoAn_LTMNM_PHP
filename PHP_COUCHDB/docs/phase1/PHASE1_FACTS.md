# Phase 1 – Số liệu CouchDB

- Database demo: `retail_order_delivery_demo`
- Tổng documents: 150 (business: 147, design: 3)
- Mango indexes trong `_design/catalog_indexes`: 15
- Tạo lúc (UTC): 2026-10-28T01:50:38Z

## Bảng 4.3 – Số lượng document theo type

| Type | Số lượng |
|---|---:|
| cart | 8 |
| customer | 15 |
| order | 65 |
| product | 30 |
| review | 20 |
| shipping_method | 3 |
| staff | 3 |
| voucher | 3 |

## Bảng 4.6 – Phân bố order theo trạng thái

| Trạng thái | Số đơn |
|---|---:|
| cancelled | 7 |
| confirmed | 11 |
| delivered | 10 |
| packing | 11 |
| pending | 11 |
| shipping | 15 |

## Bảng 4.7 – Phân bố delivery tracking

| Delivery status | Số đơn |
|---|---:|
| created | 2 |
| failed_delivery | 3 |
| in_transit | 5 |
| out_for_delivery | 2 |
| picked_up | 3 |

## Bảng 4.8 – Kết quả 12 truy vấn Mango

| Mã | Selector tóm tắt | Index theo `_explain` | Kết quả API | Trang | docs_examined | execution_time_ms | Trạng thái | Ghi chú |
|---:|---|---|---:|---:|---:|---:|---|---|
| 01 | `{"type":{"$eq":"order"},"status":{"$eq":"pending"}}` | `_design/catalog_indexes/admin_orders` | 11 | 3 | 12 | 65.224 | PASS |  |
| 02 | `{"type":{"$eq":"order"},"status":{"$eq":"shipping"}}` | `_design/catalog_indexes/admin_orders` | 15 | 2 | 15 | 8.334 | PASS |  |
| 03 | `{"type":{"$eq":"order"},"status":{"$eq":"delivered"}}` | `_design/catalog_indexes/admin_orders` | 10 | 2 | 10 | 6.748 | PASS |  |
| 04 | `{"type":{"$eq":"order"},"status":{"$eq":"cancelled"}}` | `_design/catalog_indexes/admin_orders` | 7 | 2 | 7 | 5.986 | PASS |  |
| 05 | `{"type":{"$eq":"order"},"customer.customer_id":{"$eq":"DEMO-C001"}}` | `_design/catalog_indexes/customer_orders` | 4 | 2 | 4 | 4.277 | PASS |  |
| 06 | `{"type":{"$eq":"order"},"ordered_at":{"$gte":"2026-06-01T00:00:00Z","$lt":"2027-01-01T00:00:00Z"}}` | `_design/catalog_indexes/admin_orders_by_date` | 65 | 4 | 67 | 28.232 | PASS |  |
| 07 | `{"type":{"$eq":"order"},"status":{"$eq":"pending"},"payment.method":{"$eq":"cod"},"payment.paid":{"$eq":false}}` | `_design/catalog_indexes/admin_orders` | 8 | 2 | 11 | 6.644 | PASS |  |
| 08 | `{"type":{"$eq":"order"},"guest_order":{"$eq":true}}` | `_design/catalog_indexes/guest_orders` | 15 | 2 | 15 | 8.964 | PASS |  |
| 09 | `{"type":{"$eq":"order"},"delivery_tracking.status":{"$eq":"in_transit"}}` | `_design/catalog_indexes/delivery_status` | 5 | 2 | 5 | 5.193 | PASS |  |
| 10 | `{"type":{"$eq":"order"},"delivery_tracking.status":{"$eq":"out_for_delivery"}}` | `_design/catalog_indexes/delivery_status` | 2 | 2 | 2 | 4.835 | PASS |  |
| 11 | `{"type":{"$eq":"order"},"delivery_tracking.status":{"$eq":"failed_delivery"}}` | `_design/catalog_indexes/delivery_status` | 3 | 2 | 3 | 5.325 | PASS |  |
| 12 | `{"type":{"$eq":"order"},"delivery_tracking.tracking_code":{"$eq":"DEMO-TRK-035"}}` | `_design/catalog_indexes/delivery_tracking_code` | 1 | 2 | 1 | 4.093 | PASS |  |

Kết quả là tổng qua mọi trang bookmark; docs_examined và execution_time_ms là tổng từ các response của mọi trang đã đọc.

### Guest index trước/sau

| Trạng thái | Index | docs_examined | Kết quả |
|---|---|---:|---:|
| Trước | `orders_type_scan` | 65 | 15 |
| Sau | `guest_orders` | 15 | 15 |

## Bảng 3.3 / 3.5 / 3.6 – Đối chiếu delivery index

| Query | Index trước | docs_examined trước | Index sau | docs_examined sau | Kết quả trước/sau |
|---:|---|---:|---|---:|---|
| 09 | `orders_type_scan` | 65 | `_design/catalog_indexes/delivery_status` | 5 | 5 / 5 |
| 10 | `orders_type_scan` | 65 | `_design/catalog_indexes/delivery_status` | 2 | 2 / 2 |
| 11 | `orders_type_scan` | 65 | `_design/catalog_indexes/delivery_status` | 3 | 3 / 3 |

## Tái lập

Chạy chính xác các lệnh sau từ thư mục repo trong PowerShell:

- `docker compose up --build -d`
- `Invoke-WebRequest http://127.0.0.1:8081/health/ready`
- `docker compose exec -T app composer db:seed-demo`
- `docker compose exec -T app composer seed:verify`
- `docker compose exec -T app composer phase1:report`
- `docker compose exec -T -e COUCHDB_DATABASE=retail_order_delivery_demo app composer db:backup -- phase1-demo.json`
- `docker compose exec -T -e COUCHDB_DATABASE=retail_restore_check_test app composer db:restore -- phase1-demo.json retail_restore_check_test`
- `docker compose exec -T -e COUCHDB_DATABASE=retail_restore_check_test app composer db:restore-verify -- phase1-demo.json retail_restore_check_test`

Payload query và toàn bộ response `_explain`/`_find` được lưu theo từng truy vấn trong `phase1_facts.json`.

## Nguyên nhân gốc các sai lệch cũ

- Query 06 (27 so với 21): **chưa xác định** nếu thiếu payload Fauxton cũ và tên database mà Fauxton dùng. File hiện tại đặt `limit: 50`, nên mặc định CouchDB 25 không giải thích được con số 21.
- Query 01 (fallback): **chưa xác định** nếu thiếu response `_explain` từ đúng payload Fauxton. Báo cáo có `_explain` API của database mới nhưng không xem đó là bằng chứng về request cũ.
- Probe read-only trên `shopquan_ao_phase06_demo_test`: query 06 file trả 27; cùng selector/payload nhưng chỉ đặt `limit: 25` trả 25. Query 01 đầy đủ trả 5 bằng `admin_orders`; bỏ `use_index` và `sort` tái hiện `_all_docs`, 61 docs examined. Bỏ riêng `use_index` nhưng giữ `sort` trả HTTP 400 vì không có index cho sort. Xem payload/response đầy đủ trong `legacy_database_comparison` của JSON.
