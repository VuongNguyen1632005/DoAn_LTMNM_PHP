# Biên bản bàn giao Giai đoạn 2 — Fauxton

**Dự án:** D:\PHP_NOSQL\PHP_COUCHDB  
**Ngày tổng hợp:** 27/10/2026  
**Phạm vi:** tổng hợp bằng chứng đã lưu; không chạy lại kiểm thử. Kết quả thực thi bên dưới là **lịch sử Phase 07** trên database demo `shopquan_ao_phase06_demo_test`, không chứng minh CouchDB hiện tại hoặc database ứng dụng `shopquan_ao_sql_migration_test`.

## 1. Mười hai Mango Query

Source: `database/couchdb/demo_queries/01_pending_orders.json` đến `12_tracking_code.json`. `docs/PHASE_07_NOSQL_FAUXTON_REPORT.md` ghi **12/12** request `POST /<db>/_find` trả HTTP 200. Chỉ **03/12** truy vấn (02, 11, 12) được ghi chạy trực tiếp trên Fauxton GUI.

| # | Truy vấn | API: số kết quả lịch sử | Fauxton GUI |
|---:|---|---:|---|
| 01 | Pending | 5 | Chưa có biên bản GUI riêng |
| 02 | Shipping | 6 | 6 kết quả / 6 documents examined |
| 03 | Delivered | 6 | Chưa có biên bản GUI riêng |
| 04 | Cancelled | 2 | Chưa có biên bản GUI riêng |
| 05 | Customer mẫu | 5 | Chưa có biên bản GUI riêng |
| 06 | Khoảng ngày | 27 | Chưa có biên bản GUI riêng |
| 07 | COD pending chưa trả | 5 | Chưa có biên bản GUI riêng |
| 08 | Guest | 0 | Chưa có biên bản GUI riêng |
| 09 | Delivery in transit | 2 | Chưa có biên bản GUI riêng |
| 10 | Delivery out for delivery | 1 | Chưa có biên bản GUI riêng |
| 11 | Failed delivery | 1 | 1 / 58; GUI cảnh báo thiếu index phù hợp |
| 12 | Tracking P06-TRK-0020 | 1 | 1 / 1 với delivery_tracking_code |

Query 08 hợp lệ nhưng fixture không có guest order. Query 06 có bounds ngày gắn với lần seed cũ. Mã P06-* là dữ liệu tổng hợp. Không trình bày 12 API như 12 lượt GUI.

## 2. Chín Mango Index và _explain

`database/couchdb/indexes/catalog_indexes.json` khai báo chín JSON index trong `_design/catalog_indexes`. Phase 07 ghi `GET /<db>/_index` và Manage Indexes xác nhận trên demo khi đó. Primary `_all_docs` có sẵn, nằm ngoài số chín.

| Index | Trường theo thứ tự | Mục đích |
|---|---|---|
| active_products | type, active | Product active |
| admin_products | type | Quản trị product |
| active_reviews | type, active | Review active |
| admin_reviews | type | Quản trị review |
| account_usernames | type, auth.username | Customer/staff lookup |
| customer_orders | type, customer.customer_id, ordered_at | Đơn của customer |
| admin_orders | type, status, ordered_at | Đơn theo status |
| admin_orders_by_date | type, ordered_at | Đơn theo ngày |
| delivery_tracking_code | type, delivery_tracking.tracking_code | Tracking code |

Ba `POST /<db>/_explain` được ghi: Query 01 chọn `catalog_indexes/admin_orders`, Query 05 chọn `catalog_indexes/customer_orders`, Query 12 chọn `catalog_indexes/delivery_tracking_code`. Chưa có _explain riêng cho toàn bộ 12 query hoặc từng index. Guest và delivery status fallback `_all_docs`, có thể xét 58 documents. Các số 6/6, 1/58 và 1/1 thuộc ba selector khác nhau, không phải phép đo tốc độ trước/sau cùng truy vấn.

## 3. Hai MapReduce View

Mục 3.4.13 của `BAOCAO_NOSQL-main.docx` ở thư mục cha mô tả JSON mẫu `_design/order_statistics` và URL query với `group=true`.

