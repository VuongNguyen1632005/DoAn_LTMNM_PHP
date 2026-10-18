# CÂU HỎI BẢO VỆ – NOSQL/FAUXTON/COUCHDB

Bộ 40 câu hỏi và trả lời ngắn, dựa trên source và Phase reports. Khi trả lời về thực nghiệm, nêu đó là kết quả Phase 06/07 ngày 07/10/2026; runtime hiện tại chưa kiểm tra lại trong lượt viết báo cáo. Tra file chứng minh trong `NOSQL_REPORT_SOURCE_MAP.md`.

## 1. NoSQL là gì?

Là nhóm hệ quản trị có mô hình dữ liệu ngoài mô hình quan hệ truyền thống, như key–value, document, column-family và graph. Mỗi hệ có query/consistency riêng; không phải mọi NoSQL đều không có ràng buộc.

## 2. CouchDB thuộc nhóm nào?

CouchDB là document database, lưu JSON và cung cấp HTTP API. Project lưu order với items và history lồng trong một document.

## 3. Fauxton là gì?

Là giao diện web quản trị đi kèm CouchDB. Đồ án dùng để xem database/document, chạy Mango và xem index/design document.

## 4. Fauxton khác phpMyAdmin thế nào?

Fauxton quản trị CouchDB với JSON/revision/Mango. phpMyAdmin thuộc hệ MySQL/MariaDB và dữ liệu quan hệ. Không dùng hai tool thay thế cho cùng database CouchDB.

## 5. Vì sao dùng CouchDB cho đề tài?

Order thường được đọc cùng items, shipping, payment và history. Document aggregate phù hợp màn detail; REST/JSON thuận tiện cho PHP và mục tiêu học NoSQL.

## 6. Vì sao không dùng MySQL?

MySQL cũng có thể triển khai bài toán này. Đề tài yêu cầu nghiên cứu Fauxton/CouchDB nên chọn document model để minh họa embedding, revision và Mango; không khẳng định MySQL kém hơn.

## 7. `_id` là gì?

ID của document trong database. Project có prefix như `order:P06-0020`; prefix giúp nhận diện nhưng không phải cơ chế phân quyền.

## 8. `_rev` là gì?

Token revision do CouchDB cấp sau ghi, gồm generation và digest. Update cần revision hiện hành; nó không phải schema version hay nhật ký nghiệp vụ đầy đủ.

## 9. 409 Conflict nghĩa là gì trong demo?

Fixture đọc revision 1, cập nhật lên revision 2 rồi gửi lại revision 1 cũ; CouchDB trả 409. Đây là stale-write conflict trên một node, chưa demo conflict do replication.

## 10. PHP xử lý conflict thế nào?

CouchDbClient trả status, không tự merge. Workflow order đọc lại và áp dụng callback tối đa 8 lượt; product/account có trường hợp báo tải lại. Không phải mọi 409 đều tự giải quyết được.

## 11. Mango Query là gì?

Request JSON với selector để tìm documents qua `_find`. Có thể kèm fields/sort/limit/use_index; báo cáo có 12 file thực tế.

## 12. Mango Index là gì?

Cấu trúc hỗ trợ query theo fields và partial selector. Project có 9 JSON indexes và primary `_all_docs`; chưa có index riêng cho delivery status.

## 13. `_explain` dùng làm gì?

Kiểm tra index planner chọn cho request. Phase 07 ghi pending dùng `admin_orders`, customer dùng `customer_orders`, tracking dùng `delivery_tracking_code`.

## 14. Vì sao failed query xét 58 documents nhưng trả 1?

Chưa có index phù hợp cho `delivery_tracking.status`, nên fallback quét primary. Số 58 là tổng demo documents gồm 2 design docs, không phải 58 orders.

## 15. Có thể kết luận hệ thống nhanh từ 1–3 ms không?

Không. Đó là một lượt trên dataset nhỏ, chưa có lặp, đối chứng, tải lớn hoặc kiểm soát cache. Chỉ dùng để giải thích index và examined count.

## 16. Design document là gì?

Document đặc biệt chứa định nghĩa xử lý của CouchDB. Project có `domain_validation` với validator và `catalog_indexes` với language query; chưa có business MapReduce views riêng.

## 17. Validation hoạt động thế nào?

`validate_doc_update` kiểm tra document và throw forbidden khi sai. Source kiểm tra type/schema/enum/shape và nhiều rule delivery; PHP vẫn giữ quyền, CSRF, stock và business workflow.

## 18. Validator bảo vệ mọi order transition không?

Không. Có order enum nhưng `status_history` chỉ được kiểm tra là array. Chuỗi transition order do OrderWorkflowService áp dụng.

## 19. History có bất biến tuyệt đối không?

Không. Delivery history có append-only guard trong update thường. Payment history được kiểm tra khi field còn hiện diện, nhưng bỏ hẳn field chưa bị chặn riêng; deletion/design updates còn có return sớm. Báo cáo nêu đúng giới hạn này.

## 20. HTTP 403 fixture có chứng minh riêng invalid status không?

Nó chứng minh document sai bị reject. Fixture còn nhiều trường chưa đầy đủ nên không tách riêng từng rule bằng case đó; rule enum được chứng minh qua source.

## 21. Embedded document là gì?

Object/array được lưu trong document cha. Project nhúng items và delivery tracking vào order để đọc và cập nhật chung.

