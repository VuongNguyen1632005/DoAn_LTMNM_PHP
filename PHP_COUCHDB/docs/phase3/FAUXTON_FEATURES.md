# Minh chứng các tính năng Fauxton

CouchDB `3.5.0`; mọi thao tác ghi/xóa chạy trên database có hậu tố `_scratch`. Scripts in JSON ra terminal, lưu lại JSON trong `docs/phase3/feature_evidence/`, rồi tự dọn scratch trong `finally`.

## D1 — Replication

Lệnh tái lập trong PowerShell:

```powershell
docker compose exec -T app php scripts/demo_replication.php
```

Runtime evidence: `retail_order_delivery_demo` và `retail_demo_replica_scratch` đều có **150 documents**; SHA-256 nội dung chuẩn hóa, bỏ `_rev`, khác biệt **0**. Replication báo **149** documents ghi và **0** lỗi. Script bỏ `_design/domain_validation` trong lần phát lại, sau đó cài nguyên design document validator lên đích. Cách này giữ nguyên lịch sử hợp lệ: nếu phát lại các order có nhiều bước giao hàng như một revision mới, validator append-only từ chối 13 order lịch sử. Continuous replication khởi chạy thành công, `_scheduler/jobs` thấy **1** job; `_active_tasks` ghi nhận **0** tại lần đọc; cancel trả `ok=true`, sau cancel active tasks vẫn rỗng. Scratch DB đã bị xóa.

**Thao tác Fauxton:** mở sidebar **Replication**; chọn source `retail_order_delivery_demo`, target `retail_demo_replica_scratch`, bật tạo target và dùng selector `{"_id":{"$ne":"_design/domain_validation"}}` nếu bản Fauxton có trường Selector/Advanced options; bấm **Replicate**. Sau khi replication xong, mở **Databases** → source → **Design Documents** → `_design/domain_validation`, sao chép JSON; mở target và tạo design document cùng `_id` với nội dung đó (bỏ `_rev`). Mở **Databases** và so `doc_count`; mở **Replication** để bật Continuous trong chốc lát rồi hủy job. Kỳ vọng: mỗi DB 150 docs, nội dung business và các design doc khớp khi bỏ `_rev`, job dừng sau cancel.

## D2 — Conflict do replication

Lệnh:

```powershell
docker compose exec -T app php scripts/demo_replication_conflict.php
```

Tạo `retail_conflict_left_scratch` và `retail_conflict_right_scratch`, ghi cùng document, replicate base revision, rồi cập nhật độc lập trên hai DB. `GET ?conflicts=true` ghi nhận winner `edited-on-right` revision `2-af87709ba0acece687fd487240ec1f0a`; losing revision `2-3782173b1d360c26272022cd75527e4d`. Xóa leaf revision thua thành công; document sau xử lý không còn `_conflicts`. Cả hai DB scratch đã được xóa.

