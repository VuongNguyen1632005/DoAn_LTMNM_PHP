# BẢN ĐỒ NGUỒN – BÁO CÁO NOSQL/FAUXTON/COUCHDB

Ngày đối chiếu: 09/10/2026. Source hiện tại ưu tiên hơn report cũ khi có khác biệt. Kết quả test/runtime là kết quả lịch sử của Phase, không phải lần chạy mới. Các đường dẫn trong bảng thuộc project `D:\PHP_NOSQL\PHP_COUCHDB`.

## 1. Nội dung và file chứng minh

**Bảng 1.1. Source map theo chương**

| Nội dung báo cáo | File chứng minh | Loại bằng chứng / giới hạn |
|---|---|---|
| Tên project/PHP/dependencies | [README](D:/PHP_NOSQL/PHP_COUCHDB/README.md), [Composer](D:/PHP_NOSQL/PHP_COUCHDB/composer.json) | Source; không có PHP framework |
| Docker/Apache/CouchDB 3.5/volumes/ports | [Dockerfile](D:/PHP_NOSQL/PHP_COUCHDB/Dockerfile), [Compose](D:/PHP_NOSQL/PHP_COUCHDB/docker-compose.yml) | Cấu hình; default 8081/5984 khác demo 8082 |
| Fauxton thực tế và URL | [Phase 07](D:/PHP_NOSQL/PHP_COUCHDB/docs/PHASE_07_NOSQL_FAUXTON_REPORT.md), [DB README](D:/PHP_NOSQL/PHP_COUCHDB/database/couchdb/README.md) | Browser đã đăng nhập; đọc document/index/query |
| Cấu hình database theo thời điểm | [Phase 06](D:/PHP_NOSQL/PHP_COUCHDB/docs/PHASE_06_FINAL_TEST_DEMO_DATA_REPORT.md), Phase 07 | DB app 348 business; demo 56 business + 2 design docs |
| Các 8 types/schema 2/SQL mapping | [Schema mapping](D:/PHP_NOSQL/PHP_COUCHDB/database/couchdb/docs/schema-v2-and-mapping.md), [Validator](D:/PHP_NOSQL/PHP_COUCHDB/database/couchdb/design-docs/domain_validation.json) | Contract/source, không phải SQL runtime |
| Product/variants/images | [ProductRepository](D:/PHP_NOSQL/PHP_COUCHDB/src/Catalog/ProductRepository.php), [ProductAdminService](D:/PHP_NOSQL/PHP_COUCHDB/src/Catalog/ProductAdminService.php), schema/fixture | Product có variants/images; category không áp đặt object |
| CRUD sản phẩm/soft hide/restore | [ProductAdminController](D:/PHP_NOSQL/PHP_COUCHDB/src/Catalog/ProductAdminController.php), ProductAdminService, [Catalog smoke](D:/PHP_NOSQL/PHP_COUCHDB/scripts/smoke_product_admin.php) | Manager mutation; delete mềm, không hard delete |
| Ảnh/upload/media | [ProductImageStorage](D:/PHP_NOSQL/PHP_COUCHDB/src/Catalog/ProductImageStorage.php), [MediaController](D:/PHP_NOSQL/PHP_COUCHDB/src/Catalog/ProductMediaController.php) | Filesystem; không CouchDB attachment |
| Guest/member cart | [GuestCartService](D:/PHP_NOSQL/PHP_COUCHDB/src/Cart/GuestCartService.php), [MemberCartRepository](D:/PHP_NOSQL/PHP_COUCHDB/src/Cart/MemberCartRepository.php), [MemberCartService](D:/PHP_NOSQL/PHP_COUCHDB/src/Cart/MemberCartService.php) | Guest session, member document, merge |
| Checkout/COD/snapshots/idempotency | [CheckoutService](D:/PHP_NOSQL/PHP_COUCHDB/src/Checkout/CheckoutService.php), [PricingService](D:/PHP_NOSQL/PHP_COUCHDB/src/Checkout/PricingService.php) | Product_name/size/quantity/unit_price/line_total; không ảnh |
| Order/Delivery transitions/fail/retry/ETA | [OrderWorkflowService](D:/PHP_NOSQL/PHP_COUCHDB/src/Orders/OrderWorkflowService.php) | TRANSITIONS/DELIVERY_TRANSITIONS, initializeDeliveryTracking, updateDeliveryStatus |
| Order history/ownership | [OrderController](D:/PHP_NOSQL/PHP_COUCHDB/src/Orders/OrderController.php), [CheckoutRepository](D:/PHP_NOSQL/PHP_COUCHDB/src/Checkout/CheckoutRepository.php) | Owner theo legacy ID/session guest; 25/page |
| Staff/manager forms/filter/messages | [OrderAdminController](D:/PHP_NOSQL/PHP_COUCHDB/src/Orders/OrderAdminController.php), [Admin detail](D:/PHP_NOSQL/PHP_COUCHDB/templates/admin/orders/detail.php), [Admin list](D:/PHP_NOSQL/PHP_COUCHDB/templates/admin/orders/index.php) | CSRF và server-provided transitions; ETA chỉ hiển thị |
| Customer timelines/legacy/placeholder | [Customer detail](D:/PHP_NOSQL/PHP_COUCHDB/templates/orders/detail.php), [Delivery timeline](D:/PHP_NOSQL/PHP_COUCHDB/templates/orders/delivery_timeline.php), [History](D:/PHP_NOSQL/PHP_COUCHDB/templates/orders/index.php) | Không sinh history giả/không expose staff controls |
| Responsive/bug header 390px | [CSS](D:/PHP_NOSQL/PHP_COUCHDB/public/assets/css/catalog.css), [Phase 03/03B](D:/PHP_NOSQL/PHP_COUCHDB/docs/PHASE_03_ORDER_UI_UX_REPORT.md), Phase 06 | Browser 1280/390; table cuộn trong wrapper |
| Payment độc lập/manual audit | OrderWorkflowService, CheckoutService, Phase 02/06 | COD/new; bank/card manual; không gateway/refund |
| Auth/register/login/hash | [AccountService](D:/PHP_NOSQL/PHP_COUCHDB/src/Auth/AccountService.php), [AccountRepository](D:/PHP_NOSQL/PHP_COUCHDB/src/Auth/AccountRepository.php), [AuthController](D:/PHP_NOSQL/PHP_COUCHDB/src/Auth/AuthController.php) | Không có admin CRUD account UI đầy đủ |
| Provision staff/customer | [Provision staff](D:/PHP_NOSQL/PHP_COUCHDB/scripts/provision_staff_password.php), [Provision customer](D:/PHP_NOSQL/PHP_COUCHDB/scripts/provision_customer_password.php) | Terminal guard/TTY, không chụp mật khẩu |
| JWT/role | [JwtTokenService](D:/PHP_NOSQL/PHP_COUCHDB/src/Auth/JwtTokenService.php), [JwtBearerAuthenticator](D:/PHP_NOSQL/PHP_COUCHDB/src/Auth/JwtBearerAuthenticator.php), [JwtApiController](D:/PHP_NOSQL/PHP_COUCHDB/src/Auth/JwtApiController.php) | API; không nhầm JWT với CouchDB auth |
| API contract/routes | [Entry point](D:/PHP_NOSQL/PHP_COUCHDB/public/index.php), [OpenAPI](D:/PHP_NOSQL/PHP_COUCHDB/docs/openapi.yaml) | Source routes ưu tiên contract nếu khác |
| PHP REST/cURL | [CouchDbClient](D:/PHP_NOSQL/PHP_COUCHDB/src/Infrastructure/CouchDB/CouchDbClient.php), [CouchDbResponse](D:/PHP_NOSQL/PHP_COUCHDB/src/Infrastructure/CouchDB/CouchDbResponse.php) | Timeouts/status/JSON; không tự merge 409 |
| Import fixture | [Import seed](D:/PHP_NOSQL/PHP_COUCHDB/scripts/import_seed.php), [Fixture JSON](D:/PHP_NOSQL/PHP_COUCHDB/database/couchdb/seeds/retail_order_delivery.json), [Isolated verify](D:/PHP_NOSQL/PHP_COUCHDB/scripts/verify_seed_isolated.php) | 347 gồm validator; guard _test; Phase 05/06 PASS |
| Demo 56 business / 27 orders / scenarios | [Demo seeder](D:/PHP_NOSQL/PHP_COUCHDB/scripts/seed_phase06_demo.php), [Demo verifier](D:/PHP_NOSQL/PHP_COUCHDB/scripts/verify_phase06_demo.php), Phase 06/07 | Tổng hợp, baseline; không giữ count cố định sau checkout |
| Export SQL và transform | [Exporter](D:/PHP_NOSQL/PHP_COUCHDB/scripts/export_sql_server_snapshot.ps1), [SQL export](D:/PHP_NOSQL/PHP_COUCHDB/database/couchdb/migrations/export_sql_server_snapshot.sql), [Preflight](D:/PHP_NOSQL/PHP_COUCHDB/database/couchdb/tools/validate_sql_snapshot.py), [Transform](D:/PHP_NOSQL/PHP_COUCHDB/database/couchdb/tools/transform_sql_snapshot.py), [Snapshot importer](D:/PHP_NOSQL/PHP_COUCHDB/scripts/import_sql_snapshot.php) | Không gọi fixture là backup live; không đưa snapshot chứa PII vào report |
| Backup/export/restore | [Backup script](D:/PHP_NOSQL/PHP_COUCHDB/scripts/database_backup.php), DB README, Composer | Có implementation; chưa có round-trip evidence |
| 12 Mango requests/results | [Query folder](D:/PHP_NOSQL/PHP_COUCHDB/database/couchdb/demo_queries/README.md), Phase 07 | 12 HTTP 200; 02/11/12 GUI; 08 guest = 0; 07 chỉ pending COD |
| Index 9 + primary | [Index definitions](D:/PHP_NOSQL/PHP_COUCHDB/database/couchdb/indexes/catalog_indexes.json), [Create indexes](D:/PHP_NOSQL/PHP_COUCHDB/scripts/create_catalog_indexes.php), Phase 07 | Không dedicated delivery-status/guest index |
| Explain/statistics | [Diagnostics](D:/PHP_NOSQL/PHP_COUCHDB/scripts/couchdb_query_diagnostics.php), Phase 07 | 3 explain; số ms không benchmark |
| _rev/409/validator 403 | [Fixture script](D:/PHP_NOSQL/PHP_COUCHDB/scripts/phase07_nosql_fixture_demo.php), Phase 07 | 1→2/stale 409/reject 403; cleanup fixture; negative document có nhiều lỗi |
| Conflict xử lý ở app | OrderWorkflowService::updateOrder, AccountRepository, ProductAdminService, CheckoutService | Có giới hạn retry; không merge tùy ý |
| Validation/history limitations | Validator JSON, OrderWorkflowService | Order history chỉ array; payment_history omission chưa chặn riêng |
| Realtime _changes/SSE | [RealtimeController](D:/PHP_NOSQL/PHP_COUCHDB/src/Orders/OrderRealtimeController.php), [StreamedResponse](D:/PHP_NOSQL/PHP_COUCHDB/src/Core/StreamedResponse.php), [Realtime JS](D:/PHP_NOSQL/PHP_COUCHDB/public/assets/js/order-realtime.js), [Phase 04](D:/PHP_NOSQL/PHP_COUCHDB/docs/PHASE_04_REALTIME_ORDER_DELIVERY_REPORT.md) | Longpoll ≤ 8 s / retry 5 s; offline/online chưa verify |
| Review | [ReviewService](D:/PHP_NOSQL/PHP_COUCHDB/src/Reviews/ReviewService.php), [ReviewAdminController](D:/PHP_NOSQL/PHP_COUCHDB/src/Reviews/ReviewAdminController.php) | Reply/moderation; không đưa review thật vào hình |
| Revenue | [RevenueReportService](D:/PHP_NOSQL/PHP_COUCHDB/src/Reporting/RevenueReportService.php), [ReportingController](D:/PHP_NOSQL/PHP_COUCHDB/src/Reporting/ReportingController.php) | Sum grand_total delivered, không cash-collected |
| Regression | [Checkout smoke](D:/PHP_NOSQL/PHP_COUCHDB/scripts/smoke_checkout.php), [HTTP smoke](D:/PHP_NOSQL/PHP_COUCHDB/scripts/http_smoke_checkout.php), [JWT smoke](D:/PHP_NOSQL/PHP_COUCHDB/scripts/jwt_token_smoke.php), [Failure smoke](D:/PHP_NOSQL/PHP_COUCHDB/scripts/smoke_couchdb_unavailable.php), Phase 06 | PASS lịch sử; không rerun trong nhiệm vụ tài liệu |
| Reliability/test isolation | [Phase 05](D:/PHP_NOSQL/PHP_COUCHDB/docs/PHASE_05_RELIABILITY_REPORT.md) | Seed guard/503; test DB riêng |
| Baseline/stabilization | [Phase 01](D:/PHP_NOSQL/PHP_COUCHDB/docs/PHASE_01_STABILIZATION_REPORT.md) | Seed verify từ chối data ngoài fixture |
| Delivery implementation | [Phase 02](D:/PHP_NOSQL/PHP_COUCHDB/docs/PHASE_02_ORDER_DELIVERY_REPORT.md) | Manual pending lúc đó; sau được Phase 03B/06 verify |
| Online/deployment chưa có | [Phase 08](D:/PHP_NOSQL/PHP_COUCHDB/docs/PHASE_08_RENDER_DEPLOYMENT_REPORT.md) | BLOCKED/NOT DEPLOYED; không có URL public |
| Tổng kết và bàn giao | [Completion report](D:/PHP_NOSQL/PHP_COUCHDB/docs/PROJECT_COMPLETION_REPORT.md), [Handover](D:/PHP_NOSQL/PHP_COUCHDB/docs/PROJECT_HANDOVER_SUMMARY.md) | PARTIAL tổng thể; runtime chưa kiểm tra lại |

