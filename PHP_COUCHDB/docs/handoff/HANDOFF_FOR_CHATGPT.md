# Tài liệu bàn giao cho ChatGPT chỉnh sửa báo cáo đồ án

> Tài liệu tự chứa để dùng cùng báo cáo Word cũ. Các số liệu dưới đây lấy từ facts và evidence runtime trong repo; không cần mở đường dẫn nguồn để hiểu nội dung. “Giai đoạn 1/2/3/4” dùng theo quy ước bàn giao, không theo số trong tên thư mục. Chỉ dẫn “Giai đoạn 2 chưa làm” là trạng thái, không phải nội dung lý thuyết.

## 0. Tổng quan

**Đề tài:** “Tìm hiểu Fauxton để quản trị CSDL tài liệu CouchDB cho hệ thống quản lý đơn hàng và giao hàng của cửa hàng bán lẻ trực tuyến”. Fauxton là giao diện quản trị và thực hành; database demo hiện hành là `retail_order_delivery_demo`. Runtime được ghi nhận: CouchDB **3.5.0**, PHP **8.3**, Docker Compose. URL local: ứng dụng `http://127.0.0.1:8081`; Fauxton `http://127.0.0.1:5984/_utils/`. Các cổng bind mặc định trên loopback; credentials do người dùng cấu hình trong `.env`, không chép vào báo cáo. Hệ thống có `app` và `couchdb` service.

**Số liệu then chốt — một nguồn sự thật cho bản Word:**

| Chỉ tiêu | Số liệu dùng trong báo cáo |
|---|---:|
| Tổng document database demo | 150 |
| Business / design document | 147 / 3 |
| Theo type | cart 8; customer 15; order 65; product 30; review 20; shipping_method 3; staff 3; voucher 3 |
| Order theo status | cancelled 7; confirmed 11; delivered 10; packing 11; pending 11; shipping 15 |
| Guest order | 15 |
| Delivery status | created 2; failed_delivery 3; in_transit 5; out_for_delivery 2; picked_up 3 |
| JSON Mango index | 15; endpoint `_index` trả 16 hàng nếu tính hàng đặc biệt `_all_docs` |
| MapReduce view trong design document thống kê | 8 |
| Mango query cơ bản / nâng cao | 12 / 8 |
| Nhóm kiểm thử PASS | seed verify: 150 document; query cơ bản: 12/12; query nâng cao: 8/8; view: 8/8; Fauxton feature scripts D1–D5: 5/5; replication backup: PASS; JSON restore: PASS; CSV import/export: PASS; admin HTTP: 25/25; validator: 6/6 |

Không cộng các nhóm thành một tổng duy nhất vì một số nhóm kiểm tra cùng một luồng ở nhiều lớp. Số truy vấn qua GUI không có biên bản ảnh xác nhận; không suy ra “đã chạy trên GUI” chỉ từ kết quả API/script.

**Bảy commit khối A–G được yêu cầu trong prompt:**

| Khối | Commit | Nội dung |
|---|---|---|
| A | `e17b7ae` | Index guest orders và kiểm chứng phân trang bookmark. |
| B | `58a249c` | Tám truy vấn Mango nâng cao, index và evidence độc lập. |
| C | `aa3ec8b` | MapReduce views và dữ liệu kiểm chứng. |
| D | `c1fcc1e` | Script tái lập các chức năng Fauxton: replication, conflict, attachment, security, compaction/delete. |
| E | `d6c175a` | Backup/restore, replication backup và CSV import/export. |
| F | `8c78516` | Trang quản trị users/backup và kiểm thử HTTP. |
| G | `5f178bf` | Tổng hợp evidence runtime Giai đoạn 1–4. |

Các commit này là lịch sử khối công việc kỹ thuật. Repo còn có commit đóng điều tra sai lệch lịch sử và commit tài liệu báo cáo được tạo sau đó; không thay các hash A–G bằng chúng.

### Lưu ý tên giai đoạn

Thư mục `docs/phase1`, `docs/phase3`, `docs/phase4` là nhãn triển khai/evidence trong repo. Theo quy ước của lần bàn giao này, `docs/phase3` chứa cả truy vấn nâng cao/views/Fauxton **và** backup/restore/CSV; đây là phần việc Giai đoạn 3 và Giai đoạn 4. Giai đoạn 2 là phần lý thuyết và chưa làm. Không bê nhãn thư mục thành số chương hoặc tuyên bố Giai đoạn 2 đã hoàn thành.

## 1. Giai đoạn 1 – Dữ liệu và index

### Dataset và quy trình

| Type | Số document |
|---|---:|
| cart | 8 |
| customer | 15 |
| order | 65 |
| product | 30 |
| review | 20 |
| shipping_method | 3 |
| staff | 3 |
| voucher | 3 |
| **Business / design / tổng** | **147 / 3 / 150** |

Lệnh seeder hiện dùng: `docker compose exec -T app composer db:seed-demo`; verifier: `docker compose exec -T app composer seed:verify`. Tài liệu runtime ghi một lượt xác minh dùng `db:seed-demo -- --recreate`; tùy chọn đó tạo lại dữ liệu demo và **không** phải bước mặc định khi nhóm chỉ muốn đọc/kiểm tra. `seed:verify` xác nhận số document và phân bố type/status/guest; runtime report ghi PASS với 150 document, 65 order và 15 guest order. Cài index: `composer catalog:indexes`; query evidence được tạo/đọc bằng script Phase 1.

### Phân bố order

| Status | Số đơn |
|---|---:|
| cancelled | 7 |
| confirmed | 11 |
| delivered | 10 |
| packing | 11 |
| pending | 11 |
| shipping | 15 |

| Delivery status | Số đơn |
|---|---:|
| created | 2 |
| failed_delivery | 3 |
| in_transit | 5 |
| out_for_delivery | 2 |
| picked_up | 3 |

### Mười lăm index và mục đích

Các trường liệt kê theo thứ tự index. Điều kiện “partial” là selector giới hạn document mà index nhận. Git xác nhận `guest_orders` được thêm ở commit A; `customer_orders_status`, `orders_by_total`, `orders_by_receiver`, `orders_type_scan` được thêm ở commit B. `delivery_status` đang có trong cấu hình hiện hành và Phase 1 facts ghi nhận đối chiếu trước/sau; inventory runtime ban đầu và snapshot Git cũ không thống nhất về thời điểm index được cài. Vì vậy bảng chỉ đánh dấu năm index có diff xác định ở commit A/B; `delivery_status` được mô tả là index hiện hành, không khẳng định lịch sử tạo.

