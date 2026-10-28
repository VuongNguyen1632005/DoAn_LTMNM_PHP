# Query 15

- Mục đích nghiệp vụ: Order có một item quantity ≥ 2 và unit_price > 200000 VND.
- Toán tử: `$elemMatch, $gte, $gt`.
- Index theo `_explain`: `orders_type_scan`.
- Kết quả kỳ vọng và kiểm chứng độc lập: 48 document(s); PHP filter=48.
- docs_examined: 66; execution_time_ms: 31.49.
- Trạng thái: **PASS**.

Payload đầy đủ nằm trong `ADVANCED_QUERIES.md` và JSON tương ứng dưới `database/couchdb/demo_queries/15_items_elem_match.json`.
