# Phase 3 – Tổng hợp bằng chứng cho báo cáo

- Database: `retail_order_delivery_demo`; generated UTC: 2026-10-28T02:09:26Z
- Runtime: CouchDB 3.5; 150 documents (147 business + 3 design), 65 orders, 15 guest orders.

## Bảng query 01–20

| Query | Mục đích | Selector | Index | Kết quả | docs_examined | execution_time_ms | Trạng thái |
|---:|---|---|---|---:|---:|---:|---|
| 01 | pending orders | `{"type":{"$eq":"order"},"status":{"$eq":"pending"}}` | `admin_orders` | 11 | 12 | 65.224 | PASS |
| 02 | shipping orders | `{"type":{"$eq":"order"},"status":{"$eq":"shipping"}}` | `admin_orders` | 15 | 15 | 8.334 | PASS |
| 03 | delivered orders | `{"type":{"$eq":"order"},"status":{"$eq":"delivered"}}` | `admin_orders` | 10 | 10 | 6.748 | PASS |
| 04 | cancelled orders | `{"type":{"$eq":"order"},"status":{"$eq":"cancelled"}}` | `admin_orders` | 7 | 7 | 5.986 | PASS |
| 05 | customer orders | `{"type":{"$eq":"order"},"customer.customer_id":{"$eq":"DEMO-C001"}}` | `customer_orders` | 4 | 4 | 4.277 | PASS |
| 06 | orders by date | `{"type":{"$eq":"order"},"ordered_at":{"$gte":"2026-06-01T00:00:00Z","$lt":"2027-01-01T00:00:00Z"}}` | `admin_orders_by_date` | 65 | 67 | 28.232 | PASS |
| 07 | unpaid cod | `{"type":{"$eq":"order"},"status":{"$eq":"pending"},"payment.method":{"$eq":"cod"},"payment.paid":{"$eq":false}}` | `admin_orders` | 8 | 11 | 6.644 | PASS |
| 08 | guest orders | `{"type":{"$eq":"order"},"guest_order":{"$eq":true}}` | `guest_orders` | 15 | 15 | 8.964 | PASS |
| 09 | delivery in transit | `{"type":{"$eq":"order"},"delivery_tracking.status":{"$eq":"in_transit"}}` | `delivery_status` | 5 | 5 | 5.193 | PASS |
| 10 | delivery out for delivery | `{"type":{"$eq":"order"},"delivery_tracking.status":{"$eq":"out_for_delivery"}}` | `delivery_status` | 2 | 2 | 4.835 | PASS |
| 11 | delivery failed | `{"type":{"$eq":"order"},"delivery_tracking.status":{"$eq":"failed_delivery"}}` | `delivery_status` | 3 | 3 | 5.325 | PASS |
| 12 | tracking code | `{"type":{"$eq":"order"},"delivery_tracking.tracking_code":{"$eq":"DEMO-TRK-035"}}` | `delivery_tracking_code` | 1 | 1 | 4.093 | PASS |
| 13 | Order pending hoặc confirmed của một khách cụ thể. | `{"type":{"$eq":"order"},"customer.customer_id":{"$eq":"DEMO-C001"},"$or":[{"status":{"$eq":"pending"}},{"status":{"$eq":"confirmed"}}]}` | `customer_orders_status` | 1 | 7 | 4.978 | PASS |
| 14 | Lọc nhiều trạng thái và loại cancelled. | `{"type":{"$eq":"order"},"status":{"$in":["pending","confirmed","shipping","cancelled"],"$nin":["cancelled"]}}` | `admin_orders` | 37 | 0 | 4.217 | PASS |
| 15 | Order có một item quantity ≥ 2 và unit_price > 200000 VND. | `{"type":{"$eq":"order"},"items":{"$elemMatch":{"quantity":{"$gte":2},"unit_price":{"$gt":200000}}}}` | `orders_type_scan` | 48 | 66 | 23.027 | PASS |
| 16 | Order theo khoảng tổng tiền và ngày, sắp xếp theo tổng tiền. | `{"type":{"$eq":"order"},"totals.grand_total":{"$gt":350000,"$lte":500000},"ordered_at":{"$gte":"2026-06-01T00:00:00Z","$lt":"2026-08-01T00:00:00Z"}}` | `orders_by_total` | 1 | 2 | 3.978 | PASS |
| 17 | Order có delivery_tracking; dùng $exists=false để tìm order không có tracking. | `{"type":{"$eq":"order"},"delivery_tracking":{"$exists":true}}` | `orders_type_scan` | 15 | 82 | 26.196 | PASS |
| 18 | Tìm tên sản phẩm theo regex, giới hạn tập ứng viên bằng type=product và active=true có index. | `{"type":{"$eq":"product"},"active":{"$eq":true},"name":{"$regex":"^Sản phẩm Demo 0[1-5]$"}}` | `active_products` | 5 | 55 | 20.556 | PASS |
| 19 | Order COD không cancelled, chiếu một số trường và giới hạn trang. | `{"$and":[{"type":{"$eq":"order"}},{"$not":{"status":{"$eq":"cancelled"}}},{"payment.method":{"$eq":"cod"}}]}` | `admin_orders` | 40 | 68 | 31.538 | PASS |
| 20 | Tra cứu trường lồng receiver.name vì schema demo không có receiver.city. | `{"type":{"$eq":"order"},"receiver.name":{"$eq":"Khách vãng lai Demo"}}` | `orders_by_receiver` | 15 | 15 | 7.867 | PASS |