| Index | Fields (thứ tự) | Điều kiện | Mục đích |
|---|---|---|---|
| `active_products` | type, active | type=product và active=true | Danh mục sản phẩm đang hoạt động. |
| `active_reviews` | type, active | type=review và active=true | Review đang hoạt động. |
| `account_usernames` | type, auth.username | type là customer hoặc staff | Tìm tài khoản theo username. |
| `customer_orders` | type, customer.customer_id, ordered_at | type=order | Đơn của khách, có thứ tự thời gian. |
| `guest_orders` **(bổ sung)** | type, guest_order, ordered_at | type=order | Đơn khách vãng lai; giảm examined từ 65 xuống 15, cùng 15 kết quả. |
| `customer_orders_status` **(bổ sung)** | type, customer.customer_id | Không có partial filter | Query khách kết hợp `$or` trạng thái. |
| `orders_by_total` **(bổ sung)** | type, totals.grand_total, ordered_at | type=order | Lọc/sắp xếp khoảng tổng tiền và ngày. |
| `orders_by_receiver` **(bổ sung)** | type, receiver.name | type=order | Tìm order theo tên người nhận. |
| `orders_type_scan` **(bổ sung/đối chứng**; mức “mới” cần phân biệt) | type | Không có partial filter | Truy vấn tổng quát theo type, dùng làm baseline quét trong evidence. |
| `admin_orders` | type, status, ordered_at | type=order | Order admin theo trạng thái, sắp xếp ngày. |
| `admin_orders_by_date` | type, ordered_at | type=order | Order theo khoảng ngày. |
| `delivery_tracking_code` | type, delivery_tracking.tracking_code | type=order | Tra tracking code. |
| `delivery_status` | type, delivery_tracking.status, ordered_at | type=order | Lọc trạng thái giao hàng; Phase 1 facts có đối chiếu hiệu quả trước/sau. |
| `admin_products` | type | type=product | Liệt kê sản phẩm quản trị. |
| `admin_reviews` | type | type=review | Liệt kê review quản trị. |

**Ghi chú kiểm kê index:** `[CHƯA XÁC MINH]` `docs/phase1/00_inventory.md` và snapshot Git cũ không hoàn toàn nhất quán về thời điểm index status giao hàng xuất hiện/cài đặt; bảng trên mô tả cấu hình demo/evidence hiện tại. Nếu Word cần kể lịch sử tạo index, nhóm hãy xác minh thêm; nếu không, chỉ gọi đây là index hiện hành.

### Mười hai Mango query cơ bản

`docs_examined` và thời gian là tổng qua các trang đã đọc. `PASS` nghĩa kết quả được kiểm chứng theo facts hiện hành.

| Mã | Mục đích / selector tóm tắt | Index `_explain` | Kết quả | docs_examined | ms | Trạng thái |
|---:|---|---|---:|---:|---:|---|
| 01 | Order có status pending | `admin_orders` | 11 | 12 | 65.224 | PASS |
| 02 | Order có status shipping | `admin_orders` | 15 | 15 | 8.334 | PASS |
| 03 | Order có status delivered | `admin_orders` | 10 | 10 | 6.748 | PASS |
| 04 | Order có status cancelled | `admin_orders` | 7 | 7 | 5.986 | PASS |
| 05 | `customer.customer_id=DEMO-C001` | `customer_orders` | 4 | 4 | 4.277 | PASS |
| 06 | `ordered_at` từ 2026-06-01 đến trước 2027-01-01 | `admin_orders_by_date` | 65 | 67 | 28.232 | PASS |
| 07 | Pending + COD + chưa thanh toán | `admin_orders` | 8 | 11 | 6.644 | PASS |
| 08 | `guest_order=true` | `guest_orders` | 15 | 15 | 8.964 | PASS |
| 09 | Delivery `in_transit` | `delivery_status` | 5 | 5 | 5.193 | PASS |
| 10 | Delivery `out_for_delivery` | `delivery_status` | 2 | 2 | 4.835 | PASS |
| 11 | Delivery `failed_delivery` | `delivery_status` | 3 | 3 | 5.325 | PASS |
| 12 | Tracking code `DEMO-TRK-035` | `delivery_tracking_code` | 1 | 1 | 4.093 | PASS |

Selector cốt lõi lần lượt là các điều kiện type=`order` cộng status/customer/date/guest/tracking như mô tả; payload đầy đủ đã có trong nguồn evidence. Query 06 dùng `limit=25`, `use_index=admin_orders_by_date`, sort `ordered_at asc`, `execution_stats=true`. Phân trang bookmark trả 65 kết quả qua **3 trang có dữ liệu (25, 25, 15)**; facts đếm thêm trang đọc kết thúc không có kết quả. Điều này chứng minh phân trang API theo bookmark, không chứng minh ảnh Fauxton đã được chụp.

### Index trước/sau

| Trường hợp | Trước: index / docs_examined | Sau: index / docs_examined | Kết quả |
|---|---|---|---|
| Guest orders | `orders_type_scan` / 65 | `guest_orders` / 15 | 15 / 15 |
| Delivery in transit | `orders_type_scan` / 65 | `delivery_status` / 5 | 5 / 5 |
| Delivery out for delivery | `orders_type_scan` / 65 | `delivery_status` / 2 | 2 / 2 |
| Delivery failed | `orders_type_scan` / 65 | `delivery_status` / 3 | 3 / 3 |

### Sai lệch lịch sử — điều tra đã đóng

Tài liệu cũ có hai kết quả GUI/API không khớp cho query 01 và query 06. `[CHƯA XÁC MINH]` Payload Fauxton lịch sử chưa có nên nguyên nhân không xác định; theo quyết định của người dùng, không tiếp tục điều tra và không đưa các con số lịch sử vào bảng kết quả mới. Trên database demo mới, cách kiểm chứng trong tương lai là chạy `_explain` và `_find` với cùng selector/index/sort/limit rồi đối chiếu số ID trả về với API và query count độc lập. Đây là hướng xác nhận kết quả mới, không phải kết luận về nguyên nhân lịch sử. Báo cáo mới chỉ nên trình bày kết quả runtime hiện hành ở bảng trên.

## 2. Giai đoạn 3 – Truy vấn nâng cao, view, Fauxton

### Tám query nâng cao

Số lượng kết quả và `docs_examined` đối chiếu từ `advanced_query_facts.json`; độc lập bằng lọc/đếm PHP, `ids_match=true`, không thiếu/thừa ID; mọi trường hợp PASS.

