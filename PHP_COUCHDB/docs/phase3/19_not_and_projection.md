# Query 19

- Mục đích nghiệp vụ: Order COD không cancelled, chiếu một số trường và giới hạn trang.
- Toán tử: `$not, $and, fields, limit, skip`.
- Index theo `_explain`: `admin_orders`.
- Kết quả kỳ vọng và kiểm chứng độc lập: 40 document(s); PHP filter=40.
- docs_examined: 68; execution_time_ms: 29.75.
- Trạng thái: **PASS**.

Payload đầy đủ nằm trong `ADVANCED_QUERIES.md` và JSON tương ứng dưới `database/couchdb/demo_queries/19_not_and_projection.json`.
