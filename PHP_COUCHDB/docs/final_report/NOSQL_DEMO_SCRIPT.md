# KỊCH BẢN DEMO NOSQL/FAUXTON/COUCHDB

Thời lượng mục tiêu: **10 phút 50 giây**, có thể rút còn 8 phút hoặc mở rộng đến 12 phút. Trọng tâm là đối chiếu thao tác PHP với document, revision, history, Mango và index trong Fauxton. Kịch bản dựa trên source/Phase reports; **chưa chạy rehearsal trong nhiệm vụ này**.

## Chuẩn bị trước khi tính giờ

- [ ] Xác nhận Docker Engine và app/CouchDB local hoạt động; `/health/ready` thành công trên demo app.
- [ ] Xác nhận demo app trỏ tới `shopquan_ao_phase06_demo_test`. Port 8082 là môi trường riêng từng dùng trong Phase 06, không bảo đảm đang chạy hôm nay.
- [ ] Chuẩn bị customer/manager tổng hợp. Dùng credential lưu cục bộ, không in hoặc chụp password, hash, token hay credential file.
- [ ] Chuẩn bị hai browser profile riêng cho customer và manager trên cùng URL demo. Nếu tách cookie bằng localhost/127.0.0.1, phải thử trước; hai tab cùng profile có thể chia sẻ phiên.
- [ ] Mở Fauxton đúng demo DB; chuẩn bị query 02/11/12 và response `_explain` thật, không lưu credential trong URL.
- [ ] Chọn variant còn stock và một order failed. Ghi ID order vừa tạo, revision trước/sau vào ghi chú an toàn.
- [ ] Chuẩn bị product tổng hợp riêng để demo create/update/hide/restore hoặc ảnh/log thật của lượt đã kiểm chứng.
- [ ] Chuẩn bị response/log fixture 409/403 từ lượt test cô lập được phép. Chưa có log thì ghi CHƯA CÓ MINH CHỨNG TRỰC QUAN, không gõ lại PASS.
- [ ] Nếu trình bày backup/restore, hoàn thành round-trip trên DB test mới trước buổi bảo vệ, giữ output/parity. Evidence hiện tại CHƯA XÁC MINH.
- [ ] Không reset demo DB hoặc xóa database/volume trong flow demo. Không sửa order JSON để bỏ qua workflow PHP.

`demo:verify` kiểm tra baseline chuẩn. Khi có order/cart/product phát sinh, counts có thể không còn khớp; ghi inventory mới và giải thích thay đổi, không tự reset. Không chạy verifier fixture 347 docs lên demo 56 business docs.

## Luồng trình bày chính

**Bảng 1.1. Kịch bản theo thời gian và rubric**

