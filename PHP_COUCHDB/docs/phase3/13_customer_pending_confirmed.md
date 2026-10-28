# Query 13

- Mục đích nghiệp vụ: Order pending hoặc confirmed của một khách cụ thể.
- Toán tử: `$or`.
- Index theo `_explain`: `customer_orders_status`.
- Kết quả kỳ vọng và kiểm chứng độc lập: 1 document(s); PHP filter=1.
- docs_examined: 7; execution_time_ms: 5.015.
- Trạng thái: **PASS**.

Payload đầy đủ nằm trong `ADVANCED_QUERIES.md` và JSON tương ứng dưới `database/couchdb/demo_queries/13_customer_pending_confirmed.json`.