| Mã | Mục đích | Toán tử | Index | Kết quả | docs_examined | Kiểm chứng độc lập |
|---:|---|---|---|---:|---:|---|
| 13 | Đơn pending/confirmed của khách | `$or` | `customer_orders_status` | 1 | 7 | Bộ lọc PHP, IDs khớp |
| 14 | Lọc nhiều trạng thái, loại cancelled | `$in`, `$nin` | `admin_orders` | 37 | 0 | Bộ lọc PHP, IDs khớp |
| 15 | Một item có quantity ≥2 và unit_price >200000 | `$elemMatch`, `$gte`, `$gt` | `orders_type_scan` | 48 | 66 | Bộ lọc PHP, IDs khớp |
| 16 | Khoảng tổng tiền + ngày, sort tổng tiền | `$gt`, `$lte`, `$gte`, `$lt`, sort | `orders_by_total` | 1 | 2 | Bộ lọc PHP, IDs khớp |
| 17 | Có `delivery_tracking`; có probe `$exists=false` | `$exists` | `orders_type_scan` | 15 | 82 | Bộ lọc PHP, IDs khớp |
| 18 | Tìm tên sản phẩm bằng regex | `$regex` | `active_products` | 5 | 55 | Bộ lọc PHP, IDs khớp |
| 19 | COD và không cancelled, chiếu fields/phân trang | `$not`, `$and` | `admin_orders` | 40 | 68 | Bộ lọc PHP, IDs khớp |
| 20 | Tra tên người nhận lồng nhau | nested field, `$eq` | `orders_by_receiver` | 15 | 15 | Bộ lọc PHP, IDs khớp |

`execution_time_ms` biến động theo lần chạy; nếu đưa thời gian vào Word, chỉ dùng số trong facts cùng ngày/mốc được trích dẫn, không diễn giải như benchmark cố định. Hai ví dụ payload JSON nguyên từ request:

**Query 13 — `$or` theo khách và trạng thái:**
```json
{"selector":{"type":{"$eq":"order"},"customer.customer_id":{"$eq":"DEMO-C001"},"$or":[{"status":{"$eq":"pending"}},{"status":{"$eq":"confirmed"}}]},"use_index":["_design/catalog_indexes","customer_orders_status"],"fields":["_id","customer","status","ordered_at","totals.grand_total"],"limit":25,"execution_stats":true}
```

**Query 15 — `$elemMatch` trong items:**
```json
{"selector":{"type":{"$eq":"order"},"items":{"$elemMatch":{"quantity":{"$gte":2},"unit_price":{"$gt":200000}}}},"use_index":["_design/catalog_indexes","orders_type_scan"],"fields":["_id","status","items","totals.grand_total"],"limit":50,"execution_stats":true}
```

**Query 18 — regex trên sản phẩm active:**
```json
{"selector":{"type":{"$eq":"product"},"active":{"$eq":true},"name":{"$regex":"^Sản phẩm Demo 0[1-5]$"}},"use_index":["_design/catalog_indexes","active_products"],"fields":["_id","name","category","variants"],"limit":25,"execution_stats":true}
```

### MapReduce views

Design document hiện có **8 view** được liệt kê bên dưới. Hai hợp đồng cũ được giữ tương thích: `orders_by_status` và hợp đồng monthly sum cũ dưới tên `orders_by_month_total_sum`. `orders_by_month` hiện emit key `[year, month]` và reduce `_stats`; không nhầm tên view cũ với hợp đồng mới.

| View | Map (rút gọn chính xác điều kiện/giá trị) | Reduce | Cách gọi / kết quả runtime |
|---|---|---|---|
| `order_count_by_status` | emit `status, 1` cho order có status string | `_count` | `group=true`; kết quả theo status bằng phân bố order ở mục 1 |
| `order_total_by_status` | emit `status, totals.grand_total` nếu numeric | `_sum` | `group=true`; cancelled 7,812,000; confirmed 14,238,000; delivered 11,690,000; packing 12,713,000; pending 11,690,000; shipping 17,807,000 VND |
| `orders_by_status` | Map cùng điều kiện status + grand_total numeric | `_sum` | `group=true`; compatibility, PASS |
| `orders_by_month` | emit `[parseInt(YYYY),parseInt(MM)]`, grand_total | `_stats` | `group=true&group_level=1` cho năm; `group=true&group_level=2` cho tháng; PASS |
| `orders_by_month_total_sum` | emit `YYYY-MM`, grand_total | `_sum` | Giữ hợp đồng monthly sum cũ; kiểm tra qua view tương ứng |
| `top_products` | với từng order item hợp lệ emit `product_id, quantity` | `_sum` | `group=true`; cộng quantity theo product_id; PASS |
| `revenue_delivered_by_month` | order delivered: emit `[year,month]`, grand_total | `_sum` | `group=true&group_level=2`; 2026-10 = 4,935,000; 2026-9 = 6,755,000 VND |
| `orders_by_customer` | emit `customer_id, {status, ordered_at}` | Không reduce | `reduce=false&include_docs=true&startkey="DEMO-C001"&endkey="DEMO-C015"`; 50 đơn; PASS |

Runtime summary ghi 65 order, tổng grand_total **75,950,000 VND**. Tám kiểm tra view PASS: count by status; total by status; compatibility `orders_by_status`; monthly `group_level=1`; monthly `group_level=2`; top products; delivered revenue by month; customer lookup `reduce=false` có include_docs/startkey/endkey.

**Map/reduce đầy đủ (trích design document):**

`order_count_by_status` — reduce `_count`:
```javascript
function (doc) { if (doc.type === 'order' && typeof doc.status === 'string') { emit(doc.status, 1); } }
```

`order_total_by_status` — reduce `_sum`:
```javascript
function (doc) { if (doc.type === 'order' && typeof doc.status === 'string' && doc.totals && typeof doc.totals.grand_total === 'number') { emit(doc.status, doc.totals.grand_total); } }
```

`orders_by_status` — reduce `_sum`:
```javascript
function (doc) { if (doc.type === 'order' && typeof doc.status === 'string' && doc.totals && typeof doc.totals.grand_total === 'number') { emit(doc.status, doc.totals.grand_total); } }
```

`orders_by_month` — reduce `_stats`:
```javascript
function (doc) { if (doc.type === 'order' && typeof doc.ordered_at === 'string' && doc.totals && typeof doc.totals.grand_total === 'number') { emit([parseInt(doc.ordered_at.substring(0, 4), 10), parseInt(doc.ordered_at.substring(5, 7), 10)], doc.totals.grand_total); } }
```

`orders_by_month_total_sum` — reduce `_sum`:
```javascript
function (doc) { if (doc.type === 'order' && typeof doc.ordered_at === 'string' && doc.totals && typeof doc.totals.grand_total === 'number') { emit(doc.ordered_at.substring(0, 7), doc.totals.grand_total); } }
```

`top_products` — reduce `_sum`:
```javascript
function (doc) { if (doc.type === 'order' && Array.isArray(doc.items)) { doc.items.forEach(function (item) { if (item && typeof item.product_id === 'string' && typeof item.quantity === 'number') { emit(item.product_id, item.quantity); } }); } }
```

`revenue_delivered_by_month` — reduce `_sum`:
```javascript
function (doc) { if (doc.type === 'order' && doc.status === 'delivered' && typeof doc.ordered_at === 'string' && doc.totals && typeof doc.totals.grand_total === 'number') { emit([parseInt(doc.ordered_at.substring(0, 4), 10), parseInt(doc.ordered_at.substring(5, 7), 10)], doc.totals.grand_total); } }
```

