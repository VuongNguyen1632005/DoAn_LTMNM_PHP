# Biên bản bàn giao Phase 2B — Fauxton và MapReduce

**Cập nhật:** 28/10/2026 (giờ hiển thị trên Fauxton khoảng 01:31; múi giờ chưa được xác minh độc lập)  
**CouchDB:** 3.5.0  
**Database kiểm thử:** shopquan_ao_phase06_demo_test  
**Database ứng dụng:** shopquan_ao_sql_migration_test — không truy vấn hoặc ghi trong đợt kiểm chứng này.

## Kết quả kiểm chứng trực tiếp

| Hạng mục | Trạng thái | Kết quả hiện quan sát |
|---|---|---|
| Database kiểm thử | PASS | Fauxton hiển thị database thử nghiệm; tổng 61 tài liệu. |
| Design document order_statistics | PASS | Hai View tồn tại và chạy được trên database kiểm thử. |
| View order_count_by_status, group=true | PASS | cancelled 2; confirmed 4; delivered 6; packing 4; pending 5; shipping 6. Tổng 27 order. |
| View order_total_by_status, group=true | PASS | cancelled 1.430.000; confirmed 2.870.000; delivered 4.170.000; packing 1.840.000; pending 2.820.000; shipping 3.380.000 VND. |
| Đối chiếu View với order nguồn | PASS | Đã xem 27 order qua Fauxton Mango trên hai trang. Cộng lại totals.grand_total theo status khớp với Sum; đếm status khớp với Count. Phép đối chiếu được tính thủ công từ các hàng hiển thị. |
| Chín JSON index | PASS | Manage Indexes hiển thị account_usernames, active_products, active_reviews, admin_orders, admin_orders_by_date, admin_products, admin_reviews, customer_orders, delivery_tracking_code. Không sửa index. _all_docs là index hệ thống riêng, không nằm trong chín JSON index. |
| Mango GUI 01 — pending | PASS | 5 kết quả; 61 keys và 61 documents examined; 7 ms; Fauxton cảnh báo không có index phù hợp và quét nhiều tài liệu. |
| Mango GUI 02 — shipping | PASS | 6 kết quả; 6 keys và 6 documents examined; 12 ms; admin_orders. Đã chạy mới trên Fauxton sau khi đăng nhập lại. |
| Mango GUI 05 — customer P06-001 | PASS | 5 kết quả; 5 keys và 5 documents examined; 4 ms; customer_orders. |
| Mango GUI 06 — khoảng ngày | PASS | 2026-09-01 inclusive đến trước 2026-11-01; 21 kết quả; 22 keys và 22 documents examined; 9 ms; admin_orders_by_date. |
| Mango GUI 07 — COD pending chưa thanh toán | PASS | 5 kết quả; 5 keys và 5 documents examined; 3 ms; admin_orders. |
| Explain trên Fauxton | PASS | Đã mở Explain của Query 07. Selected Index là json: admin_orders trong _design/catalog_indexes, các trường type, status, ordered_at; không có index phù hợp khác. |
| Ảnh minh chứng | PASS | Có các ảnh Fauxton thật liệt kê ở mục “Tệp minh chứng”. |
| 12 API Mango lịch sử | NOT RUN | Không chạy lại toàn bộ API suite; kết quả lịch sử 12/12 được giữ riêng, không gộp với các lần chạy GUI mới. |
| Query GUI 11 và 12 | NOT RUN mới | Chỉ giữ ghi nhận lịch sử của Phase 07; không chụp ảnh mới cho hai truy vấn này trong đợt hiện tại. |
| Trạng thái Docker/container | BLOCKED | Lệnh chỉ đọc docker ps bị từ chối khi kết nối npipe Docker Desktop Linux Engine; trạng thái container chưa xác minh. |
| Báo cáo Word | NOT RUN | Không chỉnh sửa Word theo yêu cầu. |

## Tệp minh chứng

Thư mục: docs/phase02_fauxton/evidence/

- Mango GUI: mango-query-01-pending.png, mango-query-02-shipping.png, mango-query-05-customer.png, mango-query-06-date-range.png, mango-query-07-cod-pending-unpaid.png.
- Explain: mango-query-07-explain-admin-orders.png.
- Hai View: mapreduce-count-by-status.png, mapreduce-sum-by-status.png.
- Chín index: manage-indexes-1.png, manage-indexes-2.png, manage-indexes-3.png.
- Đối chiếu dữ liệu: order-source-crosscheck.png.
- Fauxton/database: 127.0.0.1_5984__utils_.png.
- Dữ liệu tổng hợp máy đọc được: live-results.json.

Ảnh Query 02 được lưu trong thư mục evidence sau lần chạy trực tiếp mới. Các ảnh chứng minh thao tác GUI, còn JSON tổng hợp ghi lại giá trị và giới hạn đối chiếu.

## Phạm vi và giới hạn

- Chỉ đọc dữ liệu và chạy truy vấn trên shopquan_ao_phase06_demo_test. Không reset, seed, ghi document, chỉnh design document hay thay đổi index.
- Không chạm vào shopquan_ao_sql_migration_test, mã PHP, Word, commit, push hoặc deploy.
- Query 01 quét 61 tài liệu do không dùng index phù hợp; các cảnh báo hiệu năng không bị che giấu.
- Kết quả MapReduce dựa trên 27 order demo thuộc database thử nghiệm; không áp dụng cho database ứng dụng.
- Query 11 và 12 chưa được chạy lại trong phiên hiện tại. Ghi nhận lịch sử không được mô tả như kiểm chứng live mới.

## Nội dung gợi ý cập nhật Word

Cập nhật đúng phạm vi Phase 2 ở các mục 3.2–3.5, 5.6, 6.1–6.2. Phân biệt kết quả API lịch sử với năm truy vấn GUI được quan sát mới; bổ sung kết quả hai View, danh sách chín index, Explain Query 07 và đường dẫn ảnh. Nêu rõ các giới hạn tại mục trên. Không tuyên bố hoàn tất Giai đoạn 3.

## Checklist nghiệm thu Phase 2B

- [x] Xác định database test; không truy vấn/ghi database ứng dụng.
- [ ] Kiểm tra trạng thái Docker/container — BLOCKED do quyền truy cập Docker Engine qua named pipe bị từ chối.
- [x] Xác minh đủ chín Mango JSON index trên Fauxton.
- [x] Chạy MapReduce Count trên Fauxton với group=true.
- [x] Chạy MapReduce Sum trên Fauxton với group=true.
- [x] Đối chiếu hai View với 27 order nguồn.
- [x] Có ít nhất sáu ghi nhận Mango GUI nếu cộng năm lần chạy mới với hai bản ghi lịch sử của Query 11 và 12; chỉ năm truy vấn được chạy mới trong đợt này.
- [x] Lưu ảnh Fauxton và JSON kết quả có thể truy xuất.
- [x] Tạo/cập nhật biên bản kỹ thuật Phase 2B.
- [ ] Cập nhật báo cáo Word (được yêu cầu để ngoài phạm vi Phase 2B này).


