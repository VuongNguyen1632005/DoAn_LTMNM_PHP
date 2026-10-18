# Nội dung bàn giao cho ChatGPT web: Backup và Restore CouchDB

Tài liệu này dùng để tiếp tục biên soạn báo cáo Word của đồ án WEBTHOITRANG PHP + CouchDB. Kết quả dưới đây là kiểm thử local ngày **10/10/2026** trên database demo. Chỉ gửi tài liệu này cùng bản Word hiện có; **không gửi file backup JSON** vì nó có thể chứa thông tin tài khoản và password hash.

## Yêu cầu gửi kèm cho ChatGPT web

> Hãy tiếp tục hoàn thiện báo cáo Word của tôi bằng cách chèn hoặc chỉnh hai mục **4.4.3 Backup** và **4.4.4 Restore** theo nội dung bên dưới. Giữ văn phong sinh viên CNTT, trình bày mạch lạc, dùng đúng số liệu kiểm thử ngày 10/10/2026. Có thể điều chỉnh số mục và số hình theo bố cục Word hiện tại, nhưng không tự thêm kết quả thử nghiệm, ảnh chụp hay chức năng chưa kiểm chứng. Giữ vị trí chèn hình để tôi bổ sung ảnh thật. Nêu rõ đây là logical backup/restore trên database demo local, không phải sao lưu toàn bộ máy chủ CouchDB.

## 4.4.3 Backup

Trong đồ án, em đã kiểm thử chức năng sao lưu logic cho cơ sở dữ liệu CouchDB `shopquan_ao_phase06_demo_test`. Lệnh `db:backup` chạy trong container ứng dụng và tạo tệp `nosql-demo-20261010.json` tại thư mục `var/backups`. Tệp có dung lượng **84.910 byte**, chứa **60 document**, gồm **58 document nghiệp vụ và 2 design document**. JSON hợp lệ; `document_count` khớp số phần tử `documents` và `doc_count` trên CouchDB. Tệp gồm product, customer, order và các document liên quan. Bài kiểm tra phân trang với 501, 1.000 và 1.001 document giả lập đều đạt; các trường hợp lỗi kết nối, sai thông tin đăng nhập và trùng tên tệp cũng bị từ chối. Script so sánh `doc_count` cùng `update_seq` trước và sau khi đọc để phát hiện nguồn thay đổi trong quá trình sao lưu. File được loại trừ khỏi Git vì có thể chứa dữ liệu cá nhân. Đây là bản xuất các document hiện có, không phải bản sao toàn bộ lịch sử revision hay ảnh tải lên ở volume riêng.

Lệnh đã chạy:

```powershell
docker compose exec -T -e COUCHDB_DATABASE=shopquan_ao_phase06_demo_test app composer db:backup -- nosql-demo-20261010.json
```

Kết quả: **exit code 0; JSON PASS; 60/60 document khớp; BACKUP VERIFIED**.

*[Chèn hình: terminal hiển thị tên database nguồn, 60 document, đường dẫn file và Backup: PASS. Không mở nội dung account/hash.]*

## 4.4.4 Restore

Sau khi xác nhận tệp backup hợp lệ, em dùng lệnh `db:restore` để phục hồi vào database mới `nosql_report_restore_check_test`. Trước khi chạy, database đích chưa tồn tại; lệnh kết thúc với mã 0, nhập thành công **60 document** và không ghi nhận document lỗi. Restore giữ `_id` cùng nội dung nghiệp vụ nhưng loại bỏ metadata revision cũ để CouchDB tạo revision mới. Vì một số order đã có lịch sử giao hàng, script nhập design document chứa index và dữ liệu nghiệp vụ trước, sau đó cài validator từ backup. Cách này tránh việc validator coi tracking lịch sử là trạng thái khởi tạo không hợp lệ. Em dùng lệnh kiểm chứng riêng để so sánh toàn bộ document theo `_id` và nội dung JSON, kể cả các mảng lồng như `items`, `status_history` và `delivery_tracking`. Kết quả không có document thiếu, dư hoặc khác nội dung. Cả **2 design document** và **9 JSON index cùng primary index** khớp database nguồn. Ba Mango query theo trạng thái đơn, khách hàng và mã tracking tìm được order mong đợi; `_explain` chọn đúng index. Một lần thử ghi order có status sai trên database đích bị validator từ chối với **HTTP 403**. Database nguồn giữ nguyên `doc_count` và `update_seq`. Kết quả xác nhận round-trip trên dữ liệu demo local, nhưng chưa chứng minh phục hồi volume ảnh, cấu hình server hoặc toàn bộ revision tree.

Các lệnh đã chạy:

```powershell
docker compose exec -T app composer db:restore -- nosql-demo-20261010.json nosql_report_restore_check_test
docker compose exec -T app composer db:restore-verify -- nosql-demo-20261010.json nosql_report_restore_check_test
```

Kết quả: **exit code 0; imported 60; failed 0; data parity PASS; Mango/index PASS; validator 403 PASS; BACKUP/RESTORE ROUND-TRIP: PASS**.

*[Chèn hình: terminal restore thành công; Fauxton hiển thị database đích và All Documents; `order:P06-0014` có delivery tracking; `_design/domain_validation`; Mango Query trên database đích. Không chụp account/hash.]*

## Số liệu và phạm vi cần giữ nguyên khi biên tập

| Mục | Kết quả đã kiểm chứng |
|---|---|
| Ngày kiểm thử | 10/10/2026 |
| Database nguồn | `shopquan_ao_phase06_demo_test` |
| Database đích | `nosql_report_restore_check_test` |
| Tệp backup | `var/backups/nosql-demo-20261010.json` |
| Dung lượng tệp | 84.910 byte |
| Số document | 60 nguồn / 60 backup / 60 đích |
| Design documents | `_design/catalog_indexes`, `_design/domain_validation` |
| Mango indexes | 9 JSON indexes và primary `_all_docs` |
| Mango query sau restore | Status/customer/tracking: 5/5/1 kết quả; đều tìm đúng order mong đợi |
| Validator | Ghi status sai trên đích bị từ chối HTTP 403 |
| Database nguồn | `doc_count` và `update_seq` không đổi trong lượt restore |

Giới hạn: `_bulk_docs` không phải transaction toàn database; nếu import lỗi giữa chừng, đích có thể còn dữ liệu một phần. Bài kiểm thử không chứng minh giữ full revision tree, deleted history, `_security`, replication, cấu hình máy chủ, Docker volume, ảnh/file upload hoặc trạng thái cluster. Không diễn giải `60 document` như quy mô dữ liệu production. Database đích hiện đã tồn tại nên chạy lại restore cùng tên sẽ bị từ chối an toàn; muốn chụp terminal mới phải chọn tên `_test` khác chưa tồn tại.

Nguồn kiểm chứng trong project: `docs/BACKUP_VERIFICATION.md`, `docs/RESTORE_VERIFICATION.md`, `scripts/database_backup.php`, `scripts/verify_database_restore.php`.
