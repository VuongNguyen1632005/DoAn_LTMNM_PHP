# Báo cáo rà soát sự không thống nhất về backup và restore

Ngày rà soát: 27/10/2026

## Kết luận

Dự án có tài liệu ghi nhận kiểm thử backup và restore thành công trên database demo local ngày 10/10/2026. Tuy nhiên, một số đoạn trong tài liệu khác vẫn nói chưa có bằng chứng restore hoặc round-trip. Đây là sự không thống nhất về trạng thái được trình bày trong tài liệu; việc rà soát này chưa chứng minh chức năng hiện tại bị lỗi.

Kết quả PASS là kết quả lịch sử được ghi trong báo cáo dự án. Trong lần rà soát này không chạy lại backup, restore hoặc kiểm tra môi trường hiện tại.

## 1. Các tài liệu có nội dung khác nhau

### 1.1 Báo cáo kiểm chứng restore

Tệp: `PHP_COUCHDB/docs/RESTORE_VERIFICATION.md`.

Tài liệu ghi ngày 10/10/2026 đã restore backup JSON từ database demo `shopquan_ao_phase06_demo_test` vào database thử nghiệm `nosql_report_restore_check_test`. Báo cáo ghi nhận:

- Backup có 60 document và database đích có 60 document sau restore.
- Nội dung JSON được đối chiếu theo ID sau khi bỏ metadata revision; kết quả được ghi là PASS.
- Có kiểm tra design document, Mango Query, index và validator sau restore.
- Kết luận được ghi là backup/restore round-trip PASS trong phạm vi logical backup của database demo local.

Đây là bằng chứng được ghi nhận trong tài liệu dự án; không phải kết quả kiểm thử mới của lần rà soát này.

### 1.2 Kịch bản demo vẫn ghi chưa xác minh

Tệp: `PHP_COUCHDB/docs/final_report/NOSQL_DEMO_SCRIPT.md`.

Trong phần chuẩn bị, kịch bản ghi: “Evidence hiện tại CHƯA XÁC MINH”. Phần lời thoại và bước demo cũng dùng các câu “Chưa round-trip thì nói chưa xác minh” và “Chưa có round-trip evidence thì tôi không ghi PASS”.

Các câu này mâu thuẫn với báo cáo restore nêu trên nếu được đọc như nhận định về toàn bộ bằng chứng hiện có. Có thể hiểu chúng là hướng dẫn viết trước khi có kết quả hoặc nhắc phải chuẩn bị minh chứng mới; tuy nhiên, tài liệu không nêu rõ nên dễ khiến người đọc nghĩ chưa từng kiểm thử.

### 1.3 Báo cáo NoSQL tự mâu thuẫn giữa lời mở đầu và nội dung

Tệp: `PHP_COUCHDB/docs/final_report/NOSQL_FAUXTON_COUCHDB_REPORT.md`.

Lời mở đầu liệt kê “phục hồi backup thực tế” trong các kết quả chưa có bằng chứng. Trong khi đó:

- Chương 8 ghi backup và restore database demo local PASS ngày 10/10/2026, với 60/60 document.
- Bảng kiểm thử tại Chương 16 và phần kết luận cũng ghi đã kiểm chứng round-trip.

Vì vậy, ngay trong cùng một báo cáo có hai cách mô tả khác nhau về trạng thái restore. Phần lời mở đầu cần được cập nhật để phù hợp với bằng chứng lịch sử được trình bày ở các chương sau.

### 1.4 Báo cáo backup nói chưa chạy restore

Tệp: `PHP_COUCHDB/docs/BACKUP_VERIFICATION.md`.

Tài liệu này ghi backup PASS ngày 10/10/2026 và nêu “Chưa chạy restore trong lần kiểm thử này”. Câu này **không nhất thiết mâu thuẫn** với báo cáo restore: nó có thể mô tả riêng thời điểm hoặc phạm vi của lượt kiểm thử backup. Tuy nhiên, tài liệu không dẫn đến kết quả restore sau đó nên khi đọc riêng dễ hiểu rằng toàn bộ dự án chưa kiểm thử restore.

## 2. Phạm vi chính xác của kết quả đã ghi nhận

Kết quả được ghi là PASS áp dụng cho backup logic và phục hồi nội dung document vào database thử nghiệm mới. Báo cáo restore cho biết CouchDB tạo revision mới ở database đích.

Không suy ra từ kết quả này rằng đã phục hồi đầy đủ revision tree, lịch sử document đã xóa, cấu hình server, security object, replication, Docker volume hay ảnh upload lưu ngoài CouchDB. Đó là các giới hạn được chính báo cáo kiểm chứng nêu ra. Chưa có căn cứ từ lượt rà soát này để nói hệ thống hiện tại đã được kiểm thử lại hoặc backup production đã sẵn sàng.

## 3. Đề xuất chỉnh sửa

1. Trong `NOSQL_DEMO_SCRIPT.md`, thay các câu phủ định chung bằng mô tả có thời điểm: đã có kết quả round-trip local ngày 10/10/2026 theo báo cáo restore; cần kiểm tra môi trường và chuẩn bị minh chứng trước buổi bảo vệ. Giữ điều kiện “nếu chưa chạy lại thì không nói là vừa kiểm thử”.
2. Trong lời mở đầu `NOSQL_FAUXTON_COUCHDB_REPORT.md`, bỏ việc xếp restore thực tế vào nhóm chưa có bằng chứng. Phân biệt kết quả local đã được ghi nhận với các phần chưa xác minh như backup upload, backup production hoặc tình trạng hiện tại.
3. Trong `BACKUP_VERIFICATION.md`, giữ nguyên phạm vi lịch sử của lần kiểm thử backup, đồng thời thêm dẫn chiếu đến `RESTORE_VERIFICATION.md` để người đọc tìm được kết quả round-trip riêng.
4. Đối chiếu các bản Word, slide và nội dung thuyết trình thực tế với các tài liệu trên; lượt rà soát này chưa xác minh các bản Word hoặc slide cuối.

## 4. Đoạn đề xuất để sử dụng trong báo cáo

“Ngày 10/10/2026, nhóm đã ghi nhận kết quả kiểm thử backup và restore logic đối với database demo CouchDB trên môi trường local. Bản backup gồm 60 document; sau khi phục hồi vào database thử nghiệm mới, số lượng và nội dung document được báo cáo đối chiếu đạt yêu cầu. Nhóm cũng kiểm tra truy vấn Mango, index và validator sau phục hồi. Kết quả này áp dụng cho lần kiểm thử và dữ liệu demo nêu trên. Phương pháp chưa chứng minh phục hồi toàn bộ lịch sử revision, cấu hình máy chủ hoặc các tệp ảnh lưu ngoài CouchDB.”

## 5. Nguồn đối chiếu

- `PHP_COUCHDB/docs/BACKUP_VERIFICATION.md`
- `PHP_COUCHDB/docs/RESTORE_VERIFICATION.md`
- `PHP_COUCHDB/docs/final_report/NOSQL_DEMO_SCRIPT.md`
- `PHP_COUCHDB/docs/final_report/NOSQL_FAUXTON_COUCHDB_REPORT.md`

Báo cáo này chỉ đề xuất chỉnh sửa; chưa thay đổi các tài liệu gốc.