## Index trước/sau

| Truy vấn | Trước | docs_examined trước | Sau | docs_examined sau | Đếm khớp |
|---|---|---:|---|---:|---|
| Query 08 guest | `orders_type_scan` | 65 | `guest_orders` | 15 | 15 / 15 |
| Query 09 delivery | `orders_type_scan` | 65 | `_design/catalog_indexes/delivery_status` | 5 | 5 / 5 |
| Query 10 delivery | `orders_type_scan` | 65 | `_design/catalog_indexes/delivery_status` | 2 | 2 / 2 |
| Query 11 delivery | `orders_type_scan` | 65 | `_design/catalog_indexes/delivery_status` | 3 | 3 / 3 |

## Views

| View/check | Result |
|---|---|
| order_count_by_status group=true | PASS |
| order_total_by_status group=true | PASS |
| orders_by_status compatibility group=true | PASS |
| orders_by_month group_level=1 | PASS |
| orders_by_month group_level=2 | PASS |
| top_products group=true | PASS |
| revenue_delivered_by_month group_level=2 | PASS |
| orders_by_customer reduce=false include_docs startkey/endkey | PASS |
| `orders_by_month` totals | 65 orders; sum grand_total = 75,950,000 VND |
| `orders_by_customer` lookup | 50 customer orders |

## Replication, conflict, attachment, security, compaction

| Feature | Bằng chứng runtime | Trạng thái |
|---|---|---|
| Replication demo | 150/150 docs; normalized hash mismatches=0; continuous job canceled | PASS |
| Conflict | winner=edited-on-right; losing leaf deleted; conflicts cleared | PASS |
| Attachment | PNG 68 bytes; digest=md5-WY4oUadFjaXDQdG7dbQsYQ==; delete HTTP 200 | PASS |
| `_security` | member=200; outsider=403; member design edit=403 | PASS |
| Compaction/delete | revisions=51; active bytes 3195 → 2437; file bytes 225629 → 33123; delete HTTP 200, GET 404 | PASS |

## Backup/restore và CSV

| Hạng mục | Kết quả |
|---|---|
| JSON backup/restore | 150/150 documents; 3 design docs; missing/extra/different = 0/0/0; 15 JSON Mango indexes (16 `_index` rows including `_all_docs`); security parity=PASS; validator probe HTTP 403 | PASS |
| Replication backup | 150/150; content diffs=0; security parity=PASS | PASS |
| CSV import | 3 imported; 1 validator row error(s); cleanup=PASS | PASS |
| CSV export | 65 rows = query count 65; index `admin_orders`; docs_examined=65 | PASS |

## Quản trị và validator

| Hạng mục | Ca | Kết quả |
|---|---:|---|
| HTTP admin users + manager backup | 25 | PASS |
| Full domain validator | 6 | PASS |

Payload lịch sử Fauxton cho query 01 và query 06 chưa được cung cấp nên nguyên nhân không được xác minh. Theo quyết định người dùng ngày 2026-10-28, điều tra đã **đóng, không làm tiếp**. Kết quả runtime hiện tại được giữ làm bằng chứng riêng.

Evidence chi tiết: `phase1/phase1_facts.json`, `advanced_query_facts.json`, `views_facts.json`, `feature_evidence/*.json`, `backup_restore_evidence.json`, `csv_exchange_evidence.json`, và `phase4/*_evidence.json`.