## 22. Denormalization là gì?

Lưu lặp có chủ đích thông tin cần đọc, như tên/giá sản phẩm và phí ship lúc mua. Cần xác định đâu là snapshot lịch sử, đâu là dữ liệu hiện hành.

## 23. Vì sao order embed items?

Trang detail cần các dòng hàng cùng đơn và giữ giá/tên/size lúc mua. Giá product đổi sau đó không viết lại order cũ.

## 24. Vì sao delivery embed trong order?

Tracking gắn với một đơn và thường đọc cùng detail. Khi delivery delivered, order và delivery có thể ghi đồng bộ trong một PUT; đổi lại document/history tăng và có cạnh tranh revision.

## 25. Snapshot khác backup thế nào?

Snapshot order giữ giá trị giao dịch lúc mua. Backup xuất một bộ documents để phục hồi; backup script phân trang chưa tạo snapshot nguyên tử khi có ghi đồng thời.

## 26. Fauxton mạnh ở điểm nào?

Đi kèm CouchDB, mở qua browser và quan sát trực tiếp document/Mango/index/design doc. Phù hợp giải thích dữ liệu project mà không cài GUI riêng.

## 27. Fauxton yếu ở điểm nào?

Tập trung quản trị CouchDB, không thay form nghiệp vụ. Sửa JSON cần hiểu contract; không có các công cụ schema/aggregation MongoDB giống Compass.

## 28. So với MongoDB Compass?

Compass là desktop GUI cho MongoDB, có schema analysis và aggregation pipeline builder. Fauxton là web UI cho CouchDB. So sánh trong báo cáo là nghiên cứu, chưa chạy benchmark hoặc cài Compass.

## 29. Backup/restore project thế nào?

`database_backup.php` đọc `_all_docs`, kể cả design docs/attachments, vào JSON. Restore chỉ nhận DB test mới, bỏ revision metadata và kiểm tra kết quả/count. Ngày 10/10/2026, round-trip trên 60 document demo đã PASS sau đối chiếu nội dung, Mango/index và validator; uploads phải backup riêng.

## 30. Restore giữ revision cũ không?

Không. Script bỏ `_rev`, `_revisions`, `_conflicts` nên phục hồi nội dung với revision mới. Đây cũng chưa là backup toàn server/security/revision tree.

## 31. Import/export thế nào?

Fixture dùng `import_seed.php`; SQL snapshot có exporter/preflight/transform/importer riêng. Seed/import đã có test cô lập; export backup CouchDB cần thêm output thực tế. Không gọi fixture là backup live.

## 32. Database có dữ liệu thật không?

Database demo đã được tạo/thao tác trên CouchDB thật, chứa dữ liệu tổng hợp phục vụ học tập. Không dùng thông tin khách hàng thật để trình bày. Baseline có 56 business docs, 2 design docs và 27 orders.

## 33. Vì sao guest query trả 0?

Baseline Phase 06 không seed guest order. Query 08 hợp lệ và HTTP 200 nhưng 0 result. Luồng guest được test bằng fixture khác; không bịa guest document trong 27 order demo.

## 34. COD query có tìm tất cả COD unpaid không?

Không. File 07 có thêm status pending, nên chỉ tìm COD pending với `payment.paid=false`. Các order shipping/delivered chưa trả không thuộc query đó.

## 35. PHP kết nối CouchDB ra sao?

Browser → Router → Controller → Service → Repository → CouchDbClient. Client dùng cURL/REST/JSON và config môi trường. Browser customer không nhận CouchDB password.

## 36. Realtime hoạt động thế nào?

PHP theo dõi order bằng `_changes`/`_doc_ids`, longpoll tối đa 8 giây, phát SSE rồi đóng response. EventSource retry 5 giây. Đã demo không F5; offline/online và tải lớn chưa xác minh.

## 37. Order/delivery/payment khác nhau thế nào?

Order cho biết xử lý đơn, delivery cho biết vận chuyển, payment cho biết ghi nhận thu tiền. Staff cập nhật delivery delivered không tự đánh COD paid; khách xác nhận nhận COD có rule paid riêng.

## 38. “Delete” product có phải xóa document không?

Trong website là soft hide (`active=false`), có restore. Order snapshot vẫn còn. Không trình bày như CouchDB hard DELETE đã demo qua Fauxton.

## 39. User management đã đầy đủ chưa?

Có register/login/password hash/role và provision terminal. Chưa có admin CRUD user UI đầy đủ hoặc email reset tự động. Rubric được minh họa trong phạm vi auth/phân quyền thực tế.

## 40. Hạn chế project và việc cần làm trước nộp?

Không có ảnh order snapshot, shipper/GPS/gateway/refund/replication/attachment app; chưa online. Cần ảnh thật, điền bìa và dàn trang Word. Backup/restore round-trip đã kiểm thử trên demo local; không thêm feature chỉ để báo cáo đủ điểm.

## Các câu trả lời cần tránh

Không nói “mọi NoSQL nhanh hơn SQL”, “Fauxton là website bán hàng”, “revision lưu mọi history”, “validator bảo đảm tất cả quyền/FK/transaction”, “delivered luôn paid”, “đã deploy Render”, “đã phục hồi toàn server” hoặc “dữ liệu là khách thật”. Backup/restore chỉ đã kiểm chứng trên demo local; mỗi kết luận cần phù hợp phạm vi, bằng chứng và thời điểm.