`orders_by_customer` — không có reduce:
```javascript
function (doc) { if (doc.type === 'order' && doc.customer && typeof doc.customer.customer_id === 'string') { emit(doc.customer.customer_id, {status: doc.status, ordered_at: doc.ordered_at}); } }
```

Kết quả monthly `_stats` theo tháng trong runtime: `[2026,6]` count 14, sum 14,910,000, min 218,000, max 2,345,000; `[2026,7]` 13, 16,927,000, 302,000, 2,051,000; `[2026,8]` 14, 15,805,000, 176,000, 1,967,000; `[2026,9]` 13, 15,561,000, 260,000, 2,171,000; `[2026,10]` 11, 12,747,000, 294,000, 2,219,000 (mỗi nhóm: count, sum VND, min, max; `sumsqr` nằm trong runtime facts). `top_products` có 30 keys; 10 lượng cao nhất: `product:DEMO-P005` 21; `product:DEMO-P011` 21; `product:DEMO-P017` 21; `product:DEMO-P023` 18; `product:DEMO-P029` 18; `product:DEMO-P002` 15; `product:DEMO-P001` 14; `product:DEMO-P025` 14; `product:DEMO-P007` 12; `product:DEMO-P008` 12. `[CHƯA XÁC MINH]` Hợp đồng compatibility `orders_by_month_total_sum` không có kết quả riêng trong danh sách 8 check; không khẳng định đã được kiểm tra riêng.

### Chức năng Fauxton và bằng chứng

Các kết quả dưới đây đến từ script/API runtime, còn cột thao tác UI là trình tự để nhóm tái hiện trên Fauxton. Tài liệu ảnh ghi **chưa chụp ảnh**; không trình bày đây là screenshot GUI đã nghiệm thu. `[CHƯA XÁC MINH]` Số query thực sự đã chạy trên Fauxton chưa có biên bản ảnh/log.

| Chức năng | Lệnh / bằng chứng số liệu | Thao tác tương ứng trên Fauxton |
|---|---|---|
| Replication one-shot và continuous | `php scripts/demo_replication.php`; source/target 150 docs; 149 writes, 0 error; normalized SHA-256 mismatch 0; continuous job xuất hiện trong scheduler rồi cancel `ok=true`; scratch cleanup PASS | Replication → source/target → chạy one-shot; kiểm tra số docs; bật continuous, xem job rồi cancel. Validator design doc cần cài lên target theo hướng dẫn sau replicate. |
| Conflict do replication | `php scripts/demo_replication_conflict.php`; winner `edited-on-right`; losing leaf revision xóa; `_conflicts` rỗng sau xử lý | Hai DB scratch; tạo cùng document, replicate, sửa độc lập từng bên với `_rev` mới, replicate lại, xem `?conflicts=true`, xóa leaf revision thua. |
| Attachment | `php scripts/demo_attachment.php`; PNG 1×1, 68 bytes; digest `md5-WY4oUadFjaXDQdG7dbQsYQ==`; xóa trả HTTP 200, document không còn attachment | Document → Add Attachment → upload PNG `image/png`; mở rồi xóa attachment. Ảnh của ứng dụng hiện lưu ở storage ngoài và giữ đường dẫn/tham chiếu. |
| `_security` | `php scripts/demo_security.php`; member đọc HTTP 200; outsider đọc 403; member sửa design doc 403; user/DB thử nghiệm được dọn | Database → Security; thêm member thử nghiệm, để admins thử nghiệm rỗng; đăng nhập kiểm tra quyền đọc/sửa. Không chụp credential. |
| Compaction + delete DB | `php scripts/demo_compaction.php`; 51 revisions; `sizes.active` 3195→2437 bytes; `sizes.file` 225629→33123 bytes; xóa HTTP 200, GET sau xóa 404 | Tạo scratch DB và document nhiều revision; Overview ghi size; Compact, chờ xong, ghi size; Delete Database. Runtime không trả alias `data_size`/`disk_size`, dùng `sizes.active`/`sizes.file`. |
| Config / Active Tasks UI | `[CHƯA XÁC MINH]` không thấy evidence thao tác Fauxton Config. Trong replication runtime có một job ở `_scheduler/jobs`, `_active_tasks` ghi 0 tại lần đọc; job continuous được cancel. | Nhóm chụp Fauxton → Configuration/Active Tasks nếu thực sự trình diễn; không tuyên bố Config đã thực hành nếu chưa có ảnh/log. |

## 3. Giai đoạn 4 – Backup/Restore/Import/Export và ứng dụng

### Backup, replication backup, restore

Lệnh JSON backup: `composer db:backup -- <file.json>`; verify: `composer db:backup-verify -- <file.json>`; restore: `composer db:restore -- <file.json> <target_test_or_demo_database>`; evidence đầy đủ qua `scripts/verify_phase3_restore.php`. Backup runtime `phase3-evidence-20261028.json` dài 155,365 bytes.

Backup JSON format_version 1 gồm `source_database`, `created_at`, `document_count`, `documents` (lấy bằng `_all_docs?include_docs=true&attachments=true`) và `security`. Vì request lấy `attachments=true`, document/attachment data được đọc theo cách CouchDB trả; không nói file chỉ chứa `_id`. Backup không lưu revision tree đầy đủ; restore bỏ `_rev`, `_revisions`, conflict/local revision metadata, không restore tombstone đã xóa, sequence, cấu hình toàn node hay lịch sử revision. Đây là logical backup của database documents và security, không phải backup toàn máy CouchDB.

Restore verify so nội dung từng document bằng SHA-256 JSON đã chuẩn hóa: loại `_rev`, revision-local fields và attachment `revpos`; báo danh sách missing/extra/different IDs. Không chỉ so `_id`. Kết quả: 150/150, missing/extra/different = 0/0/0; 3 design docs; 15 Mango index tương ứng, 16 `_index` rows nếu tính `_all_docs`; `_security` parity PASS; validator probe HTTP 403; target test bị dọn sau verify.

Replication backup: `php scripts/backup_replication.php`; phát lại document không gồm validator design, sau đó copy design doc nguyên bản và `_security`; source/target 150/150, missing/extra/different rỗng, `_security` parity PASS; scratch cleanup PASS.

### CSV

Import chạy bằng `php scripts/verify_csv_exchange.php` trên DB scratch có validator. File mẫu `products_import.csv` có 4 dòng dữ liệu: **3 nhập thành công**, **1 dòng bị validator từ chối** tại dòng 5, mã `CSV-BAD01`, HTTP logic `forbidden`, lý do: `product variant has invalid id, size, price, stock, or active flag`; cleanup PASS.

Export `composer export:orders-csv -- orders_phase3_20261028.csv`; cột `id,date,customer,status,total,payment,delivery_status`. Export trả 65 dòng data, khớp 65 kết quả `_find`, index `admin_orders`, `docs_examined=65`. Exporter từ chối ghi đè file đã có.

