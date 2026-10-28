# MapReduce Views

Cơ sở dữ liệu: `retail_order_delivery_demo`. Các số liệu được đối chiếu với lọc và tổng hợp độc lập bằng PHP trên order trong DB demo.

| View | Map | Reduce | Runtime kiểm tra |
|---|---|---|---|
| `order_count_by_status` | `function (doc) { if (doc.type === 'order' && typeof doc.status === 'string') { emit(doc.status, 1); } }` | `_count` | PASS |
| `order_total_by_status` | `function (doc) { if (doc.type === 'order' && typeof doc.status === 'string' && doc.totals && typeof doc.totals.grand_total === 'number') { emit(doc.status, doc.totals.grand_total); } }` | `_sum` | PASS |
| `orders_by_status` | `function (doc) { if (doc.type === 'order' && typeof doc.status === 'string' && doc.totals && typeof doc.totals.grand_total === 'number') { emit(doc.status, doc.totals.grand_total); } }` | `_sum` | PASS |
| `orders_by_month` | `function (doc) { if (doc.type === 'order' && typeof doc.ordered_at === 'string' && doc.totals && typeof doc.totals.grand_total === 'number') { emit([parseInt(doc.ordered_at.substring(0, 4), 10), parseInt(doc.ordered_at.substring(5, 7), 10)], doc.totals.grand_total); } }` | `_stats` | PASS, PASS |
| `orders_by_month_total_sum` | `function (doc) { if (doc.type === 'order' && typeof doc.ordered_at === 'string' && doc.totals && typeof doc.totals.grand_total === 'number') { emit(doc.ordered_at.substring(0, 7), doc.totals.grand_total); } }` | `_sum` | được kiểm tra qua view tương ứng |
| `top_products` | `function (doc) { if (doc.type === 'order' && Array.isArray(doc.items)) { doc.items.forEach(function (item) { if (item && typeof item.product_id === 'string' && typeof item.quantity === 'number') { emit(item.product_id, item.quantity); } }); } }` | `_sum` | PASS |
| `revenue_delivered_by_month` | `function (doc) { if (doc.type === 'order' && doc.status === 'delivered' && typeof doc.ordered_at === 'string' && doc.totals && typeof doc.totals.grand_total === 'number') { emit([parseInt(doc.ordered_at.substring(0, 4), 10), parseInt(doc.ordered_at.substring(5, 7), 10)], doc.totals.grand_total); } }` | `_sum` | PASS |
| `orders_by_customer` | `function (doc) { if (doc.type === 'order' && doc.customer && typeof doc.customer.customer_id === 'string') { emit(doc.customer.customer_id, {status: doc.status, ordered_at: doc.ordered_at}); } }` | `không có` | PASS |

## URL dùng trong Fauxton

Trong Fauxton mở database → **Design Documents** → `_design/order_statistics` → chọn view → chỉnh Query Options như dưới và bấm **Run**.

| View | URL/Query Options | Kết quả kỳ vọng từ runtime |
| order_count_by_status | `/_design/order_statistics/_view/order_count_by_status?group=true` | {"cancelled":7,"confirmed":11,"delivered":10,"packing":11,"pending":11,"shipping":15} |
| order_total_by_status | `/_design/order_statistics/_view/order_total_by_status?group=true` | {"cancelled":7812000,"confirmed":14238000,"delivered":11690000,"packing":12713000,"pending":11690000,"shipping":17807000} |
| orders_by_month (năm) | `/_design/order_statistics/_view/orders_by_month?group=true&group_level=1` | Một hàng theo năm với `_stats` count/sum/min/max/sumsqr |
| orders_by_month (tháng) | `/_design/order_statistics/_view/orders_by_month?group=true&group_level=2` | {"[2026,10]":{"count":11,"sum":12747000,"min":294000,"max":2219000,"sumsqr":18855845000000},"[2026,6]":{"count":14,"sum":14910000,"min":218000,"max":2345000,"sumsqr":21267422000000},"[2026,7]":{"count":13,"sum":16927000,"min":302000,"max":2051000,"sumsqr":25618623000000},"[2026,8]":{"count":14,"sum":15805000,"min":176000,"max":1967000,"sumsqr":22021411000000},"[2026,9]":{"count":13,"sum":15561000,"min":260000,"max":2171000,"sumsqr":22678223000000}} |
| top_products | `/_design/order_statistics/_view/top_products?group=true` | Quantity được cộng theo từng product_id; xem `views_facts.json` |
| revenue_delivered_by_month | `/_design/order_statistics/_view/revenue_delivered_by_month?group=true&group_level=2` | {"2026-10":4935000,"2026-9":6755000} |
| orders_by_customer | `/_design/order_statistics/_view/orders_by_customer?reduce=false&include_docs=true&startkey="DEMO-C001"&endkey="DEMO-C015"` | 50 documents có customer_id trong khoảng |

View `orders_by_month_total_sum` giữ lại hợp đồng cũ của view `orders_by_month` trước Phase C (key `YYYY-MM`, reduce `_sum`). Các view cũ `orders_by_status` cũng được giữ nguyên; hai tên view lịch sử `order_count_by_status` và `order_total_by_status` hiện diện tương thích.

Kết quả kiểm tra từng view:

- **PASS** `order_count_by_status group=true`.
- **PASS** `order_total_by_status group=true`.
- **PASS** `orders_by_status compatibility group=true`.
- **PASS** `orders_by_month group_level=1`.
- **PASS** `orders_by_month group_level=2`.
- **PASS** `top_products group=true`.
- **PASS** `revenue_delivered_by_month group_level=2`.
- **PASS** `orders_by_customer reduce=false include_docs startkey/endkey`.
