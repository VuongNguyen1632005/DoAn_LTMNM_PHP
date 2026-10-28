# Mango queries cho demo Phase 1

Mỗi file JSON chứa trực tiếp request body cho Fauxton `/_find` (hoặc `/_explain`). Chọn đúng database `retail_order_delivery_demo`, mở **Mango Query**, dán JSON vào ô query rồi chạy. Dataset được tạo xác định bởi `scripts/seed_final_demo.php`; tên, email, điện thoại và địa chỉ đều là dữ liệu tổng hợp.

Dataset có 65 order trải trên sáu trạng thái và ít nhất bốn tháng; có guest orders, paid/unpaid payment, COD và retry giao hàng. Seeder cài `_design/catalog_indexes`, `_design/domain_validation` và `_design/order_statistics`.

Query 06 dùng khoảng ngày cố định 2026-09-01 đến trước 2026-11-01; timestamps trong dataset cũng cố định nên kết quả tái lập được. `limit` và `execution_stats` được ghim tường minh trong payload.

Các truy vấn status/date/customer/tracking tương ứng với index `admin_orders`, `admin_orders_by_date`, `customer_orders`, `delivery_tracking_code`. Query 09–11 dùng `delivery_status` trên `(type, delivery_tracking.status, ordered_at)`. Xác nhận lựa chọn thực tế bằng `POST /<database>/_explain` hoặc Fauxton trước khi thuyết trình; số liệu thực tế do `composer phase1:report` ghi lại.