### Trang Backup và Admin Users

Kiểm thử HTTP tích hợp `tests/admin_users_integration.php`: **25/25 PASS**. Manager login, tìm/lọc account, xem detail an toàn không lộ password hash/auth object, sửa profile, stale `_rev` trả 409 nhưng không ghi đè dữ liệu mới, lock/unlock, CSRF, tạo staff; backup qua HTTP, tải JSON, từ chối thiếu CSRF, traversal filename và quyền staff/customer. Non-manager bị 403 tại users/backup/create/download. Trang backup dùng ManagerGuard, database backup bị giới hạn hậu tố `_test`/`_demo`; file đặt tên duy nhất, publish không ghi đè.

**Lỗi đã sửa:** cập nhật hồ sơ trước đây thiếu `_rev` và chưa phát hiện stale revision. Đã thêm revision vào form, so sánh revision trong service và trả HTTP 409 khi dữ liệu đổi đồng thời; test xác nhận dữ liệu cập nhật mới còn nguyên.

**Danh sách 25 kiểm tra HTTP:** evidence JSON lưu tên ca và PASS/FAIL nhưng không lưu status cho từng record. Status dưới đây chỉ ghi số mà assertion trong integration script kiểm tra; ca không assert/lưu status cụ thể được đánh `[CHƯA XÁC MINH]` thay vì suy đoán.

| # | Tên ca | HTTP | Kết quả |
|---:|---|---:|---|
| 1 | Manager đăng nhập HTTP | 303 | PASS |
| 2 | Manager tìm kiếm và lọc customer đúng | 200 | PASS |
| 3 | Manager xem chi tiết tài khoản an toàn | 200 | PASS |
| 4 | Manager sửa hồ sơ với revision hiện hành | 303 | PASS |
| 5 | Hồ sơ sau sửa đã được lưu | [CHƯA XÁC MINH] (status GET không được assertion ghi nhận) | PASS |
| 6 | Revision cũ trả xung đột | 409 | PASS |
| 7 | Sửa stale không ghi đè hồ sơ mới | [CHƯA XÁC MINH] (status GET không được assertion ghi nhận) | PASS |
| 8 | Manager khóa tài khoản | 303 | PASS |
| 9 | Manager mở khóa tài khoản | 303 | PASS |
| 10 | Thiếu CSRF không đổi trạng thái | 303 | PASS |
| 11 | Manager tạo tài khoản Staff active | 303 | PASS |
| 12 | Tạo backup thiếu CSRF | 403 | PASS |
| 13 | Manager tạo backup tên duy nhất | 303 | PASS |
| 14 | Manager tải JSON backup an toàn | 200 | PASS |
| 15 | Traversal filename bị từ chối | [CHƯA XÁC MINH] (assertion chấp nhận 400 hoặc 404) | PASS |
| 16 | Staff đăng nhập HTTP | 303 | PASS |
| 17 | Staff bị từ chối `/admin/users` | 403 | PASS |
| 18 | Staff bị từ chối `/admin/backup` | 403 | PASS |
| 19 | Staff không tạo backup | 403 | PASS |
| 20 | Staff không tải backup | 403 | PASS |
| 21 | Customer đăng nhập HTTP | 303 | PASS |
| 22 | Customer bị từ chối `/admin/users` | 403 | PASS |
| 23 | Customer bị từ chối `/admin/backup` | 403 | PASS |
| 24 | Customer không tạo backup | 403 | PASS |
| 25 | Customer không tải backup | 403 | PASS |

### Validator: đủ 6 ca

| Ca | Request tóm tắt | HTTP | Body trả về | Kết quả |
|---|---|---:|---|---|
| Order status không hợp lệ | pending → invalid_status | 403 | `{"error":"forbidden","reason":"order requires a valid status, receiver, and non-empty items"}` | PASS |
| Rút ngắn delivery history | 2 event → 1 | 403 | `{"error":"forbidden","reason":"delivery history is append-only"}` | PASS |
| Paid → unpaid | paid/true → unpaid/false | 403 | `{"error":"forbidden","reason":"paid orders cannot be marked unpaid"}` | PASS |
| Bỏ qua trạng thái giao | created → in_transit, bỏ picked_up | 403 | `{"error":"forbidden","reason":"delivery status transition is not allowed"}` | PASS |
| Tạo tracking hợp lệ | order shipping, tracking created đầu tiên | 201 | `{"ok":true,"id":"order:validator-valid","rev":"1-b9b67911434dc4cf0924b5a5c6cbdefa"}` | PASS |
| Chuyển giao hợp lệ | created → picked_up, thêm event | 201 | `{"ok":true,"id":"order:validator-valid","rev":"2-7577e4c1a16358e7bdae283618f39dba"}` | PASS |

Validator file được cài trên DB test đầy đủ; dung lượng file nguồn 11,649 bytes. Các response 403/201 là HTTP runtime. Lý do invalid status là thông báo chung cho điều kiện order/status/receiver/items, không diễn giải thành lỗi status riêng.

### Smoke suites và xác minh ứng dụng

Runtime summary có: Compose app/CouchDB healthy; `seed:verify` PASS; `verify_views.php` 8/8 PASS; `verify_advanced_queries.php` 8/8 PASS; D1–D5 5 script PASS và cleanup true; replication backup PASS; JSON restore PASS; CSV PASS; admin HTTP PASS; validator PASS. Số liệu cụ thể được trình bày ở các mục trên.

Các tên script/suite có trong Composer nhưng **không có kết quả chi tiết trong facts bàn giao hiện tại**, nên trạng thái là `[CHƯA XÁC MINH]`: `checkout:smoke`, `checkout:http-smoke`, `catalog:admin-smoke`, `users:admin-smoke`, `auth:jwt-smoke`, `couchdb:failure-smoke`, `db:backup-smoke`, `db:restore-smoke`. Không gộp các tên này vào kết luận PASS 25 ca admin HTTP; đó là suite riêng.

### Điều ứng dụng chưa làm / giới hạn có bằng chứng

- Không có tích hợp shipper, hãng vận chuyển hay GPS được chứng minh; `delivery_tracking` là trạng thái/tracking nội bộ.
- Thanh toán demo dùng COD và các trạng thái ghi trong order; không có bằng chứng tích hợp payment gateway ngoài.
- `[CHƯA XÁC MINH]` Không tìm thấy evidence deploy public/production; Compose đang bind app và CouchDB trên `127.0.0.1` theo cấu hình mặc định. Chỉ kết luận môi trường bằng chứng hiện có là local.
- Backup JSON là logical database backup, không bao gồm toàn bộ node/volume/configuration và revision tree đầy đủ.
- Ảnh Fauxton chưa được chụp; Config UI chưa có evidence. Nội dung GUI phải được nhóm chụp bổ sung trước khi ghi “đã thực hành trên Fauxton”.
- Không kết luận “ảnh order snapshot bị thiếu” hoặc các giới hạn khác nếu nhóm không đối chiếu giao diện/code đang giao; Word cũ có thể mô tả ảnh ở app ngoài CouchDB.

