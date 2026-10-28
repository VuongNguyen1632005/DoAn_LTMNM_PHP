# Query 16

- Mục đích nghiệp vụ: Order theo khoảng tổng tiền và ngày, sắp xếp theo tổng tiền.
- Toán tử: `$gt, $lte, $gte, $lt, sort`.
- Index theo `_explain`: `orders_by_total`.
- Kết quả kỳ vọng và kiểm chứng độc lập: 1 document(s); PHP filter=1.
- docs_examined: 2; execution_time_ms: 3.978.
- Trạng thái: **PASS**.

Payload đầy đủ nằm trong `ADVANCED_QUERIES.md` và JSON tương ứng dưới `database/couchdb/demo_queries/16_total_range_date_sort.json`.
