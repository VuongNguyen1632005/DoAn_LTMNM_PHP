# Query 18

- Mục đích nghiệp vụ: Tìm tên sản phẩm theo regex, giới hạn tập ứng viên bằng type=product và active=true có index.
- Toán tử: `$regex`.
- Index theo `_explain`: `active_products`.
- Kết quả kỳ vọng và kiểm chứng độc lập: 5 document(s); PHP filter=5.
- docs_examined: 55; execution_time_ms: 18.516.
- Trạng thái: **PASS**.

Payload đầy đủ nằm trong `ADVANCED_QUERIES.md` và JSON tương ứng dưới `database/couchdb/demo_queries/18_product_name_regex.json`.