CouchDB giữ cả hai nhánh revision sau replication. Nó chọn winner theo một thuật toán xác định để các peer cùng thấy một revision; thứ tự mảng `_conflicts` không biểu thị nhánh thắng. Cách xử lý trong script là giữ winner CouchDB chọn và tạo tombstone cho leaf revision thua. [Tài liệu CouchDB 3.5 về conflict](https://docs.couchdb.org/en/stable/replication/conflicts.html).

**Thao tác Fauxton:** vào **Databases** → tạo hai DB scratch; tạo document `conflict:shared-demo-doc` bên trái và replicate sang phải trong **Replication**; sửa trường `value` khác nhau ở từng DB bằng đúng `_rev`; replicate trái sang phải lần nữa. Mở URL document của DB phải với `?conflicts=true` để thấy `_conflicts`; vào **Document** để chọn leaf revision cần xóa, dùng **Delete** cho revision đó. Kỳ vọng: còn một winner và GET `?conflicts=true` không còn `_conflicts`.

## D3 — Attachment PNG

Lệnh:

```powershell
docker compose exec -T app php scripts/demo_attachment.php
```

Tạo PNG 1×1 dài **68 bytes**, đính kèm `tiny.png` vào product trên `retail_attachment_scratch`. MD5 đọc lại khớp `WY4oUadFjaXDQdG7dbQsYQ==`, CouchDB digest là `md5-WY4oUadFjaXDQdG7dbQsYQ==`. Xóa attachment trả HTTP **200**; GET document không còn attachment. DB scratch đã được xóa.

**Thao tác Fauxton:** tạo DB `retail_attachment_scratch`; tạo product JSON; trong trang document bấm **Add Attachment**, chọn một PNG nhỏ, đặt tên `tiny.png`, bấm **Upload**. Mở attachment để kiểm tra; sau đó dùng **Delete Attachment** hoặc xóa attachment ở trang document. Kỳ vọng: content type `image/png`, attachment hiện dưới document rồi biến mất sau khi xóa. Ứng dụng hiện lưu ảnh ở storage ngoài và chỉ giữ tham chiếu/đường dẫn; CouchDB attachment là binary được quản lý cùng revision document.

## D4 — Database Security

Lệnh:

```powershell
docker compose exec -T app php scripts/demo_security.php
```

Security object đặt member thử nghiệm trong `members.names`, không có admins thử nghiệm. Kết quả runtime: member đọc document HTTP **200**; outsider đọc HTTP **403**; member sửa design document HTTP **403**, body `{"error":"forbidden","reason":"You are not a db or server admin."}`. Database scratch và cả hai user tạm trong `_users` đã được xóa. Lần chạy đầu tạo system database `_users` vì CouchDB instance chưa có; system database này được giữ lại, chỉ các user thử nghiệm bị xóa.

**Thao tác Fauxton:** tạo user thử nghiệm trong `_users` với `name`, `type=user`, `roles=[]`, và password dùng một lần. Tạo `retail_security_scratch`, tạo document thường và design document. Mở database **Security**; để `admins.names` và `admins.roles` rỗng, thêm username member vào `members.names`; bấm **Update**. Đăng nhập user member để đọc document (200), thử sửa design document (403); đăng nhập user ngoài danh sách để đọc document (403). Sau khi ghi minh chứng, xóa database scratch và hai user thử nghiệm trong `_users`.

## D5 — Compaction và xóa database

Lệnh:

```powershell
docker compose exec -T app php scripts/demo_compaction.php
```

Script ghi **51** revisions vào một document. `POST /_compact` trả **202**; lần poll đầu đã thấy compaction hoàn tất. CouchDB 3.5 không trả alias `data_size`/`disk_size` trong database info của runtime này, vì vậy evidence lưu các trường tương ứng từ `sizes.active` và `sizes.file`:

| Đo | Trước | Sau compact |
|---|---:|---:|
| doc_count | 1 | 1 |
| data_size (`sizes.active`) | 3195 | 2437 |
| disk_size (`sizes.file`) | 225629 | 33123 |
| sizes.external | 2111 | 2111 |

Xóa database trả HTTP **200**; GET xác nhận **404**. Scratch đã bị dọn.

**Thao tác Fauxton:** tạo `retail_compaction_scratch`; tạo document, cập nhật nhiều lần bằng revision mới; ghi size và `doc_count` ở overview. Trong trang database bấm **Compact** và xác nhận; chờ `compact_running` thành false rồi ghi số liệu lại. Trở về **Databases**, chọn database scratch và **Delete Database**; xác nhận GET database trả 404.

## File evidence

Mỗi script lưu JSON chi tiết vào `docs/phase3/feature_evidence/`: `replication.json`, `replication_conflict.json`, `attachment.json`, `security.json`, `compaction_delete.json`. Thời điểm UTC, mã HTTP, revision, hash, số đo, trạng thái cleanup được lưu trong từng file.