## 4. Giai đoạn 2 – Trạng thái

**Giai đoạn 2 chưa làm.** Nhóm tự viết, không yêu cầu ChatGPT tự sinh lý thuyết trong lần chỉnh báo cáo này:

- Mở rộng Chương 2 theo rubric: B-tree append-only/MVCC, CAP/eventual consistency, replication, cluster, compaction.
- Bổ sung một cách cài đặt CouchDB thứ hai có thể tái lập.
- So sánh với công cụ cơ sở dữ liệu thứ hai và nêu điểm yếu của Fauxton; không chỉ so với MongoDB Compass.
- Viết mục loại ứng dụng phù hợp với document database/CouchDB.
- Tự chọn và kiểm tra tài liệu tham khảo ngoài tài liệu chính thức.

## 5. Ánh xạ nội dung Word cũ → nội dung thay mới

| Bảng/mục trong Word cũ | Nội dung cũ cần xử lý (tóm tắt) | Hành động và nội dung thay |
|---|---|---|
| Bảng 3.3, 3.5, 3.6 | Index/explain/query efficiency | **THAY** bằng bảng index, query 01–12 và before/after ở mục 1; không giữ screenshot/fallback cũ. |
| Bảng 4.3 | Phân bố dữ liệu | **THAY** bằng phân bố type 150 docs ở mục 0/1. |
| Bảng 4.6 | Số lượng/order trạng thái | **THAY** bằng status distribution ở mục 1. |
| Bảng 4.7 | Delivery states | **THAY** bằng delivery status ở mục 1. |
| Bảng 4.8 | Query Mango cơ bản | **THAY** bằng bảng đủ 12 query ở mục 1. |
| Bảng 4.9 | Index hoặc query efficiency (đối chiếu heading Word) | **THAY** theo bảng 15 index / explain / before-after ở mục 1; nếu không trùng nội dung thì **ĐỐI CHIẾU** với bản Word. |
| Bảng 5.3 | Kết quả kiểm thử ứng dụng | **THAY** bằng nhóm PASS đã có evidence ở mục 3; suite thiếu kết quả giữ `[CẦN BỔ SUNG]`. |
| Bảng 6.1 | Tổng kết/giới hạn | **THAY** bằng kết quả và giới hạn có bằng chứng ở mục 3; nhóm xác nhận ý kiến. |
| Mục 3.4.x | Truy vấn Mango cũ và số liệu GUI/API | **THAY** bằng query 01–12 hiện hành; bỏ ảnh/câu số cũ. |
| Mục 3.5.6 | Explain/execution statistics/index | **THAY** bằng facts `docs_examined`, index hiện hành và before/after ở mục 1. |
| Mục 4.3.4 | Quy mô và phân bố dataset | **THAY** bằng 150/147/3 và bảng distribution ở mục 1. |
| Mục 4.4.x | Import/export, backup, restore | **THAY** bằng kết quả, format, parity và giới hạn backup ở mục 3. |
| Mục 5.2.1 | Luồng/chức năng ứng dụng | **GIỮ** phần còn đúng; **THAY** các tuyên bố kiểm thử bằng evidence quản trị/validator ở mục 3. |
| Mục 5.5.6 | Validation/đảm bảo dữ liệu (đối chiếu heading) | **THAY** bằng 6 ca validator ở mục 3; không ghi chung “đã kiểm tra” không có HTTP/body. |
| Mục 5.6 | Kết quả kiểm thử, tổng hợp suite | **THAY** bằng PASS phân nhóm ở mục 0 và 3; chỗ thiếu kết quả để placeholder. |
| Mục 6.1 | Kết quả đạt được | **THAY** bằng số liệu then chốt và evidence Giai đoạn 1/3/4. |
| Mục 6.2 | Hạn chế | **THAY** bằng giới hạn thật ở mục 3, sau khi nhóm kiểm tra giao diện/code cuối. |
| Mục 6.4 | Kết luận/định hướng theo Word cũ | **THAY** hoặc **GIỮ** câu nào còn đúng; bỏ số liệu và tên DB cũ, chừa mục Giai đoạn 2 cho nhóm. |

## 6. Chuỗi cần xóa hoặc thay trong Word

Các chuỗi bên dưới là chuỗi dò trong bản cũ, **không phải số liệu dùng cho bảng mới**. Xóa/thay khi xuất hiện dưới bất kỳ biến thể viết hoa/thường nào:

- Nhãn thư mục/phase cũ: `phase0*`, `phase03`, `phase06`, `phase07`; thay bằng tên giai đoạn/chương theo cấu trúc cuối, không đưa tên folder vào văn xuôi.
- Đường dẫn máy cá nhân: `D:\PHP_NOSQL` và mọi đường dẫn tuyệt đối tương tự.
- Tên database cũ/không phải demo hiện hành: `shopquan_ao_*`, `*_phase06_*`, `phase03_validator_index_backup_test`, các scratch/test DB; hỏi nhóm nếu cần quyết định giữ tên ứng dụng `WEBTHOITRANG`.
- `retail_order_delivery_test`: đây là tên mặc định/config template trong repo, không phải DB demo dùng cho kết quả mới; chỉ giữ nếu giải thích đúng vai trò config/test sau khi nhóm xác nhận.
- Số cũ cần dò/xóa khỏi nội dung, caption, hình, mục lục và bảng: **27, 21, 58, 60, 61, 347, 348**. Giữ số nào nếu nó xuất hiện với nghĩa khác chỉ sau khi kiểm tra ngữ cảnh và nguồn.
- Cụm tự kiểm toán/giọng ghi nhật ký: “đợt kiểm thử bổ sung”, “lần restore lịch sử”, “đối chiếu chỉ đọc”, “bằng chứng lịch sử”. Viết lại thành kết quả kiểm thử/evidence của bản giao, không kể lịch sử thao tác nhóm.
- Mọi câu đã lỗi thời dạng “chưa kiểm thử”, “cần bổ sung”, “chưa triển khai”: vị trí dò được gồm Chương 5 mục 5.3.4; bảng kết quả backup file ảnh; bảng giới hạn MapReduce; bảng 5.6.1/kết quả admin. Thay bằng trạng thái hiện có ở mục 3 hoặc `[CẦN BỔ SUNG]` nếu thiếu evidence thật. Với ảnh Fauxton và Giai đoạn 2, trạng thái chưa hoàn tất vẫn đúng.
- Thay tên DB trong ảnh Fauxton cũ, caption, URL, lệnh, bảng inventory, ví dụ backup/restore. Ảnh cũ có tên database hoặc query result cũ phải chụp lại.

## 7. Nhận xét giảng viên và cách xử lý

“Đã xử lý bằng code/số liệu?” đánh giá mức có evidence kỹ thuật trong repo, không thay việc nhóm viết nội dung và chụp ảnh.