| View | Mã mô tả trong Word | Trạng thái chứng cứ |
|---|---|---|
| order_count_by_status | Order: emit(doc.status, 1), reduce _count | Có mã mẫu, URL và chú thích Hình 3.7; chưa thấy asset design doc hoặc response GET view trong repo |
| order_total_by_status | Order có grand_total dạng số: emit(doc.status, doc.totals.grand_total), reduce _sum | Có mã mẫu và URL; chưa thấy asset design doc hoặc response GET view trong repo |

`domain_validation.json` và `catalog_indexes.json` là design document khác. **Chưa xác nhận** `_design/order_statistics` và hai view tồn tại, chạy được trên CouchDB hiện tại. Chú thích hình trong Word không thay thế response hoặc ảnh GUI có ngữ cảnh.

## 4. File thay đổi và bằng chứng hiện có

**Lần tổng hợp này chỉ tạo biên bản này.** Trước khi tạo, `git status --short` cho thấy các thay đổi đã có tại `PHP_COUCHDB/README.md`, `composer.json`, `public/index.php`, `src/Auth/AccountRepository.php`, `src/Auth/AccountService.php`, `templates/layout.php`; các file chưa theo dõi về quản trị người dùng gồm `docs/BIEN_BAN_BAN_GIAO_GIAI_DOAN_2.md`, `docs/USER_ADMINISTRATION.md`, `docs/BAO_CAO_PHAN_TICH_KY_THUAT.md`, `scripts/set_quick_passwords.php`, `scripts/set_staff_password.php`, `scripts/smoke_user_administration.php`, `src/Auth/UserAdministrationController.php`, `src/Auth/UserAdministrationService.php`, `templates/admin/users/`, `var/reference_materials/`. Trạng thái working tree **không đủ căn cứ** quy chúng cho Fauxton Giai đoạn 2. Không stage/commit.

Bằng chứng đã đọc: `docs/PHASE_07_NOSQL_FAUXTON_REPORT.md`; 12 request JSON và README ở `database/couchdb/demo_queries/`; `database/couchdb/indexes/catalog_indexes.json`; `docs/final_report/NOSQL_FAUXTON_COUCHDB_REPORT.md`; `BAOCAO_NOSQL-main.docx`. Biên bản `docs/BIEN_BAN_BAN_GIAO_GIAI_DOAN_2.md` cũ nói về **quản trị người dùng**, không phải nghiệm thu Fauxton. Không thấy bộ response/ảnh/log riêng Giai đoạn 2; checklist Phase 07 ghi ảnh chụp chưa tạo.

## 5. Nội dung cần cập nhật trong Word

| Chương | Nội dung |
|---|---|
| 3 | Mục 3.4 tách 12/12 API khỏi 03/12 GUI; nêu guest rỗng và ngày seed của Query 06. Mục 3.5 ghi chín JSON index cộng primary, ba _explain, fallback và giới hạn phép đo. Mục 3.4.13 gọi hai view là mã mẫu/chưa kiểm chứng đến khi có response thực tế. |
| 4 | Phân biệt database demo với database ứng dụng; 27 order/58 documents thuộc fixture lịch sử. Nếu xác minh view, bổ sung asset design doc và bằng chứng. |
| 5 | Bảng kiểm thử ghi 12 API HTTP 200, ba GUI với results/docs examined, ba _explain; hai view ghi “chưa kiểm chứng”. Không dùng thời gian giữa các selector khác nhau để khẳng định tăng tốc. |
| 6 | Kết luận nêu giới hạn phạm vi, guest fixture rỗng, thiếu index riêng cho delivery status/guest, hai view và CouchDB hiện tại chưa kiểm chứng. |

## 6. Chưa hoàn thành hoặc chưa kiểm chứng

1. Không có xác minh live mới ngày 27/10/2026 cho query, index, _explain, số document hoặc hai view; không chạy lại theo yêu cầu.
2. Chưa có biên bản GUI riêng cho Query 01, 03–10; chỉ ba query GUI ghi trong Phase 07.
3. Chưa xác nhận `_design/order_statistics`, response hai view hoặc ảnh gốc có ngữ cảnh.
4. Chưa đối chiếu rubric PDF trong lần tổng hợp này; không khẳng định đạt mọi tiêu chí giảng viên.

**Bàn giao cho biên tập Word:** giữ nhãn “kết quả lịch sử” và “chưa kiểm chứng” ở đúng chỗ.