| Thời gian | Bước/thao tác | Nội dung và bằng chứng cần chỉ | Tiêu chí |
|---|---|---|---|
| 00:00–00:30 | 1. Giới thiệu đề tài | “PHP là giao diện nghiệp vụ; Fauxton là tool quản trị CouchDB.” Nêu dataset tổng hợp. | 1, 4 |
| 00:30–01:00 | 2. Mở ứng dụng PHP | Chỉ catalog/product/variant; xác nhận môi trường local dùng CouchDB. | 8, 9 |
| 01:00–01:35 | 3. Login customer | Không chiếu password. Nêu register/hash/role; chưa có full admin user CRUD. | 9 |
| 01:35–02:25 | 4. Checkout | Thêm variant, chọn dòng, COD; chỉ shipping/voucher/totals/unpaid. Ghi ID order mới. | 5, 8, 9 |
| 02:25–02:50 | 5. Mở Fauxton | Databases → demo DB → Documents; giải thích PHP và tool cùng đọc database. | 1, 2, 5 |
| 02:50–03:40 | 6. Xem order document | Tìm ID vừa tạo; chỉ _id/_rev/schema2/items/shipping/payment/totals. Tránh receiver chưa kiểm tra an toàn. | 5, 8 |
| 03:40–04:30 | 7. Admin update order | Profile manager chuyển confirmed → packing → shipping bằng options hợp lệ. Chỉ tracking tự tạo và success message. | 9 |
| 04:30–04:55 | 8. Xem revision đổi | Refresh Fauxton; so revision cùng ID trước/sau. Generation không phải số bước nghiệp vụ. | 2, 8 |
| 04:55–05:25 | 9. Xem status_history | Chỉ events/time được lưu; phân biệt business history với revision. | 5, 9 |
| 05:25–06:40 | 10. Delivery Tracking | Chỉ code/ETA; chuyển picked_up → in_transit. Dùng order failed chuẩn bị để retry; nếu đủ giờ tiếp tục out_for_delivery → delivered. History giữ failed event; payment riêng. | 5, 9 |
| 06:40–07:30 | 11. Mango Query | Chạy query 02 rồi 11 hoặc 12. Counts sau update có thể khác baseline; nói theo output hiện tại. | 7 |
| 07:30–08:15 | 12. Index và explain | Manage Indexes; chỉ admin_orders/customer_orders/tracking index và response explain thật. Không gọi vài ms là benchmark. | 2, 7 |
| 08:15–09:00 | 13. Validator hoặc conflict | Log thật từ fixture đã chuẩn bị: revision 1→2/stale 409 hoặc reject 403. Case403 có nhiều field sai. Chưa có log thì chỉ source và report, gọi rõ là bằng chứng lịch sử. | 2, 5 |
| 09:00–09:40 | 14. CRUD/hỗ trợ | Minh họa product create/edit/hide/restore trực tiếp hoặc ảnh/log thật. Chỉ realtime nếu hai phiên đã chuẩn bị; nêu review/voucher/revenue thực có. | 9 |
| 09:40–10:20 | 15. Import/backup/so sánh | Chỉ importer/backup script và output thật. Chưa round-trip thì nói chưa xác minh. Fauxton web/CouchDB so với Compass desktop/MongoDB, không benchmark. | 3, 6 |
| 10:20–10:50 | 16. Kết luận | Document/query/index/rev/validator/PHP; nêu giới hạn snapshot ảnh, gateway, replication và deployment. | 4, 10 |

## Các câu cần nói chính xác

“Đã giao chưa đồng nghĩa đã thanh toán. Staff hoàn tất delivery không tự ghi COD paid; chủ đơn xác nhận nhận COD và staff thu COD có rule riêng.”

“Index tracking hỗ trợ tìm mã, không phải unique constraint xuyên documents. Kiểm tra mã hiện do application làm.”

“Validator kiểm tra order enum và nhiều rule delivery. Chuỗi transition order nằm ở PHP; status_history chưa được validator bảo vệ append-only đầy đủ.”

“Demo ban đầu có 27 orders, 56 business documents và 2 design documents. Checkout trong buổi trình bày làm counts thay đổi.”

“Restore là phục hồi nội dung vào DB test mới và bỏ revision cũ. Chưa có round-trip evidence thì tôi không ghi PASS.”

## Điều chỉnh thời lượng

**Bản 8 phút:** login trước giờ; giảm phần catalog; chuyển order đến shipping; dùng order failed/retried chuẩn bị để chỉ history; chạy một Mango query và xem một explain. CRUD/supporting dùng ảnh thật có nhãn “minh chứng chuẩn bị”.

**Bản 12 phút:** thêm delivery đến delivered và payment unpaid riêng; mở detail 390px hoặc customer/date query. Thêm 30–45 giây cho output backup/restore nếu đã hoàn thành. Không chạy thao tác quản trị dài ngay trên sân khấu.

## Xử lý sự cố

Nếu PHP hoặc Fauxton không lên, nói runtime hiện không sẵn sàng; dùng ảnh/log thật đã chuẩn bị và gọi là kết quả lượt trước. Nếu query trả 0, kiểm tra DB/selector/status sau thao tác; query 08 baseline trả 0 là đúng. Nếu form không có transition, đọc status và rule, không sửa JSON để ép bước.

Nếu hai tab chia sẻ cookie, chuyển profile đã chuẩn bị. Nếu target restore đã tồn tại, script phải từ chối; không xóa target để tiếp tục. Mọi log cần kiểm tra secret/PII trước khi mở.

## Checklist rehearsal cuối

- [ ] Đo thời gian 8–12 phút trên máy thật.
- [ ] Cùng order ID đi từ PHP sang Fauxton; ghi revision/history trước và sau.
- [ ] Phân biệt order/delivery/payment; có failed/retry/legacy example.
- [ ] Query/index/explain có output thật; không nhầm baseline với sau checkout.
- [ ] Có minh chứng CRUD mềm/account role và phần backup/restore trung thực.
- [ ] Bộ hình/log thật sẵn sàng; không cần delete/reset database.