| Nhận xét | Đã xử lý bằng code/số liệu? | Bằng chứng / phần nhóm còn làm |
|---|---|---|
| M1 Lý thuyết/cài đặt mỏng: Ch2 ~5 trang, thiếu kiến trúc/CAP/replication/cluster/compaction; chỉ cài bằng Docker. | Một phần | Giai đoạn 2 chưa làm; script có replication/compaction demo tại mục 2, repo có Docker. Nhóm tự viết lý thuyết và cách cài thứ hai. |
| M2 Chức năng Fauxton: Replication, Config, Attachments, _security, Active Tasks, xóa DB, compaction chỉ "khảo sát"; Ch3 thiên về Mango. | Một phần | Runtime D1–D5 cho replication, conflict, attachment, security, compaction/delete; UI ảnh chưa chụp. Config UI chưa xác minh; Active Tasks có API evidence gián tiếp. Nhóm chụp Fauxton và mô tả đúng mức đã thực hiện. |
| M3 So sánh: chỉ MongoDB Compass, 6 dòng, thiếu điểm yếu của Fauxton, thiếu công cụ thứ hai. | Không – nhóm tự làm | Giai đoạn 2 chưa làm; nhóm chọn công cụ, viết so sánh và kiểm tra nguồn. |
| M4 Thiếu mục "điểm mạnh với loại ứng dụng nào". | Không – nhóm tự làm | Giai đoạn 2 chưa làm; nhóm viết mục phù hợp ứng dụng. |
| M5 Dữ liệu nhỏ (56 doc), truy vấn "nâng cao" chỉ là lọc $eq/khoảng ngày; số liệu không nhất quán (58/60/61, 347/348, 27 vs 21); truy vấn 01 fallback; failed_delivery quét 58 doc nhưng không sửa; DB demo tạo bằng seeder PHP. | Một phần | Demo mới 150 docs, 65 order; 12 query cơ bản + 8 query nâng cao (gồm `$or`, `$in/$nin`, `$elemMatch`, `$regex`, `$exists`, `$not`, projection/sort); query hiện hành PASS và index có evidence; demo được seeder PHP. Không kết luận nguyên nhân GUI cũ. |
| M6 Backup/restore chỉ script PHP; thiếu replication; thiếu _security/revision; chỉ so khớp _id; trộn nhiều mốc ngày. | Có, ở phạm vi đã thử | Replication backup, JSON security, content hash parity (không chỉ ID), một evidence backup date, restore. Giới hạn revision tree/tombstone được nêu ở mục 3. |
| M7 Chỉ 7/12 query chạy trên GUI; MapReduce chỉ _count/_sum. | Một phần | Script/evidence có 12+8 query; 8 view gồm `_count`, `_sum`, `_stats`, map không reduce. Chưa có ảnh GUI để khẳng định số query chạy trên Fauxton; nhóm cần chụp. |
| M8 Ứng dụng từng là project SQL migrate; cần chứng minh thiết kế lại theo document. | Một phần | Repo hiện có PHP + CouchDB, document schema và seeder; có import snapshot SQL trong lịch sử mã nguồn. Nhóm giải thích ranh giới migration/ứng dụng hiện hành và thiết kế document bằng code/ảnh. |
| M9 Demo: quản trị người dùng tự thừa nhận chưa kiểm thử; backup chỉ bằng CLI; chức năng hỗ trợ thiếu hình. | Một phần | Admin/backup HTTP 25/25 PASS; Fauxton feature/API scripts và manager backup có evidence. Ảnh Fauxton/UI chưa chụp; nhóm bổ sung hình demo thực. |
| M10 Định dạng/trình bày: bìa lặp; thiếu lời cảm ơn/nhận xét GV; style Normal không phải TNR 13/1,5 (một phần chạy 10–10,5pt, dãn dòng 1,0–1,08); văn phong "tự kiểm toán"; tên phase0x và đường dẫn máy cá nhân; ~10 chỗ thú nhận dở dang; TLTK chỉ tài liệu chính thức; 100% đồng loạt trong bảng phân công. | Một phần | Báo cáo V2 có chuẩn format dự kiến và lọc nội dung cũ, nhưng cần render Word kiểm tra; tên/bìa, nhận xét GV, cảm ơn, TLTK độc lập, phân công và tỷ lệ đóng góp do nhóm tự hoàn thiện. |

**Nhóm phải tự làm:** viết Giai đoạn 2; bổ sung/chọn nguồn tham khảo; xác nhận bìa, logo, lời cảm ơn và nhận xét GV; chụp Fauxton; xác nhận hình app; điền phân công/tỷ lệ đóng góp thật; rà định dạng và số trang; xác nhận các mục chưa xác minh. Code không thể thay các việc này.

## 8. Danh sách hình cần có

Kế hoạch dưới đây rút từ danh sách Fauxton screenshot trong evidence. **Không có ảnh Fauxton nào được chụp trong evidence hiện tại**; các mục Fauxton đều “chụp lại”. Hình ứng dụng PHP trong Word cũ chỉ dùng lại sau khi nhóm xác nhận vẫn đúng với code hiện hành và không chứa số liệu/DB cũ.