## 2. Cơ sở lý thuyết và so sánh công cụ

**Bảng 2.1. Nguồn nghiên cứu bên ngoài**

| Nội dung | Nguồn chính thức | Giới hạn |
|---|---|---|
| HTTP/document/MVCC | [CouchDB Overview](https://docs.couchdb.org/en/stable/intro/overview.html) | Không quy atomic một doc thành transaction nhiều doc |
| Fauxton/các thao tác | [Fauxton tour](https://docs.couchdb.org/en/stable/intro/tour.html) | Tài liệu tool không chứng minh đã click trong project |
| Mango/index/explain | [Mango API](https://docs.couchdb.org/en/stable/api/database/find.html) | Project source/report chứng minh query cụ thể |
| Validation/design doc | [Design documents](https://docs.couchdb.org/en/stable/ddocs/ddocs.html) | Rule project phải đọc validator thực tế |
| Replication/conflict | [Replication intro](https://docs.couchdb.org/en/stable/replication/intro.html), [Conflict model](https://docs.couchdb.org/en/stable/replication/conflicts.html) | Replication chưa demo |
| Changes/attachment/compact | [Changes](https://docs.couchdb.org/en/stable/api/database/changes.html), [Attachments](https://docs.couchdb.org/en/stable/api/document/attachments.html), [Compaction](https://docs.couchdb.org/en/stable/maintenance/compaction.html) | Attachment/compact mới nghiên cứu |
| Compass UI/query/aggregation | [Compass overview](https://www.mongodb.com/docs/compass/) | Không cài Compass ở nhiệm vụ này |
| Compass visualization/monitoring/index | [Schema](https://www.mongodb.com/docs/compass/schema/), [Performance](https://www.mongodb.com/docs/compass/performance/), [Indexes](https://www.mongodb.com/docs/compass/indexes/) | So sánh chức năng, không benchmark |

Nhận xét phù hợp học tập là suy luận theo mục tiêu đồ án. Bảng so sánh không chứng minh đã chạy một experiment Fauxton–Compass tương đương.

## 3. Khác biệt cần xử lý nhất quán

**Bảng 3.1. Quyết định diễn giải**

| Vấn đề | Diễn giải dùng trong báo cáo mới |
|---|---|
| Phase 01 guest admin 302; Phase 06 ghi 403 | Source staffGuard: chưa auth trả 302; đã auth nhưng thiếu quyền trả 403 |
| Healthy Phase 06/07; unavailable Phase 08 | Khác thời điểm/môi trường audit; không kết luận runtime hôm nay PASS |
| Retail default vs migration DB | Default cấu hình mẫu khác tên DB được quan sát |
| 347/348/56/58 docs | 347 fixture gồm validator; 348 business DB app; 56 business demo; 58 demo gồm 2 design docs |
| Report nói history append-only | Delivery bình thường có guard; status_history chỉ array; payment_history có omission limit |
| README nói design docs trước business khi restore | Source chỉ sort mảng, có thể chung batch _bulk_docs; chưa chứng minh validator luôn có trước ghi |
| Invalid_status → 403 | Fixture còn nhiều field chưa hợp lệ; 403 chứng minh reject sai document, không tách từng rule |
| Phase 03B/09 files | 03B ở report 03; 09 không có report riêng, chỉ deliverables báo cáo |
| Tool create DB/document CRUD | Có workflow nghiên cứu; DB demo do script; không bịa GUI CRUD |
| User management | Có auth/register/role/provision; chưa có admin CRUD user UI đầy đủ |
| Render guide | Kế hoạch, không triển khai thật |

## 4. Khoảng trống bằng chứng

- CHƯA XÁC MINH: backup/restore thực tế, content parity, validator sau phục hồi và backup uploads.
- CHƯA XÁC MINH: runtime local hiện tại và các ảnh báo cáo mới.
- CHƯA XÁC MINH bằng GUI: create/delete/compact DB, document CRUD đầy đủ; chỉ mô tả nghiên cứu.
- CHƯA XÁC MINH: guest order trong baseline demo; query 08 = 0.
- CHƯA XÁC MINH: browser network offline/online và tải SSE lớn.
- CHƯA TRIỂN KHAI: replication/attachment trong app, full admin user CRUD, gateway/refund, GPS/shipper, public deployment.

## 5. Checklist kiểm soát claim

- [x] 17 chương và phụ lục gắn với source/report hoặc tài liệu chính thức.
- [x] 12 query JSON lấy nguyên từ source; không thay selector/result.
- [x] Kết quả test ghi thời điểm và loại kiểm chứng GUI/API/automated.
- [x] Không xuất secret, credential file, password hash hoặc receiver thật.
- [x] Phần lý thuyết và nghiên cứu không được ghi thành demo đã chạy.
- [ ] Chèn ảnh thật và ghi ngày/DB/role của từng ảnh.
- [ ] Bổ sung round-trip backup/restore và kiểm tra lại runtime trước bảo vệ.
