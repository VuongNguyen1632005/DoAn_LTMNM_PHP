# Kiểm thử validator CouchDB đầy đủ

## Cách chạy

```powershell
docker compose exec -T -e COUCHDB_DATABASE=retail_order_delivery_test app php scripts/verify_full_validator.php
```

Biến `COUCHDB_DATABASE` chỉ được dùng để xác nhận runtime nguồn là database test; script tạo database riêng có hậu tố `_test`, cài nguyên vẹn `database/couchdb/design-docs/domain_validation.json` (11,649 byte), chạy các request, rồi xóa scratch database và xác nhận GET trả HTTP 404.

## Kết quả runtime

Lần chạy ngày 2026-10-28 trên CouchDB 3.5: **6/6 PASS**. Request payload, mã HTTP thực và response body nguyên bản có trong [`validator_tests_evidence.json`](validator_tests_evidence.json).

| Ca | Request | HTTP thực | Response body thực | Kết quả |
|---|---|---:|---|---|
| Trạng thái order không hợp lệ | Cập nhật order từ `pending` thành `invalid_status` | 403 | `{"error":"forbidden","reason":"order requires a valid status, receiver, and non-empty items"}` | PASS |
| Rút ngắn `delivery_tracking.history` | Cập nhật lịch sử từ 2 event xuống 1 event | 403 | `{"error":"forbidden","reason":"delivery history is append-only"}` | PASS |
| Paid quay về unpaid | Cập nhật `payment.status=paid`, `paid=true` thành `unpaid`, `false` | 403 | `{"error":"forbidden","reason":"paid orders cannot be marked unpaid"}` | PASS |
| Chuyển trạng thái giao sai | `created` → `in_transit`, bỏ qua `picked_up` | 403 | `{"error":"forbidden","reason":"delivery status transition is not allowed"}` | PASS |
| Tạo tracking hợp lệ | Tạo order `shipping` với tracking `created` ban đầu | 201 | CouchDB trả `ok`, `id`, `rev` | PASS |
| Chuyển giao hợp lệ | `created` → `picked_up`, nối thêm event | 201 | CouchDB trả `ok`, `id`, `rev` | PASS |

Các response trong bảng đã bỏ newline cuối để dễ đọc; JSON đầy đủ, bao gồm revision và payload, được lưu trong evidence. `invalid_status` bị từ chối đúng HTTP 403; validator hiện trả reason chung `order requires a valid status, receiver, and non-empty items` cho điều kiện status/receiver/items.
