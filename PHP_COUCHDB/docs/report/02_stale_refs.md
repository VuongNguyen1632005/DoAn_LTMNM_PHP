# Chuỗi cũ báo cáo nguồn

Mỗi vị trí có heading gần nhất và chỉ số đoạn/bảng. Số trang dưới đây được suy ra từ các caption/TOC còn trích được, nên là khoảng định hướng: Chương 1 khoảng trang 8; Chương 2 trang 9–12; Chương 3 trang 14–28; Chương 4 trang 29–40; Chương 5 trang 41–54; Chương 6 khoảng trang 57. Word pagination chính xác không trích được vì không export PDF. Nội dung query history đã đóng sẽ không vào V2.

## phase0 (11)
- 3.2 Các chức năng quản trị đã tìm hiểu và sử dụng trong Fauxton — đoạn 170: Trong quá trình thực hành trên Fauxton, nhóm sử dụng database shopquan_ao_phase06_demo_test với 61 d
- 3.4.7 Thống kê bằng MapReduce View trên Fauxton — đoạn 213: Trên Fauxton, nhóm sử dụng database shopquan_ao_phase06_demo_test để thực hành hai MapReduce View tr
- 3.5.6 _explain và execution statistics — đoạn 237: Để đánh giá việc bổ sung index, nhóm dùng cùng selector delivery_tracking.status=failed_delivery trê
- 4.3.3 Import dữ liệu mẫu — đoạn 296: scripts/import_seed.php dùng bộ dữ liệu trong database/couchdb/seeds/retail_order_delivery.json để t
- 4.4.3 Backup — đoạn 321: Lần backup lịch sử ngày 10/10/2026 của shopquan_ao_phase06_demo_test tạo var/backups/nosql-demo-2026
- 4.4.3 Backup — đoạn 322: docker compose exec -T -e COUCHDB_DATABASE=shopquan_ao_phase06_demo_test app composer db:backup -- n
- 5.4.2 Kiểm thử stale revision trả HTTP 409 — đoạn 402: Script phase07_nosql_fixture_demo.php dùng một database test riêng để minh họa conflict. Product đượ
- bảng 3, hàng 2: Database | Không gian chứa documents và design documents | shopquan_ao_phase06_demo_test
- bảng 13, hàng 3: shopquan_ao_phase06_demo_test | Database tổng hợp dùng trình bày | Seed: 58 ID; backup: 60 ID (thêm cart:P06-0
- bảng 13, hàng 6: phase07_nosql_fixture_test | Database tạm để kiểm thử revision và validator | Được cleanup sau khi kiểm thử
- bảng 22, hàng 22: 21 | Index/_explain | Đạt trong phạm vi ghi nhận | Chín JSON Index được hiển thị trên Fauxton; API _explain ch

## phase (11)
- 3.2 Các chức năng quản trị đã tìm hiểu và sử dụng trong Fauxton — đoạn 170: Trong quá trình thực hành trên Fauxton, nhóm sử dụng database shopquan_ao_phase06_demo_test với 61 d
- 3.4.7 Thống kê bằng MapReduce View trên Fauxton — đoạn 213: Trên Fauxton, nhóm sử dụng database shopquan_ao_phase06_demo_test để thực hành hai MapReduce View tr
- 3.5.6 _explain và execution statistics — đoạn 237: Để đánh giá việc bổ sung index, nhóm dùng cùng selector delivery_tracking.status=failed_delivery trê
- 4.3.3 Import dữ liệu mẫu — đoạn 296: scripts/import_seed.php dùng bộ dữ liệu trong database/couchdb/seeds/retail_order_delivery.json để t
- 4.4.3 Backup — đoạn 321: Lần backup lịch sử ngày 10/10/2026 của shopquan_ao_phase06_demo_test tạo var/backups/nosql-demo-2026
- 4.4.3 Backup — đoạn 322: docker compose exec -T -e COUCHDB_DATABASE=shopquan_ao_phase06_demo_test app composer db:backup -- n
- 5.4.2 Kiểm thử stale revision trả HTTP 409 — đoạn 402: Script phase07_nosql_fixture_demo.php dùng một database test riêng để minh họa conflict. Product đượ
- bảng 3, hàng 2: Database | Không gian chứa documents và design documents | shopquan_ao_phase06_demo_test
- bảng 13, hàng 3: shopquan_ao_phase06_demo_test | Database tổng hợp dùng trình bày | Seed: 58 ID; backup: 60 ID (thêm cart:P06-0
- bảng 13, hàng 6: phase07_nosql_fixture_test | Database tạm để kiểm thử revision và validator | Được cleanup sau khi kiểm thử
- bảng 22, hàng 22: 21 | Index/_explain | Đạt trong phạm vi ghi nhận | Chín JSON Index được hiển thị trên Fauxton; API _explain ch

## D:\ (1)
- 3.1.1 Cài đặt CouchDB theo cấu hình đồ án — đoạn 153: Set-Location D:\PHP_NOSQL\PHP_COUCHDB
if (-not (Test-Path .env)) { Copy-Item .env.example .env }

do

## shopquan_ao (7)
- 3.2 Các chức năng quản trị đã tìm hiểu và sử dụng trong Fauxton — đoạn 170: Trong quá trình thực hành trên Fauxton, nhóm sử dụng database shopquan_ao_phase06_demo_test với 61 d
- 3.4.7 Thống kê bằng MapReduce View trên Fauxton — đoạn 213: Trên Fauxton, nhóm sử dụng database shopquan_ao_phase06_demo_test để thực hành hai MapReduce View tr
- 4.4.3 Backup — đoạn 321: Lần backup lịch sử ngày 10/10/2026 của shopquan_ao_phase06_demo_test tạo var/backups/nosql-demo-2026
- 4.4.3 Backup — đoạn 322: docker compose exec -T -e COUCHDB_DATABASE=shopquan_ao_phase06_demo_test app composer db:backup -- n
- bảng 3, hàng 2: Database | Không gian chứa documents và design documents | shopquan_ao_phase06_demo_test
- bảng 13, hàng 2: shopquan_ao_sql_migration_test | Database ứng dụng được quan sát khi kiểm thử local | 348 business document tr
- bảng 13, hàng 3: shopquan_ao_phase06_demo_test | Database tổng hợp dùng trình bày | Seed: 58 ID; backup: 60 ID (thêm cart:P06-0

## _test (14)
- 3.2 Các chức năng quản trị đã tìm hiểu và sử dụng trong Fauxton — đoạn 170: Trong quá trình thực hành trên Fauxton, nhóm sử dụng database shopquan_ao_phase06_demo_test với 61 d
- 3.4.7 Thống kê bằng MapReduce View trên Fauxton — đoạn 213: Trên Fauxton, nhóm sử dụng database shopquan_ao_phase06_demo_test để thực hành hai MapReduce View tr
- 4.4.3 Backup — đoạn 321: Lần backup lịch sử ngày 10/10/2026 của shopquan_ao_phase06_demo_test tạo var/backups/nosql-demo-2026
- 4.4.3 Backup — đoạn 322: docker compose exec -T -e COUCHDB_DATABASE=shopquan_ao_phase06_demo_test app composer db:backup -- n
- 4.4.4 Restore — đoạn 328: Nhóm phục hồi file backup hợp lệ vào database mới nosql_report_restore_check_test bằng lệnh db:resto
- 4.4.4 Restore — đoạn 329: docker compose exec -T app composer db:restore -- nosql-demo-20261010.json nosql_report_restore_chec
- 4.4.4 Restore — đoạn 330: docker compose exec -T app composer db:restore-verify -- nosql-demo-20261010.json nosql_report_resto
- 4.4.4 Restore — đoạn 331: Trong lần restore lịch sử, script nhập dữ liệu và Design Document rồi cài validator theo quy trình đ
- bảng 3, hàng 2: Database | Không gian chứa documents và design documents | shopquan_ao_phase06_demo_test
- bảng 5, hàng 8: Database mặc định mẫu | retail_order_delivery_test | Tên fallback/template, khác database local đã quan sát
- bảng 13, hàng 2: shopquan_ao_sql_migration_test | Database ứng dụng được quan sát khi kiểm thử local | 348 business document tr
- bảng 13, hàng 3: shopquan_ao_phase06_demo_test | Database tổng hợp dùng trình bày | Seed: 58 ID; backup: 60 ID (thêm cart:P06-0
- bảng 13, hàng 4: retail_order_delivery_test | Default trong template/fallback | Tên database mặc định trong file cấu hình mẫu (
- bảng 13, hàng 6: phase07_nosql_fixture_test | Database tạm để kiểm thử revision và validator | Được cleanup sau khi kiểm thử

## WEBTHOITRANG (2)
- 1.3 Đối tượng và phạm vi nghiên cứu — đoạn 106: Báo cáo sử dụng ứng dụng WEBTHOITRANG PHP + CouchDB làm phần minh họa, tập trung vào quản lý đơn hàn
- 6.4 Kết luận — đoạn 466: [9] Mã nguồn đồ án WEBTHOITRANG PHP + CouchDB, gồm README, cấu hình Docker, các module PHP, script k

## 347 (3)
- 4.4.2 Import database — đoạn 319: Khi import, script kiểm tra database đích và kết quả trả về của từng document. API _bulk_docs giúp g
- bảng 13, hàng 5: Database kiểm thử độc lập | Kiểm thử importer/verifier | 347 document kiểm thử gồm validator; script tự cleanu
- bảng 22, hàng 15: 14 | Seed isolated | Đạt | 347 document kiểm thử được import/verify trên DB tạm

## 348 (1)
- bảng 13, hàng 2: shopquan_ao_sql_migration_test | Database ứng dụng được quan sát khi kiểm thử local | 348 business document tr

## 58 (9)
- 3.5.1 Vì sao cần index — đoạn 220: Khi thực hiện truy vấn failed_delivery, CouchDB trả một order nhưng kiểm tra 58 document. Trong khi 
- 4.3.4 Số lượng dữ liệu — đoạn 299: Bộ dữ liệu ban đầu gồm 56 business document và 2 Design Document, tổng cộng 58 document. Khi đối chi
- 4.4.3 Backup — đoạn 321: Lần backup lịch sử ngày 10/10/2026 của shopquan_ao_phase06_demo_test tạo var/backups/nosql-demo-2026
- 6.1 Kết quả đạt được — đoạn 445: Ngoài chín JSON Mango Index, nhóm đã thực hiện Explain và hai MapReduce View thống kê trên 27 order 
- bảng 10, hàng 3: Failed delivery (API _find) | 1 | 58 | Khoảng 3 ms | Fallback; UI cảnh báo thiếu index phù hợp
- bảng 13, hàng 3: shopquan_ao_phase06_demo_test | Database tổng hợp dùng trình bày | Seed: 58 ID; backup: 60 ID (thêm cart:P06-0
- bảng 16, hàng 10: Tổng document | 58
- bảng 22, hàng 25: 24 | Backup/restore round-trip | Restore đạt; đối chiếu _id đạt | Bài thử phục hồi 60/60 document và đối chiếu
- bảng 23, hàng 7: Delivery status thiếu index dedicated | Truy vấn delivery status từng xét 58 document; index chuyên biệt đã đư

## 60 (12)
- 4.3.4 Số lượng dữ liệu — đoạn 299: Bộ dữ liệu ban đầu gồm 56 business document và 2 Design Document, tổng cộng 58 document. Khi đối chi
- 4.4.3 Backup — đoạn 321: Lần backup lịch sử ngày 10/10/2026 của shopquan_ao_phase06_demo_test tạo var/backups/nosql-demo-2026
- 4.4.3 Backup — đoạn 324: Sao lưu JSON của đồ án lưu 60 document hiện hành. Tệp này không bao gồm đầy đủ revision tree, docume
- 4.4.4 Restore — đoạn 328: Nhóm phục hồi file backup hợp lệ vào database mới nosql_report_restore_check_test bằng lệnh db:resto
- 4.4.4 Restore — đoạn 331: Trong lần restore lịch sử, script nhập dữ liệu và Design Document rồi cài validator theo quy trình đ
- 4.4.4 Restore — đoạn 336: Trong lần kiểm thử, toàn bộ 60 document đã được phục hồi vào database đích. Đối chiếu với file backu
- 6.1 Kết quả đạt được — đoạn 445: Ngoài chín JSON Mango Index, nhóm đã thực hiện Explain và hai MapReduce View thống kê trên 27 order 
- bảng 13, hàng 3: shopquan_ao_phase06_demo_test | Database tổng hợp dùng trình bày | Seed: 58 ID; backup: 60 ID (thêm cart:P06-0
- bảng 19, hàng 4: Export/backup CouchDB JSON | Có | Đạt ngày 10/10/2026: file 84.910 byte, JSON hợp lệ, 60/60 document
- bảng 19, hàng 5: Restore vào DB mới | Có | Đạt: imported 60, failed 0; data parity, query/index và validator đều PASS
- bảng 22, hàng 25: 24 | Backup/restore round-trip | Restore đạt; đối chiếu _id đạt | Bài thử phục hồi 60/60 document và đối chiếu
- bảng 23, hàng 9: Backup/restore hiện là logical trên demo local | Logical backup chưa bao gồm revision tree, document đã xóa, _

## 61 (15)
- Trang đầu — đoạn 50: Hình 3.7. Mango Query 01 lọc đơn pending; giao diện cảnh báo quét 61 document 	27
- 3.2 Các chức năng quản trị đã tìm hiểu và sử dụng trong Fauxton — đoạn 170: Trong quá trình thực hành trên Fauxton, nhóm sử dụng database shopquan_ao_phase06_demo_test với 61 d
- 3.4.1 Tìm đơn pending — đoạn 182: Khi chạy truy vấn 01 qua API, hệ thống trả HTTP 200 với 5 document. Trên Fauxton ngày 28/10/2026, tr
- 3.5.6 _explain và execution statistics — đoạn 237: Để đánh giá việc bổ sung index, nhóm dùng cùng selector delivery_tracking.status=failed_delivery trê
- 3.5.6 _explain và execution statistics — đoạn 241: Hình 3.7. Truy vấn Mango Query 01 lọc đơn pending và cảnh báo quét 61 document
- 4.3.4 Số lượng dữ liệu — đoạn 299: Bộ dữ liệu ban đầu gồm 56 business document và 2 Design Document, tổng cộng 58 document. Khi đối chi
- 4.4.3 Backup — đoạn 321: Lần backup lịch sử ngày 10/10/2026 của shopquan_ao_phase06_demo_test tạo var/backups/nosql-demo-2026
- 4.4.3 Backup — đoạn 322: docker compose exec -T -e COUCHDB_DATABASE=shopquan_ao_phase06_demo_test app composer db:backup -- n
- 4.4.4 Restore — đoạn 329: docker compose exec -T app composer db:restore -- nosql-demo-20261010.json nosql_report_restore_chec
- 4.4.4 Restore — đoạn 330: docker compose exec -T app composer db:restore-verify -- nosql-demo-20261010.json nosql_report_resto
- 4.4.4 Restore — đoạn 331: Trong lần restore lịch sử, script nhập dữ liệu và Design Document rồi cài validator theo quy trình đ
- 6.1 Kết quả đạt được — đoạn 445: Ngoài chín JSON Mango Index, nhóm đã thực hiện Explain và hai MapReduce View thống kê trên 27 order 
- bảng 10, hàng 5: 01 Pending (28/10) | 5 | 61 | 7 ms | Fallback; 61 keys/documents examined
- bảng 13, hàng 3: shopquan_ao_phase06_demo_test | Database tổng hợp dùng trình bày | Seed: 58 ID; backup: 60 ID (thêm cart:P06-0
- bảng 22, hàng 25: 24 | Backup/restore round-trip | Restore đạt; đối chiếu _id đạt | Bài thử phục hồi 60/60 document và đối chiếu

## 27 (16)
- Trang đầu — đoạn 50: Hình 3.7. Mango Query 01 lọc đơn pending; giao diện cảnh báo quét 61 document 	27
- Trang đầu — đoạn 51: Hình 3.8. Mango Query 06 theo khoảng ngày trên Fauxton (21 kết quả) 	27
- Trang đầu — đoạn 91: Bảng 4.7. Phân bố 27 order bộ dữ liệu ban đầu	35
- 3.1.1 Cài đặt CouchDB theo cấu hình đồ án — đoạn 153: Set-Location D:\PHP_NOSQL\PHP_COUCHDB
if (-not (Test-Path .env)) { Copy-Item .env.example .env }

do
- 3.1.2 Truy cập Fauxton và tài khoản quản trị — đoạn 156: Trong môi trường local, nhóm truy cập Fauxton tại http://127.0.0.1:5984/_utils/ và đăng nhập bằng tà
- 3.4 Truy vấn dữ liệu bằng Mango Query — đoạn 176: Các truy vấn 01–04 lọc trạng thái đơn hàng; 05–08 tập trung vào khách hàng, thời gian và thanh toán;
- 3.4.4 Tìm order theo khoảng thời gian — đoạn 200: Khi chạy qua API _find, truy vấn 06 trả HTTP 200 với 27 document. Ở lần thực hành trên Fauxton ngày 
- 3.4.7 Thống kê bằng MapReduce View trên Fauxton — đoạn 213: Trên Fauxton, nhóm sử dụng database shopquan_ao_phase06_demo_test để thực hành hai MapReduce View tr
- 3.4.7 Thống kê bằng MapReduce View trên Fauxton — đoạn 215: Kết quả đếm gồm cancelled 2, confirmed 4, delivered 6, packing 4, pending 5 và shipping 6, tổng cộng
- 4.3.5 Các trạng thái order trong dataset — đoạn 301: Bảng 4.7. Phân bố 27 order bộ dữ liệu ban đầu
- 4.3.5 Các trạng thái order trong dataset — đoạn 302: Bộ dữ liệu demo có 27 order trải trên nhiều trạng thái, giúp nhóm kiểm tra các truy vấn lọc, thống k
- 6.1 Kết quả đạt được — đoạn 445: Ngoài chín JSON Mango Index, nhóm đã thực hiện Explain và hai MapReduce View thống kê trên 27 order 
- bảng 7, hàng 7: 06 | Khoảng thời gian | ordered_at từ 01/09 đến trước 01/11/2026 | admin_orders_by_date | 27 API / 21 GUI | AP
- bảng 16, hàng 7: order | 27
- bảng 22, hàng 28: 27 | MapReduce View | Đạt trên DB demo 28/10 | Hai View Count và Sum chạy với group=true trên Fauxton, sử dụng
- bảng 23, hàng 13: MapReduce View mới chỉ nghiệm thu trên database demo | Hai View Count/Sum đã chạy trên Fauxton ngày 28/10 với 

## 21 (7)
- Trang đầu — đoạn 51: Hình 3.8. Mango Query 06 theo khoảng ngày trên Fauxton (21 kết quả) 	27
- 3.4 Truy vấn dữ liệu bằng Mango Query — đoạn 176: Các truy vấn 01–04 lọc trạng thái đơn hàng; 05–08 tập trung vào khách hàng, thời gian và thanh toán;
- 3.4.4 Tìm order theo khoảng thời gian — đoạn 200: Khi chạy qua API _find, truy vấn 06 trả HTTP 200 với 27 document. Ở lần thực hành trên Fauxton ngày 
- 3.5.6 _explain và execution statistics — đoạn 243: Hình 3.8. Truy vấn Mango Query 06 theo khoảng thời gian trên Fauxton (21 kết quả)
- bảng 7, hàng 7: 06 | Khoảng thời gian | ordered_at từ 01/09 đến trước 01/11/2026 | admin_orders_by_date | 27 API / 21 GUI | AP
- bảng 10, hàng 8: 06 Date range (28/10) | 21 | 22 | 9 ms | admin_orders_by_date; 22 keys
- bảng 22, hàng 22: 21 | Index/_explain | Đạt trong phạm vi ghi nhận | Chín JSON Index được hiển thị trên Fauxton; API _explain ch

## lịch sử (23)
- Trang đầu — đoạn 72: Hình 5.14. Minh chứng từ chối document không hợp lệ trong kiểm thử lịch sử; chưa phân lập rule statu
- 1.5 Ý nghĩa của đề tài — đoạn 110: Đề tài giúp nhóm làm quen với cách tổ chức dữ liệu document và thấy được những điểm phù hợp của Couc
- 2.1.2 Phân loại cơ sở dữ liệu NoSQL — đoạn 123: Trong đồ án, mô hình document được chọn vì một order có thể chứa nhiều phần dữ liệu lồng nhau như it
- 2.2.2 Các thành phần quan trọng của CouchDB — đoạn 135: _rev, phiên bản cấu trúc và status_history có vai trò khác nhau. _rev là mã phiên bản CouchDB dùng k
- 2.2.3 Ưu điểm, hạn chế và lý do lựa chọn CouchDB — đoạn 137: Trong bài toán Order/Delivery, màn hình chi tiết thường đọc đồng thời người nhận, items, shipping, p
- 4.1.3 Luồng Order — đoạn 261: Order đi qua các trạng thái pending, confirmed, packing, shipping và delivered. Sau khi đã vào shipp
- 4.1.4 Luồng Delivery — đoạn 264: Thông tin tracking được tạo khi order chuyển sang shipping. Nếu giao thất bại, order vẫn giữ trạng t
- 4.2.4 Order Document — đoạn 280: Order document chứa thông tin khách hàng, người nhận, items, payment, shipping, totals và lịch sử xử
- 4.2.5 Snapshot, embedding và denormalization trong order — đoạn 282: Một Order document nhúng items, shipping, payment và lịch sử vì các phần này thường được đọc cùng nh
- 4.4.3 Backup — đoạn 321: Lần backup lịch sử ngày 10/10/2026 của shopquan_ao_phase06_demo_test tạo var/backups/nosql-demo-2026
- 4.4.4 Restore — đoạn 331: Trong lần restore lịch sử, script nhập dữ liệu và Design Document rồi cài validator theo quy trình đ
- 4.4.4 Restore — đoạn 332: Kết quả cho thấy quy trình sao lưu rồi phục hồi hoạt động đúng trên database demo local. Vì _bulk_do
- 5.1.3 Các module nghiệp vụ và document liên quan — đoạn 352: Giỏ hàng của khách vãng lai được lưu trong session, còn giỏ của thành viên sử dụng Cart document. Ch
- 5.2.3 Order Management — đoạn 367: Customer chọn biến thể, thêm vào giỏ rồi checkout bằng COD. Sau khi tạo đơn, trang History và Detail
- 5.2.4 Delivery Tracking — đoạn 377: Khi order chuyển sang shipping, hệ thống tạo delivery_tracking. Admin cập nhật lần lượt các bước pic
- 5.3.1 Luồng hiện có — đoạn 385: Realtime được thực hiện bằng SSE kết hợp với _changes của CouchDB. Trang chi tiết đơn nhận được trạn
- 5.5.4 Validation Delivery status — đoạn 424: Delivery có tập trạng thái riêng cùng các trường code, date và history. Validator kiểm tra timestamp
- 5.5.5 Quy tắc bảo toàn lịch sử trạng thái đơn hàng (Append-only History) — đoạn 426: 5.5.5 Quy tắc bảo toàn lịch sử trạng thái đơn hàng (Append-only History)
- 5.5.5 Quy tắc bảo toàn lịch sử trạng thái đơn hàng (Append-only History) — đoạn 427: Lịch sử delivery được lưu theo hướng chỉ bổ sung sự kiện mới: sau mỗi lần cập nhật, mảng history khô
- bảng 2, hàng 3: Document | Tài liệu có trường, object và array | Order có items[], snapshot và lịch sử
- bảng 8, hàng 7: customer_orders | type, customer.customer_id, ordered_at | order | Lịch sử theo chủ đơn
- bảng 15, hàng 3: Item/shipping snapshot | Giữ lịch sử giá/tên/phí | Lặp dữ liệu; ảnh chưa được giữ
- bảng 23, hàng 2: Không lưu ảnh order snapshot | Detail dùng placeholder; không có ảnh lịch sử

## chưa kiểm thử (3)
- 5.3.4 Kết quả và giới hạn — đoạn 391: Khi kiểm thử trên trình duyệt, nhóm quan sát các thay đổi shipping, failed_delivery, retry, in_trans
- bảng 19, hàng 6: Backup upload files | Volume có | Chưa kiểm thử sao lưu/phục hồi file ảnh nằm ngoài CouchDB
- bảng 23, hàng 13: MapReduce View mới chỉ nghiệm thu trên database demo | Hai View Count/Sum đã chạy trên Fauxton ngày 28/10 với 

## cần bổ sung (2)
- 5.6.1 Cơ sở tổng hợp — đoạn 435: Bảng 5.3 tổng hợp kết quả kiểm thử chức năng PHP và CouchDB theo từng nhóm nghiệp vụ. Nhóm đã thực h
- bảng 22, hàng 29: 28 | Quản trị người dùng | Đã xây dựng; kiểm tra mã nguồn đạt | Trang /admin/users hỗ trợ tìm kiếm, lọc, xem/s