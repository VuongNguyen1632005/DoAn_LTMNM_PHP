# Query 20

- Mục đích nghiệp vụ: Tra cứu trường lồng receiver.name vì schema demo không có receiver.city.
- Toán tử: `nested field, $eq`.
- Index theo `_explain`: `orders_by_receiver`.
- Kết quả kỳ vọng và kiểm chứng độc lập: 15 document(s); PHP filter=15.
- docs_examined: 15; execution_time_ms: 9.021.
- Trạng thái: **PASS**.

Payload đầy đủ nằm trong `ADVANCED_QUERIES.md` và JSON tương ứng dưới `database/couchdb/demo_queries/20_nested_receiver_name.json`.
