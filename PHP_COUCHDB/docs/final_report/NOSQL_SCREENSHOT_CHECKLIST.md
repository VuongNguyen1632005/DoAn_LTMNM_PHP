# CHECKLIST HÌNH – BÁO CÁO NOSQL/FAUXTON/COUCHDB

Ngày lập: 09/10/2026. **Chưa có ảnh được tạo trong nhiệm vụ này.** Các ô trống là minh chứng cần bổ sung. Chỉ chụp database demo tổng hợp; không mở credential/hash/token/PII. Sơ đồ tự dựng phải được gọi là sơ đồ, không phải ảnh thực nghiệm.

Fauxton theo Phase 07: `http://127.0.0.1:5984/_utils/`. Website mặc định 8081; demo riêng từng dùng 8082. Trước khi chụp phải xác nhận app đúng DB demo, không suy từ port. `order:P06-0020` là order tracked in_transit của baseline; nếu dữ liệu đã đổi, chọn lại và ghi thời điểm.

**Bảng 1.1. Danh mục khớp placeholder trong báo cáo**

| Đã chụp | Hình số | Tên hình | Trang/vùng cần mở | Role | Dữ liệu cần có | Mục đích |
|---|---|---|---|---|---|---|
| [ ] | 2.1 | Fauxton sau đăng nhập | `/_utils/` | CouchDB admin | Version/UI | Tool đã truy cập |
| [ ] | 2.2 | Danh sách database | Databases | CouchDB admin | Tên demo DB | Phân biệt database |
| [ ] | 2.3 | All Documents | Demo DB → All Documents | CouchDB admin | Count có thời điểm | Database thật, dữ liệu tổng hợp |
| [ ] | 5.1 | Sơ đồ nghiệp vụ | Dựng từ workflow source | Không cần login | Order/delivery/cancel/retry | Sơ đồ giải thích luồng |
| [ ] | 7.1 | Product JSON | Demo DB → product | CouchDB admin | Variants/images | Document lồng |
| [ ] | 7.2 | Order snapshot | Demo DB → order | CouchDB admin | Items/shipping/payment/totals | Aggregate/snapshot |
| [ ] | 7.3 | Delivery Tracking | order:P06-0020 → tracking | CouchDB admin | Code/status/ETA/history | Delivery nhúng |
| [ ] | 7.4 | Order history | Order → status_history | CouchDB admin | Status/time thực | Lịch sử đã lưu |
| [ ] | 8.1 | Backup JSON | Terminal output an toàn | Người vận hành | Backup thực, count | Export/backup |
| [ ] | 8.2 | Restore mới | Terminal + target DB | Vận hành/admin | Count và content parity | Restore được xác minh |
| [ ] | 9.1 | Mango shipping | Mango → query 02 | CouchDB admin | Results/stats có thời điểm | Query cơ bản/GUI |
| [ ] | 9.2 | Mango failed | Mango → query 11 | CouchDB admin | Failed result/warning | Nested query/fallback |
| [ ] | 9.3 | Mango tracking | Mango → query 12 | CouchDB admin | Mã synthetic phù hợp | Tra mã/index |
| [ ] | 10.1 | Manage Indexes | Demo DB → Indexes | CouchDB admin | 9 JSON + primary | Index thật |
| [ ] | 10.2 | _explain | Response của query 01/05/12 | Admin/client nội bộ | Index selected | Planner; dùng API thật nếu GUI không có nút |
| [ ] | 11.1 | Revision trước/sau | Product fixture hoặc demo doc | CouchDB admin | Hai revision cùng ID | Revision đổi do write |
| [ ] | 11.2 | Conflict 409 | Fixture output/log HTTP | Người vận hành | Stale PUT 409 thật | API conflict; không giả GUI error |
| [ ] | 12.1 | Validator | Design Documents | CouchDB admin | validate_doc_update | Design doc/validation |
| [ ] | 12.2 | Reject 403 | Fixture output/response | Người vận hành | Sai document bị reject | Validator khác lỗi role |
| [ ] | 13.1 | Sơ đồ kiến trúc | Dựng từ public/index.php | Không cần login | PHP → Repository → REST → DB | Sơ đồ integration |
| [ ] | 14.1 | Auth/role | `/register`, `/login`, sau login | Customer/staff/manager | Tài khoản tổng hợp | User/auth thực có |
| [ ] | 14.2 | PHP catalog | `/`, `/products/{id}` | Guest/customer | Product và variant | Read/application |
| [ ] | 14.3 | Product CRUD mềm | `/admin/products`, form | Manager | Product demo riêng | Create/edit/hide/restore |
| [ ] | 14.4 | Checkout | `/checkout` | Customer | Cart/COD/ship/totals | App ghi order |
| [ ] | 14.5 | History/detail | `/account/orders`, detail | Customer owner | Order items/payment | Ownership/order info |
| [ ] | 14.6 | Admin list/filter | `/admin/orders?status=shipping` | Staff/manager | Orders có tracking | Filter/payment/delivery |
| [ ] | 14.7 | Admin detail/form | `/admin/orders/{id}` | Staff/manager | Transitions hợp lệ | Mutation qua PHP |
| [ ] | 14.8 | Failed/retry timeline | Customer detail | Customer owner | Failed và retry events | History không xóa lỗi |
| [ ] | 14.9 | Legacy no tracking | Legacy delivered detail | Customer owner | Một trong hai legacy baseline | Không backfill giả |
| [ ] | 14.10 | Detail 390px | Customer và admin detail | Hai phiên role riêng | Timeline/form/table đọc được | Responsive; hai ảnh có nhãn |
| [ ] | 14.11 | Hỗ trợ | Revenue hoặc review demo | Role phù hợp | Dữ liệu tổng hợp | Chức năng hỗ trợ thật |
| [ ] | 15.1 | Realtime | Customer detail sau update | Customer + staff phiên khác | Order/SSE có thay đổi | Ảnh kèm video/demo không F5 |

## Cách thu thập minh chứng

1. Xác nhận health/readiness và database dùng bởi demo app; bảo đảm màn hình không chứa dữ liệu cá nhân thật.
2. Ghi ngày, DB, role, ID tổng hợp và viewport. Counts sau thao tác có thể khác baseline; caption phải theo kết quả thật.
3. Crop vùng cần thiết nhưng giữ dấu hiệu nguồn UI/ID; không sửa nội dung kết quả. Che secret trước khi lưu/chia sẻ.
4. Backup/restore/409/403 chỉ dùng output thật. Fixture script có guard và tự cleanup; chuẩn bị trong lượt test riêng được phép, không thêm DELETE thủ công.
5. Lưu tên như `figure_09_02_mango_failed.png`, chèn đúng placeholder và caption. Chỉ đánh dấu [x] sau khi lưu ảnh thật.
6. Kiểm tra hình/bảng/code đọc được trong Word và tạo danh mục hình tự động.

## Minh chứng bổ sung nên có

- [ ] Tạo DB bằng Fauxton trên target test mới: hiện chưa có biên bản GUI; thêm caption/hình nếu thực hiện.
- [ ] CRUD document bằng Fauxton trên product fixture riêng: hiện có read GUI và CRUD PHP/test.
- [ ] Guest query có kết quả trên dataset được bổ sung có kiểm soát; baseline query 08 = 0, không tự seed/reset để tạo ảnh.
- [ ] Log test mới trước bảo vệ; không thay output thật bằng chữ PASS gõ lại.

Replication, attachment và Compass thuộc phần nghiên cứu. Không tạo ảnh giả để ghi thành demo project. Có 32 vị trí hình; một vị trí có thể gồm nhiều ảnh trước/sau hoặc theo role.
