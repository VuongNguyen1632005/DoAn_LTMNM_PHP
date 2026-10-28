# Query 17

- Mục đích nghiệp vụ: Order có delivery_tracking; dùng $exists=false để tìm order không có tracking.
- Toán tử: `$exists`.
- Index theo `_explain`: `orders_type_scan`.
- Kết quả kỳ vọng và kiểm chứng độc lập: 15 document(s); PHP filter=15.
- docs_examined: 82; execution_time_ms: 33.546.
- Trạng thái: **PASS**.

Payload đầy đủ nằm trong `ADVANCED_QUERIES.md` và JSON tương ứng dưới `database/couchdb/demo_queries/17_delivery_tracking_exists.json`.