| Hình đề xuất | Nội dung và thao tác | Database | Trạng thái |
|---|---|---|---|
| 3.1 / `01-demo-database.png` | Danh sách database và Overview, thấy tổng document | `retail_order_delivery_demo` | Chụp Fauxton mới |
| 3.2 / `02-demo-indexes.png` | Manage Indexes, `_design/catalog_indexes`, 15 JSON indexes | `retail_order_delivery_demo` | Chụp Fauxton mới |
| 3.3–3.14 / `03`–`14-query-*.png` | Query 01–12: selector, count, index; query 06 chụp Show more/pagination | `retail_order_delivery_demo` | Chụp Fauxton mới; từng payload lấy từ query JSON hiện hành |
| 3.15 / `15-advanced-query-13-20.png` | Query 13–20, mỗi query chụp riêng nếu không đọc được chung | `retail_order_delivery_demo` | Chụp Fauxton mới |
| 3.16 / `16-order-statistics-views.png` | View group/group_level/reduce=false và kết quả | `retail_order_delivery_demo` | Chụp Fauxton mới |
| 3.17 / `17-full-domain-validator.png` | Mở design doc validator, chỉ xem source, không sửa/lưu | `retail_order_delivery_demo` | Chụp Fauxton mới |
| 3.18 | One-shot và continuous replication rồi cancel | Source demo + target scratch | Chụp Fauxton mới; chạy setup có cleanup |
| 3.19 | Conflict, winner, losing leaf và xử lý `_conflicts` | Hai DB scratch | Chụp Fauxton mới |
| 3.20 | Tạo/mở/xóa attachment, dung lượng/digest | DB scratch | Chụp Fauxton mới |
| 3.21 | `_security`, quyền member/outsider | DB scratch | Chụp Fauxton mới, không chụp thông tin đăng nhập |
| 3.22 | Compaction, size trước/sau, xóa DB | DB scratch | Chụp Fauxton mới |
| 3.23–3.25 | Replication backup, JSON backup, restore parity | Demo/DB scratch | Chụp Fauxton mới; lệnh tạo scratch theo script |
| 3.26–3.27 | CSV import response và export 65 dòng | Scratch/demo | Chụp mới; CSV export là file, không phải ảnh Fauxton |
| `28-admin-users-search.png` | Tìm kiếm/lọc account dưới manager | DB test HTTP | Ảnh Fauxton không áp dụng; nhóm chụp UI PHP mới hoặc duyệt ảnh cũ đúng code |
| `29-admin-user-detail-edit.png` | Chi tiết, sửa profile, stale `_rev` | DB test HTTP | Như trên; không lộ password hash |
| `30-admin-user-status-create.png` | Khóa/mở khóa customer, tạo staff, CSRF | DB test HTTP | Như trên |
| `31-manager-backup.png` | Tạo/tải backup manager, traversal filename | DB test HTTP | Như trên; dùng tên file test không nhạy cảm |
| `32-role-denials.png` | Quyền từ chối staff/customer | DB test HTTP | Như trên |
| `33-validator-invalid-status.png` | Validator từ chối status không hợp lệ | DB test/scratch | Chụp request/response mới hoặc trích evidence; hiện chưa có ảnh |
| `34-validator-delivery-payment.png` | Lịch sử delivery, paid→unpaid, chuyển trạng thái sai | DB test/scratch | Chụp response mới hoặc trích evidence; hiện chưa có ảnh |
| `35-validator-valid-write.png` | Tạo tracking và chuyển trạng thái hợp lệ | DB test/scratch | Chụp response mới hoặc trích evidence; hiện chưa có ảnh |

Hình Fauxton cũ chứa tên database hoặc kết quả truy vấn cũ phải thay, không chỉ sửa caption. Danh sách gợi ý trên giữ chủ đề và nhóm hình, nhóm có thể đánh lại số sau khi hoàn tất Chương 2.

## 9. Yêu cầu định dạng và thang điểm

Yêu cầu khi hoàn thiện Word: **50–60 trang**, Times New Roman 13, giãn dòng 1,5; tối đa 3 thành viên; bố cục bìa → cam đoan → cảm ơn → nhận xét GV → mục lục → danh mục hình/bảng → nội dung. Tài liệu tham khảo phải tự kiểm tra/biên soạn, không sao chép máy móc. Placeholder Giai đoạn 2 không tính là phần đã hoàn thành.

| Tiêu chí thang điểm theo đề | Điểm | Phần báo cáo phục vụ tiêu chí |
|---|---:|---|
| Lý thuyết + cài đặt | 0,5 | Chương 2 và 3.1; Giai đoạn 2 do nhóm viết |
| Chức năng tool | 0,5 | Chương 3: Fauxton, index, views, replication, attachments, security, compaction |
| So sánh | 0,5 | Chương 2; nhóm tự viết công cụ thứ hai và nhược điểm Fauxton |
| Điểm mạnh với loại ứng dụng | 0,5 | Chương 2; nhóm tự viết |
| Thiết kế DB: tạo, insert, query cơ bản, query nâng cao | 4 × 0,5 | Chương 4/Chương dữ liệu theo bố cục nhóm; dataset, seeder, query và index |
| Import/export, backup/restore | 0,5 | Chương 4: JSON, replication backup, restore parity, CSV |
| Query cơ bản trên GUI | 0,5 | Chương 3: query Mango cơ bản và ảnh Fauxton |
| Query nâng cao trên GUI | 0,5 | Chương 3: 8 truy vấn và ảnh Fauxton |
| Kết nối ứng dụng | 0,5 | Chương 5: PHP + CouchDB, luồng nghiệp vụ |
| Demo quản trị người dùng | 0,5 | Chương 5: admin users, 25 HTTP checks |
| Demo thao tác CSDL | 0,5 | Chương 3–4: query/view/backup/restore/CSV |
| Demo quản lý theo chức năng | 0,5 | Chương 5: nghiệp vụ và phân quyền |
| Demo chức năng hỗ trợ | 0,5 | Chương 3/4/5: replication, conflict, validator, backup |
| Báo cáo | 0,5 | Toàn báo cáo: đúng định dạng, số liệu, hình và nguồn |
| Thuyết trình | 0,5 | Slide và demo trực tiếp; nhóm chuẩn bị |
| Nộp | 0,5 | Word + PowerPoint + source đúng yêu cầu |
| Làm việc nhóm | 0,5 | Bảng phân công/tỷ lệ có xác nhận thật |

Phân bổ phần tiêu chí trên giữ đúng tổng theo đề; nhóm xác nhận cách đánh số chương cuối trước khi dẫn chiếu rubric vào Word.

## 10. Câu hỏi cần nhóm trả lời

1. `[CHƯA XÁC MINH]` Các file Laravel/SMART CASUAL ngoài repo PHP + CouchDB là sản phẩm nào, do ai tạo, có thuộc source nộp đồ án NoSQL không?
2. `[CHƯA XÁC MINH]` Tên ứng dụng `WEBTHOITRANG` giữ hay đổi trong báo cáo? Không tự đổi tên project từ tên Composer.
3. `[CHƯA XÁC MINH]` Cung cấp thông tin bìa đúng, logo chuẩn, lời cảm ơn và người nhận xét GV; xác nhận có giữ thông tin bìa Word cũ không.
4. `[CHƯA XÁC MINH]` Tỷ lệ đóng góp và nhiệm vụ từng thành viên là gì? Không điền đồng loạt 100% nếu nhóm chưa xác nhận.
5. `[CHƯA XÁC MINH]` Hình ứng dụng PHP nào trong Word cũ còn đúng với code hiện tại và được phép dùng lại?
6. `[CHƯA XÁC MINH]` Ai chụp và xác nhận các ảnh Fauxton; ai kiểm tra ảnh không còn DB/số liệu cũ?
7. `[CHƯA XÁC MINH]` Ai viết Giai đoạn 2, cài đặt thứ hai, so sánh công cụ, điểm mạnh và tài liệu tham khảo?
8. `[CHƯA XÁC MINH]` `retail_order_delivery_test` còn dùng cho mục đích nào trong báo cáo cuối, hay chỉ là cấu hình test/template?
9. Xác nhận các mục `[CHƯA XÁC MINH]`: Config UI, các smoke suites còn thiếu output, số query thật sự đã chạy trên GUI, và phân loại lịch sử index theo baseline.
10. `[CHƯA XÁC MINH]` Ai render Word cuối, kiểm tra trang 50–60, mục lục, caption, TNR 13/1,5 và xem lại toàn bộ placeholder trước khi nộp?
