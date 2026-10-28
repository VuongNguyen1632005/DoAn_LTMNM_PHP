# Query 14

- Mục đích nghiệp vụ: Lọc nhiều trạng thái và loại cancelled.
- Toán tử: `$in, $nin`.
- Index theo `_explain`: `admin_orders`.
- Kết quả kỳ vọng và kiểm chứng độc lập: 37 document(s); PHP filter=37.
- docs_examined: 0; execution_time_ms: 8.734.
- Trạng thái: **PASS**.

Payload đầy đủ nằm trong `ADVANCED_QUERIES.md` và JSON tương ứng dưới `database/couchdb/demo_queries/14_status_in_nin.json`.
