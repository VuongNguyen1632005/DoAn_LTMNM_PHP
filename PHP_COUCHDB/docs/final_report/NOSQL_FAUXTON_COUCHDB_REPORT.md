# BÁO CÁO MÔN HỌC NOSQL

**TRƯỜNG:** [ĐIỀN TÊN TRƯỜNG]  
**KHOA:** [ĐIỀN TÊN KHOA]  
**MÔN HỌC:** [ĐIỀN TÊN MÔN HỌC]  
**GIẢNG VIÊN:** [ĐIỀN HỌ TÊN GIẢNG VIÊN]  
**SINH VIÊN:** [ĐIỀN HỌ TÊN SINH VIÊN]  
**MSSV:** [ĐIỀN MSSV]  
**LỚP:** [ĐIỀN LỚP]  
**NĂM HỌC:** [ĐIỀN NĂM HỌC]

## ĐỀ TÀI

“Tìm hiểu công cụ Fauxton để quản trị CSDL tài liệu CouchDB cho hệ thống
‘Quản lý đơn hàng và giao hàng của cửa hàng bán lẻ trực tuyến’.
Xây dựng ứng dụng minh họa.”

Ứng dụng minh họa: **WEBTHOITRANG PHP + CouchDB**.  
Project: `D:\PHP_NOSQL\PHP_COUCHDB`.  
Ngày biên soạn: **09/10/2026**.

## Quy ước đọc và phạm vi bằng chứng

Báo cáo được viết theo source hiện tại và các Phase report đã lưu. Kết quả thực nghiệm chủ yếu thuộc ngày **07/10/2026**; việc biên soạn ngày 09/10 không phải một lần chạy kiểm thử mới. Kết quả cũ không bảo đảm dịch vụ trên máy đang hoạt động ở thời điểm đọc báo cáo. Database minh họa là database CouchDB đã được tạo và truy vấn thực tế, chứa **dữ liệu tổng hợp phục vụ demo**, không phải dữ liệu cá nhân của khách hàng thật.

Các nhãn sử dụng: **ĐÃ TRIỂN KHAI** là có source; **ĐÃ KIỂM CHỨNG** là có kết quả trong Phase report; **RESEARCHED – ĐÃ NGHIÊN CỨU** là có cơ sở tài liệu nhưng chưa demo; **CHƯA XÁC MINH** là thiếu bằng chứng thực nghiệm; **CHƯA TRIỂN KHAI** là chưa có chức năng trong phạm vi project. Các placeholder hình không phải ảnh đã chụp. Source được ưu tiên khi khác với diễn giải của report cũ.

**Bảng 0.1. Liên kết nội dung với 10 tiêu chí đánh giá**

| Tiêu chí | Nội dung chứng minh | Vị trí | Mức sẵn sàng theo hồ sơ |
|---|---|---|---|
| 1. Lý thuyết và cài đặt tool | NoSQL, CouchDB, Docker, URL Fauxton và sử dụng local | Chương 1–2 | READY; cần hình và xác nhận runtime trước buổi bảo vệ |
| 2. Chi tiết chức năng tool | Database/document, JSON, Mango, index, design doc, validation, replication, attachment | Chương 3, 9–12 | READY về nội dung; thao tác nghiên cứu được ghi riêng |
| 3. So sánh GUI | Fauxton với MongoDB Compass theo tài liệu chính thức | Chương 4 | READY; không coi Compass là công cụ đã cài/demo |
| 4. Điểm mạnh và ứng dụng phù hợp | Aggregate đơn, snapshot, HTTP, giới hạn đồng thời và giao dịch | Chương 4, 6, 17 | READY |
| 5. Database và dữ liệu | Demo DB, bộ 56 business document, order/delivery scenarios | Chương 6–7 | READY theo Phase 06/07; guest query baseline trả 0 |
| 6. Import/export/backup/restore | Importer, backup JSON, restore vào DB mới, quy trình kiểm tra | Chương 8, Phụ lục C | Đạt trên demo local ngày 10/10/2026; cần chèn ảnh thật |
| 7. Truy vấn GUI | 12 Mango request; 3 request đã chạy trực tiếp trong Fauxton | Chương 9–10 | READY theo Phase 07 |
| 8. Database kết nối ứng dụng | PHP cURL/REST, Repository, quyền và JSON | Chương 13 | READY |
| 9. Demo ứng dụng | Tài khoản/role, CRUD mềm sản phẩm, order, delivery, chức năng hỗ trợ | Chương 14–15; kịch bản demo riêng | READY trong phạm vi source; không có màn hình quản lý user đầy đủ |
| 10. Báo cáo và bảo vệ | Chương, bảng, JSON, vị trí hình, source map và hỏi đáp | Toàn báo cáo và 4 tài liệu hỗ trợ | PARTIAL; ảnh, thông tin bìa và dàn trang Word còn thiếu |

READY chỉ mô tả tài liệu/bằng chứng sẵn có, không phải điểm giảng viên đã chấm. Mục tiêu dàn trang là khoảng **35–50 trang Word sau khi chèn hình**; số trang thực tế phụ thuộc quy định trường, kích thước hình và kiểu chữ.

## Mục lục nội dung

Lời mở đầu; Chương 1 Tổng quan NoSQL/CouchDB; Chương 2 Tìm hiểu và cài đặt Fauxton; Chương 3 Chức năng Fauxton; Chương 4 So sánh GUI; Chương 5 Phân tích bài toán; Chương 6 Thiết kế database; Chương 7 Tạo và nhập dữ liệu; Chương 8 Import/export/backup/restore; Chương 9 Mango Query; Chương 10 Mango Index; Chương 11 Revision/conflict; Chương 12 Validation; Chương 13 PHP kết nối CouchDB; Chương 14 Demo ứng dụng; Chương 15 Realtime; Chương 16 Kiểm thử; Chương 17 Đánh giá; Kết luận; Tài liệu tham khảo; Phụ lục A–D. Khi chuyển Word, dùng Heading và tạo mục lục tự động, không tự điền số trang chưa xác định.

## LỜI MỞ ĐẦU

Hoạt động bán lẻ trực tuyến tạo ra nhu cầu quản lý xuyên suốt từ lúc khách chọn sản phẩm đến khi cửa hàng giao hàng và ghi nhận thanh toán. Một đơn hàng không chỉ có danh sách sản phẩm mà còn gồm người nhận, phương thức vận chuyển, tổng tiền, trạng thái xử lý và lịch sử giao hàng. Nếu các thông tin này thiếu liên kết hoặc bị ghi đè khi danh mục thay đổi, cửa hàng khó giải thích giao dịch cũ, còn khách hàng khó theo dõi tiến trình nhận hàng.

Dữ liệu đơn hàng có cấu trúc chung nhưng số dòng sản phẩm, số sự kiện và mức độ đầy đủ của tracking có thể khác nhau. Đơn cũ có thể chưa lưu mã vận đơn; đơn mới có thêm lịch sử giao thất bại và thử giao lại. Đặc điểm đó đặt ra yêu cầu lưu dữ liệu linh hoạt, đồng thời vẫn kiểm tra được những trường quan trọng. Linh hoạt không có nghĩa bỏ qua ràng buộc: trạng thái, số lượng, số tiền và quyền cập nhật vẫn phải được kiểm soát.

CouchDB được lựa chọn trong đồ án để nghiên cứu mô hình cơ sở dữ liệu tài liệu. Một order document có thể chứa các dòng sản phẩm và thông tin giao hàng lồng bên trong, phù hợp với cách màn hình chi tiết đơn đọc dữ liệu. HTTP REST và JSON cũng thuận tiện cho ứng dụng PHP. Các cơ chế Mango Query, index, revision và validation tạo điều kiện minh họa cả truy vấn lẫn kiểm soát cập nhật đồng thời. Đây là lựa chọn phục vụ mục tiêu học tập; báo cáo không khẳng định CouchDB thay thế mọi cơ sở dữ liệu quan hệ.

Fauxton đóng vai trò công cụ quản trị và quan sát database. Qua giao diện này, người thực hiện có thể xem document, đọc lịch sử, chạy truy vấn và kiểm tra index. Website PHP là ứng dụng nghiệp vụ dành cho khách hàng, nhân viên và quản lý. Việc phân biệt hai giao diện giúp phần demo thể hiện được quan hệ giữa thao tác nghiệp vụ và dữ liệu thực sự được lưu trong CouchDB.

Báo cáo sử dụng project WEBTHOITRANG PHP + CouchDB làm ứng dụng minh họa, tập trung vào quản lý đơn hàng và giao hàng. Bộ dữ liệu tổng hợp riêng được dùng để tránh đưa thông tin khách hàng thật vào phần trình bày. Nội dung được tổ chức theo mười tiêu chí môn học, từ nghiên cứu công cụ đến database, truy vấn, tích hợp và bảo vệ. Các kết quả chưa có bằng chứng, như phục hồi backup thực tế, replication hoặc triển khai online, được ghi rõ để người đọc đánh giá đúng phạm vi hoàn thành.

# CHƯƠNG 1 – TỔNG QUAN VỀ NOSQL VÀ COUCHDB

## 1.1 Khái niệm NoSQL

NoSQL là tên chung cho các hệ quản trị có mô hình dữ liệu khác mô hình quan hệ truyền thống. Trong thực hành, thuật ngữ thường được hiểu là “Not Only SQL”: hệ thống có thể tổ chức dữ liệu bằng cặp khóa–giá trị, tài liệu, họ cột hoặc đồ thị. Nhu cầu xuất hiện từ các bài toán có cấu trúc thay đổi, nhiều dữ liệu lồng nhau hoặc cần phân phối dữ liệu giữa các nút. Mỗi sản phẩm có cơ chế nhất quán và truy vấn riêng; không thể suy ra tất cả NoSQL đều không có schema hoặc đều có cùng ưu điểm.

Đối với đồ án, điểm khác biệt dễ quan sát là cách biểu diễn đơn hàng. Thiết kế quan hệ thường tách phần đầu đơn và chi tiết thành các bảng có khóa ngoại; thiết kế document gom thông tin đọc chung vào một tài liệu. Cách thứ hai giảm nhu cầu JOIN khi đọc chi tiết, nhưng yêu cầu ứng dụng tự xử lý những ràng buộc liên document. Đây là đánh đổi thiết kế theo luồng truy cập, không phải kết luận SQL kém phù hợp với bán hàng.

## 1.2 Các nhóm NoSQL phổ biến

**Bảng 1.1. Các mô hình NoSQL và liên hệ với bài toán**

| Nhóm | Cách tổ chức | Ví dụ nhu cầu |
|---|---|---|
| Key–Value | Giá trị được truy cập bằng khóa | Cache, session, cấu hình theo khóa |
| Document | Tài liệu có trường, object và array | Order có `items[]`, snapshot và lịch sử |
| Column-family | Dữ liệu tổ chức theo hàng và họ cột | Luồng ghi lớn, truy vấn theo partition đã thiết kế |
| Graph | Đỉnh và cạnh mô tả quan hệ | Mạng quan hệ, đường đi, gợi ý dựa trên kết nối |

Project sử dụng nhóm document. Không cài thêm các hệ quản trị thuộc ba nhóm còn lại để minh họa bảng này.

## 1.3 Document Database

Document là đơn vị dữ liệu tự chứa các trường cần thiết cho một đối tượng. JSON dùng object `{}` để gom cặp tên–giá trị và array `[]` để lưu nhiều phần tử. Trong order, `payment` là object, `items` là array các object, còn `delivery_tracking.history` là array nằm trong một object khác. Giá trị có thể là chuỗi, số, boolean, null, object hoặc array.

Schema linh hoạt cho phép document cũ thiếu một trường mới nếu chương trình và validator chấp nhận. Project giữ order legacy không có `delivery_tracking`; khi hiển thị, UI thông báo chưa có thông tin giao hàng. Đồng thời, business document vẫn phải có `type`, `schema_version: 2` và các trường bắt buộc theo validator. Hai yêu cầu này bổ sung cho nhau: cấu trúc có thể mở rộng nhưng dữ liệu cốt lõi vẫn có contract.

## 1.4 Giới thiệu CouchDB

Apache CouchDB lưu tài liệu JSON và cung cấp API HTTP. Client gửi GET để đọc, PUT để tạo/cập nhật theo ID, POST cho các API như `_find`, và nhận response JSON. Cơ chế MVCC cho phép kiểm tra revision khi ghi; cập nhật nguyên tử ở cấp một document không tương đương transaction nhiều document. [Tài liệu tổng quan CouchDB](https://docs.couchdb.org/en/stable/intro/overview.html)

CouchDB có replication để đồng bộ giữa database. Khi nghiên cứu hệ phân tán, eventual consistency cần được hiểu trong bối cảnh các bản sao và thời gian đồng bộ: một replica có thể chưa nhận ngay thay đổi ở replica khác. Đồ án chạy local, chưa dựng mô hình replication nên không có kết quả thực nghiệm về hội tụ hoặc xử lý conflict giữa replica. [Tài liệu replication](https://docs.couchdb.org/en/stable/replication/intro.html)

## 1.5 Các thành phần quan trọng

**Bảng 1.2. Thuật ngữ dùng trong báo cáo**

| Thành phần | Ý nghĩa | Minh họa project |
|---|---|---|
| Database | Không gian chứa documents và design documents | `shopquan_ao_phase06_demo_test` |
| Document | Một đối tượng JSON có định danh | Một order hoặc product |
| `_id` | Định danh document trong database | `order:P06-0020` |
| `_rev` | Token revision do CouchDB cấp cho phiên bản ghi | Kiểm tra stale write ở Chương 11 |
| Design document | Document đặc biệt chứa định nghĩa xử lý/index | `_design/domain_validation` |
| Mango Query | Request JSON selector qua `_find` | Lọc order `shipping` |
| Mango Index | Cấu trúc hỗ trợ tìm kiếm theo trường | `customer_orders` |
| `_changes` | Feed thông báo thay đổi database | PHP theo dõi một order |
| Validation | Kiểm tra document trước khi chấp nhận ghi | Từ chối status ngoài enum |
| Replication | Đồng bộ database giữa nguồn và đích | Đã nghiên cứu; chưa triển khai |

`_rev`, `schema_version` và `status_history` có ba mục đích khác nhau. `_rev` bảo vệ cập nhật đồng thời; schema version nhận diện contract dữ liệu; history là lịch sử nghiệp vụ do ứng dụng lưu. Không dùng revision làm thay thế cho nhật ký giao hàng.

## 1.6 Vì sao CouchDB phù hợp với đề tài

Trang Order Detail cần đọc người nhận, `items[]`, shipping, payment, totals và lịch sử. Việc đặt các dữ liệu đó trong một order aggregate phù hợp với thao tác đọc một đơn. Khi delivery hoàn tất, order status và delivery status được cập nhật trong cùng document write. Snapshot tên/giá giữ thông tin của giao dịch dù catalog đổi sau đó.

Tuy nhiên, đặt hàng còn thay đổi product stock và voucher. Các đối tượng này không cùng một document với order, nên service sử dụng journal, marker và bù trừ; CouchDB không tự biến toàn bộ checkout thành transaction nhiều bảng. Lựa chọn phù hợp trong đồ án là đặt các thông tin cần đọc/cập nhật chung cạnh nhau, giữ dữ liệu có vòng đời riêng thành document riêng.

Nguồn project: schema mapping, `CheckoutService`, `OrderWorkflowService` và Phase 07; xem source map đi kèm.

# CHƯƠNG 2 – TÌM HIỂU VÀ CÀI ĐẶT FAUXTON

## 2.1 Fauxton là gì

Fauxton là giao diện web quản trị được tích hợp với CouchDB. Website PHP phục vụ quy trình bán hàng và phân quyền nghiệp vụ; Fauxton phục vụ người quản trị database. Người customer không cần tài khoản CouchDB và không được hướng dẫn truy cập Fauxton để xem đơn của mình. [CouchDB – Fauxton tour](https://docs.couchdb.org/en/stable/intro/tour.html)

## 2.2 Cài đặt CouchDB theo cấu hình project

Project dùng Docker Compose với hai service: `app` xây từ `php:8.3-apache`, `couchdb` dùng image `couchdb:3.5.0`. Named volume `couchdb_data` gắn tại `/opt/couchdb/data`. Fauxton được cung cấp cùng CouchDB; không có bước cài một phần mềm Fauxton desktop riêng.

**Bảng 2.1. Cấu hình local được đối chiếu**

| Thành phần | Cấu hình source | Bằng chứng thực nghiệm |
|---|---|---|
| PHP/Apache | `Dockerfile`, PHP 8.3 | Phase 06 ghi PHP 8.3.35 |
| CouchDB | Compose image `couchdb:3.5.0` | Phase 07 Fauxton hiển thị 3.5.0 |
| App host port | Loopback, mặc định 8081 | Phase 06 app chính 8081 |
| Demo app riêng | Không phải port mặc định Compose | Phase 06 ghi demo tại 8082 |
| CouchDB host port | Loopback, mặc định 5984 | Phase 07 truy cập Fauxton tại 5984 |
| Kết nối trong Docker | `http://couchdb:5984` | `COUCHDB_URL` của app |
| Database mặc định mẫu | `retail_order_delivery_test` | Fallback/template, không phải DB local đã quan sát |

Quy trình dựng local từ thư mục project:

```powershell
Set-Location D:\PHP_NOSQL\PHP_COUCHDB
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
# Người vận hành tự điền secret local trong .env; không đưa vào báo cáo.
docker compose up --build -d
docker compose ps
Invoke-WebRequest http://127.0.0.1:8081/health/ready
```

Đây là lệnh có trong cấu hình/hướng dẫn, không phải biên bản vừa chạy ngày 09/10. Readiness cần database đã cấu hình tồn tại; container healthy của CouchDB riêng chưa đủ chứng minh PHP đọc được database. Không tự seed một database đang có dữ liệu để xử lý readiness lỗi.

## 2.3 Truy cập Fauxton

URL đã xác minh trong Phase 07 là `http://127.0.0.1:5984/_utils/`. Người vận hành đăng nhập bằng tài khoản CouchDB trong môi trường local; báo cáo không chứa user/password thật. Cổng host có thể đổi bằng cấu hình, vì vậy trước buổi demo phải kiểm tra URL đang dùng. Không đồng nhất tài khoản Fauxton với tài khoản customer/staff của website.

[CHÈN HÌNH 2.1: Giao diện Fauxton sau đăng nhập, không lộ credential TẠI ĐÂY]

## 2.4 Giao diện Fauxton

Các vùng đã mở trong Phase 07 gồm Databases, All Documents, editor document, Design Documents, Mango Query và Manage Indexes. Khi chọn database demo, người thực hiện mở `order:P06-0020` để đọc JSON, sau đó mở validator/index để đối chiếu với source. Replication và Configuration là chức năng quản trị được nghiên cứu; không có biên bản chỉnh cấu hình hoặc chạy replication.

[CHÈN HÌNH 2.2: Danh sách database và tên database demo TẠI ĐÂY]

[CHÈN HÌNH 2.3: All Documents của database demo, số document tại thời điểm chụp TẠI ĐÂY]

## 2.5 Tạo database bằng Fauxton

Quy trình thao tác cần trình bày: mở Databases → chọn chức năng tạo database → nhập một tên test chưa tồn tại → xác nhận → kiểm tra database xuất hiện và mở All Documents. Chỉ dùng tên database cô lập được chuẩn bị cho buổi học, không xóa hoặc thay thế database ứng dụng.

**Trạng thái:** đã nghiên cứu thao tác tạo database trên tool; **CHƯA XÁC MINH việc tạo database bằng click Fauxton** trong Phase 07. Database demo có thật nhưng được tạo bằng seeder PHP. Cần phân biệt “đã tạo database” với “đã tạo database bằng GUI”. [Hướng dẫn Fauxton chính thức](https://docs.couchdb.org/en/stable/intro/tour.html)

Nguồn: `Dockerfile`, `docker-compose.yml`, README, Phase 06/07. Phase 08 báo local engine/endpoint không khả dụng trong lượt audit khác; không lấy kết quả healthy cũ làm xác nhận dịch vụ hiện tại.

# CHƯƠNG 3 – CHỨC NĂNG CỦA FAUXTON

## 3.1 Quản lý database

**Mục đích:** tạo không gian lưu dữ liệu, xem database và thông tin vận hành. **Cách dùng:** Databases để chọn/tạo database; vùng thông tin database để xem count/size; chức năng xóa hoặc compact thuộc quản trị. **Liên hệ:** database demo tách khỏi database ứng dụng để trình bày dữ liệu tổng hợp. Create qua GUI, Delete qua GUI và Compact chưa có biên bản demo trong project.

Compact phục vụ thu hồi không gian từ dữ liệu cũ; không được xem là backup và không phải nút hoàn tác. Báo cáo chỉ mô tả chức năng, không đưa thao tác xóa database vào flow demo chính. CouchDB mô tả compaction trong tài liệu vận hành. [CouchDB compaction](https://docs.couchdb.org/en/stable/maintenance/compaction.html)

## 3.2 Quản lý document

**Mục đích:** tạo, đọc, sửa, xóa một document. **Cách dùng:** mở All Documents, chọn document để đọc JSON; dùng chức năng tạo mới hoặc chỉnh sửa và lưu; cập nhật cần revision hiện hành. Xóa document là thao tác database, khác với ẩn mềm sản phẩm trong website. **Liên hệ:** Phase 07 đã mở order thật trong database demo; chưa ghi nhận CRUD đầy đủ bằng click Fauxton. Product CRUD của ứng dụng PHP và revision fixture là bằng chứng khác, không được ghi thành GUI CRUD.

## 3.3 JSON Editor

**Mục đích:** quan sát cấu trúc lồng và sửa JSON. **Cách dùng:** mở document, tìm `items`, `status_history`, `delivery_tracking`; kiểm tra dấu ngoặc, kiểu giá trị và field bắt buộc trước khi lưu. **Liên hệ:** editor giúp chứng minh một order có nhiều item và hai lịch sử riêng. Trong Phase 07 editor được dùng để đọc; không Save trên database demo. JSON parse hợp lệ vẫn có thể bị validator từ chối do sai contract.

## 3.4 Trường `_id`

**Mục đích:** nhận diện duy nhất document trong một database. **Cách dùng:** đọc ở đầu JSON, chọn đúng document theo ID hoặc truy cập document endpoint. **Liên hệ:** `order:P06-0020` và `product:P06-001` là mã synthetic. Prefix giúp phân biệt loại nhưng validator vẫn kiểm tra trường `type`; prefix không tự tạo quyền truy cập.

## 3.5 Trường `_rev`

**Mục đích:** phát hiện bản cập nhật dựa trên revision cũ. **Cách dùng:** quan sát revision trước/sau thao tác; đọc lại document trước khi sửa. **Liên hệ:** order mẫu có generation 3 trong lượt Phase 07; fixture tăng 1→2 và stale PUT trả 409. Không dự đoán digest và không tự tăng chuỗi `_rev`. Fauxton đọc revision; demo stale write đã thực hiện bằng script/API.

## 3.6 Mango Query

**Mục đích:** lọc documents bằng selector JSON. **Cách dùng:** chọn Mango Query, dán request từ file demo, chạy và xem results, warning, execution statistics. **Liên hệ:** UI đã chạy shipping, failed delivery và tracking code. `fields` giới hạn dữ liệu trả về để phần trình bày không cần mở người nhận; `limit` giới hạn mỗi lượt. Chương 9 trình bày đủ 12 request, không chỉ một truy vấn tổng quát.

## 3.7 Mango Index

**Mục đích:** hỗ trợ đường truy cập phù hợp với selector/sort. **Cách dùng:** Manage Indexes để xem định nghĩa; đối chiếu `_explain` với index dự kiến. **Liên hệ:** chín JSON index đã có, không tạo index trùng cho buổi demo. `use_index` thể hiện lựa chọn mong muốn nhưng vẫn phải đọc kết quả/cảnh báo; không coi việc có index là bảo đảm mọi query nhanh. [Mango API](https://docs.couchdb.org/en/stable/api/database/find.html)

## 3.8 Design Document

**Mục đích:** lưu định nghĩa xử lý/index đặc biệt của CouchDB. **Cách dùng:** vùng Design Documents hoặc All Documents để mở `_design/...`. **Liên hệ:** `_design/catalog_indexes` có `language: query`; `_design/domain_validation` chứa JavaScript validator. Không giới thiệu định nghĩa index là một hệ business MapReduce view đã tự viết, vì source chưa có hệ view như vậy.

## 3.9 Validation

**Mục đích:** từ chối document không thỏa điều kiện khi ghi. **Cách dùng:** đọc `validate_doc_update`, thực hiện thử dữ liệu sai trên fixture riêng và quan sát HTTP/error. **Liên hệ:** kiểm tra type, schema version, order enum, item/totals, delivery và một số history. Chương 12 phân biệt validator với nghiệp vụ PHP; đọc code trên Fauxton không thay thế việc kiểm thử từng rule. [Design documents và validation](https://docs.couchdb.org/en/stable/ddocs/ddocs.html)

## 3.10 Replication

**Mục đích:** đồng bộ database nguồn–đích. **Cách dùng nghiên cứu:** mở Replication, chọn nguồn/đích, loại đồng bộ phù hợp và theo dõi tiến trình. **Liên hệ:** có thể phù hợp với cửa hàng có bản sao ở nhiều địa điểm, nhưng project chưa cấu hình hay đo replication. **RESEARCHED – ĐÃ NGHIÊN CỨU; CHƯA TRIỂN KHAI.** Không đưa ảnh từ tài liệu bên ngoài như ảnh demo project. [Replication CouchDB](https://docs.couchdb.org/en/stable/replication/intro.html)

## 3.11 Attachment

**Mục đích:** gắn nội dung tệp vào document thông qua attachment API. **Cách dùng nghiên cứu:** thao tác attachment của document, xem metadata và tải nội dung. **Liên hệ:** project hiện dùng `images[].path` và file storage, không lưu ảnh bằng `_attachments`; order snapshot không chứa ảnh. **RESEARCHED – project hiện tại không dùng CouchDB Attachment để lưu ảnh.** [Attachment API](https://docs.couchdb.org/en/stable/api/document/attachments.html)

## 3.12 Database Information

**Mục đích:** xem thông tin database như số documents và dung lượng, hỗ trợ kiểm tra sau import. **Cách dùng:** mở database information và ghi thời điểm chụp. **Liên hệ:** cần phân biệt 56 business docs với tổng 58 docs gồm design documents. Số document là thông tin kiểm kê; không đủ chứng minh tất cả trường, quan hệ hoặc file ảnh hợp lệ.

**Bảng 3.1. Phân loại bằng chứng chức năng tool**

| Chức năng | Đã dùng trong hồ sơ | Chưa xác minh bằng GUI |
|---|---|---|
| Database/Documents | Mở demo DB, All Documents, order JSON | Tạo/xóa/compact bằng click |
| JSON/design document | Đọc order, validator và index design doc | CRUD JSON đầy đủ trong Fauxton |
| Mango/index | 3 query UI, Manage Indexes | Toàn bộ 12 query đều chạy UI |
| Revision/validator | Xem revision; fixture API 409/403 | Tái hiện stale write bằng editor GUI |
| Replication/attachment | Nghiên cứu tài liệu | Cấu hình và thực nghiệm trong project |

# CHƯƠNG 4 – SO SÁNH FAUXTON VỚI MONGODB COMPASS

So sánh này dựa trên tài liệu chính thức, không phải thử nghiệm hai công cụ trên cùng dataset. Compass thuộc hệ sinh thái MongoDB; Fauxton thuộc CouchDB. Không dùng Compass để mở database CouchDB của đồ án.

**Bảng 4.1. So sánh công cụ theo chức năng**

| Tiêu chí | Fauxton | MongoDB Compass |
|---|---|---|
| Database hỗ trợ | CouchDB | MongoDB |
| Cài đặt | Đi kèm CouchDB; truy cập bằng browser | Ứng dụng desktop, cài riêng |
| UI | Giao diện web theo database/document | Giao diện theo database/collection và các tab |
| Document management | Đọc/sửa JSON document, revision | Insert/read/update/delete document MongoDB |
| Query | Mango selector; CouchDB views theo chức năng | MongoDB query filter |
| Index | Mango index của CouchDB | Index của MongoDB; có tab quản lý |
| Visualization | Quan sát document và kết quả truy vấn | Schema analysis với biểu đồ phân bố trường |
| Monitoring | Vùng quản trị CouchDB, thông tin database/tác vụ | Performance tab; phạm vi tùy quyền và deployment |
| Aggregation | CouchDB MapReduce/view là cơ chế khác; app chưa có business view | Aggregation Pipeline Builder |
| Administration | Database, design doc, cấu hình, replication | Quản lý dữ liệu/index, shell và công cụ tương ứng MongoDB |
| Độ đơn giản | Thuận tiện khi cần đọc JSON và học CouchDB | Nhiều công cụ phân tích; cần hiểu MongoDB |
| Học tập | Phù hợp đề tài revision, validator, Mango, `_changes` | Phù hợp học query, schema, aggregation MongoDB |

Nguồn cho so sánh: [Fauxton tour](https://docs.couchdb.org/en/stable/intro/tour.html), [Compass overview](https://www.mongodb.com/docs/compass/), [Compass indexes](https://www.mongodb.com/docs/compass/indexes/), [Compass schema analysis](https://www.mongodb.com/docs/compass/schema/), [Compass performance](https://www.mongodb.com/docs/compass/performance/). Những nhận xét về thuận tiện học tập là đánh giá theo mục tiêu đồ án, không phải xếp hạng chất lượng chung.

## 4.1 Điểm mạnh của Fauxton

Trong đề tài này, lợi thế rõ nhất là mở trực tiếp database đã dùng bởi PHP mà không cài GUI riêng. Người thực hiện có thể chuyển từ order trên website sang JSON chứa snapshot/history, rồi chạy selector và xem index. Fauxton giúp giải thích cấu trúc dữ liệu bằng dữ liệu đang lưu, thay vì chỉ dùng sơ đồ thiết kế. Design document và revision cũng có thể xem trong cùng môi trường.

## 4.2 Điểm yếu của Fauxton

Fauxton tập trung quản trị CouchDB; giao diện document không thay thế form bán hàng có quyền và workflow. Khi sửa JSON, người quản trị phải hiểu contract để tránh tạo dữ liệu không phù hợp nghiệp vụ. Với mục tiêu phân tích schema trực quan hoặc dựng pipeline aggregation MongoDB, Compass có các vùng chức năng chuyên biệt; đồ án không đo mức độ tiện dụng bằng khảo sát người dùng. [Compass schema](https://www.mongodb.com/docs/compass/schema/), [Aggregation Pipeline Builder](https://www.mongodb.com/docs/compass/create-agg-pipeline/)

## 4.3 Loại ứng dụng phù hợp

Thiết kế document hợp với dữ liệu đọc theo aggregate, ví dụ đơn hàng có nhiều dòng và history. CouchDB cũng đáng nghiên cứu khi cần đồng bộ bản sao, nhưng khả năng replication không được xem là kết quả đã đạt của project. Nếu yêu cầu trọng tâm là giao dịch nguyên tử nhiều thực thể, JOIN linh hoạt hoặc đối soát tài chính lớn, phải đánh giá thêm mô hình quan hệ và chi phí xử lý ở application layer. Báo cáo không dùng số thời gian vài ms trên 58 documents để kết luận Fauxton/CouchDB nhanh hơn MongoDB.

# CHƯƠNG 5 – PHÂN TÍCH BÀI TOÁN

## 5.1 Đối tượng sử dụng

**Bảng 5.1. Tác nhân và quyền trong source**

| Tác nhân | Nhiệm vụ |
|---|---|
| Guest | Xem hàng, cart session, checkout, xem order được gắn với session |
| Customer | Đăng ký/đăng nhập, member cart, đặt hàng, xem đơn của mình, xác nhận đã nhận |
| Staff | Xem/quản lý order, cập nhật delivery, xác minh thanh toán theo rule, phản hồi review |
| Manager | Quyền staff và quản trị sản phẩm, ẩn/khôi phục review |

Không có role shipper. `assigned_staff_id` là field phân công ở cấp order, không chứng minh có ứng dụng giao nhận riêng. Customer/guest không có form cập nhật trạng thái vận chuyển.

## 5.2 Luồng mua hàng

```text
Product → Cart → Checkout preview → Kiểm tra lại giá/stock/voucher/ship → Order
```

Giỏ chỉ là ý định mua, chưa bảo đảm giữ nguyên giá/tồn. Checkout đọc lại dữ liệu từ CouchDB, tạo order journal, thực hiện giữ tồn/voucher và hoàn tất. Order mới có COD chưa thanh toán và trạng thái pending. Gửi lại cùng thao tác hợp lệ không được trừ stock hai lần; marker và idempotency hỗ trợ yêu cầu này.

[CHÈN HÌNH 5.1: Sơ đồ luồng mua hàng, order và delivery theo source TẠI ĐÂY]

## 5.3 Luồng Order

```text
pending → confirmed → packing → shipping → delivered
pending / confirmed / packing → cancelled (khi chưa thanh toán)
```

Sau shipping không cho hủy. Đơn đã paid bị chặn hủy vì chưa có refund. Khi có tracking chưa delivered, form không cho staff đóng order trực tiếp thành delivered; cập nhật delivery cuối đồng bộ cả hai. Chủ đơn có thể xác nhận đã nhận theo workflow riêng. Gửi lại status đã áp dụng không thêm event trùng. Quy tắc nằm trong `OrderWorkflowService`.

## 5.4 Luồng Delivery

```text
created → picked_up → in_transit → out_for_delivery → delivered
Các bước trước delivered → failed_delivery
failed_delivery → in_transit hoặc out_for_delivery
```

Tracking được khởi tạo khi order vào shipping. Giao thất bại vẫn giữ order shipping, cho phép ghi lý do và retry. `delivered` là trạng thái cuối. Thời điểm event do backend tạo theo UTC; UI sử dụng lịch sử thực sự đã lưu. ETA được hiển thị, không có form sửa ETA trong admin hiện tại.

## 5.5 Payment

**Bảng 5.2. Ba trạng thái độc lập**

| Nhóm | Ví dụ | Câu hỏi được trả lời |
|---|---|---|
| Order | pending, packing, shipping, delivered | Cửa hàng đang xử lý đơn đến bước nào? |
| Delivery | in_transit, failed_delivery, delivered | Hàng đang vận chuyển ra sao? |
| Payment | unpaid, paid | Khoản thanh toán đã được ghi nhận chưa? |

Staff cập nhật delivery delivered không tự ghi COD paid. Chủ đơn xác nhận nhận COD có rule ghi paid; staff có thể thu COD sau khi đơn đã giao. Bank transfer/card được xác minh thủ công sau đối soát ngoài hệ thống, không phải gateway. Các lần xác nhận có audit `payment_history`; chưa hỗ trợ refund hoặc chuyển ngược paid→unpaid.

# CHƯƠNG 6 – THIẾT KẾ DATABASE COUCHDB

## 6.1 Database hiện tại theo bằng chứng đã lưu

**Bảng 6.1. Phân biệt các database/dataset**

| Tên | Vai trò | Số lượng và thời điểm |
|---|---|---|
| `shopquan_ao_sql_migration_test` | DB app được quan sát trong Phase 06/07 | 348 business docs, chưa tính 2 design docs, Phase 07 |
| `shopquan_ao_phase06_demo_test` | DB tổng hợp dùng trình bày | 56 business docs + 2 design docs = 58, baseline Phase 06/07 |
| `retail_order_delivery_test` | Default trong template/fallback | Không gán counts của DB khác vào tên này |
| DB seed cô lập ngẫu nhiên | Test importer/verifier | 347 fixture docs gồm validator; tự cleanup theo script |
| `phase07_nosql_fixture_test` | Demo revision và reject | Đã cleanup trong Phase 07 |

Tên DB local hôm nay **CHƯA XÁC MINH lại**; không đọc hoặc công bố toàn bộ `.env`. Counts baseline sẽ thay đổi khi checkout, tạo sản phẩm hoặc đăng ký thêm. Một database chứa dữ liệu phát sinh không còn là fixture thuần, vì vậy `seed:verify` có thể từ chối an toàn.

## 6.2 Các loại document

**Bảng 6.2. Document types trong schema version 2**

| Type | Mục đích | Tổ chức chính |
|---|---|---|
| customer | Tài khoản/hồ sơ khách | `auth`, `profile`, `active`, `legacy_id` |
| staff | Tài khoản nhân viên/quản lý | Các trường account và `role` |
| product | Sản phẩm và tồn theo biến thể | `category`, `variants[]`, `images[]` |
| cart | Giỏ thành viên | `customer_id`, `items[]` |
| order | Giao dịch và quá trình xử lý | Snapshot, trạng thái, histories, tracking tùy chọn |
| voucher | Điều kiện giảm giá/số lượt | `discount_type`, `value`, `remaining_quantity` |
| shipping_method | Phương thức/phí hiện tại | `code`, `name`, `fee`, `active` |
| review | Đánh giá riêng theo sản phẩm | `product_id`, rating/content, moderation |

Hai design documents không phải business types. Delivery không phải type thứ chín; nó nằm trong order. Review được tách riêng để product không tích lũy mọi đánh giá trong một array lớn.

## 6.3 Product Document

```text
product
├── _id / type / schema_version / legacy_id
├── name / description / category
├── variants[] → variant_id, size, price, stock, active
├── images[]   → path, thông tin ảnh
├── active
└── meta
```

Schema mapping mô tả thông tin category theo nguồn danh mục; không áp đặt mọi product phải có category là object, vì source/fixture có thể dùng biểu diễn category được module hiện tại chấp nhận. Biến thể là đơn vị có giá/tồn theo size. Ẩn product thay đổi active, không xóa các snapshot đã nằm trong order. Ảnh upload ở file storage; đường dẫn chỉ là tham chiếu.

## 6.4 Order Document

```text
order
├── customer / guest_order
├── receiver
├── items[]
├── shipping
├── payment / payment_history[] (tùy dữ liệu)
├── discount / totals
├── status / ordered_at
├── status_history[]
├── assigned_staff_id
├── delivery_tracking (tùy chọn)
└── meta (journal/nguồn dữ liệu)
```

`receiver` chứa dữ liệu giao hàng nên không xuất nguyên document có PII để làm ảnh. `customer.customer_id` trong app sử dụng legacy ID; ví dụ demo `P06-001` khác CouchDB ID `customer:P06-001`. Đây là chi tiết quan trọng cho truy vấn lịch sử. Thông tin payment/totals được tách object để UI không trộn trạng thái đã giao và đã trả tiền.

## 6.5 Order Item Snapshot

Mỗi item giữ `product_id`, `variant_id`, `product_name`, `size`, `quantity`, `unit_price`, `line_total`. `line_total = quantity × unit_price`. Tên, size và giá là giá trị lúc mua, không phải một phép lookup luôn lấy catalog mới nhất. Khi manager sửa giá sản phẩm, đơn đã tạo vẫn cần hiển thị giá giao dịch ban đầu.

Ảnh sản phẩm không có trong snapshot. Placeholder “Đơn không lưu ảnh sản phẩm” được giữ; không lấy ảnh catalog hiện tại rồi gọi đó là ảnh lịch sử. Bổ sung image snapshot/identifier là cải tiến P3, chưa làm trong báo cáo.

## 6.6 Delivery Tracking

`delivery_tracking` gồm `tracking_code`, `status`, `estimated_delivery_date`, `delivered_at`, `history[]`. Event lưu `status`, `at`, `note`, `updated_by`; một số event có source. Mã demo `P06-TRK-0020` là synthetic. Với order mới, service tạo mã `DEL-YYYYMMDD-<12 ký tự hex>` và query kiểm tra trùng. Đây là kiểm tra ứng dụng, không phải unique constraint xuyên documents của CouchDB.

ETA mới mặc định được service tính khoảng ba ngày từ thời điểm tạo tracking, hoặc dùng ngày legacy hợp lệ. ETA không phải cam kết của nhà vận chuyển vì chưa tích hợp provider. `assigned_staff_id` vẫn ở cấp order, không nhân đôi trong tracking.

## 6.7 Embedded Document

Embedding đặt phần dữ liệu phụ thuộc cùng vòng đời trong document cha. Items, shipping snapshot và delivery history thường được đọc cùng order; nhúng giúp màn chi tiết lấy một aggregate. Order delivered và delivery delivered có thể lưu nhất quán trong một PUT. Việc này không làm stock của product trở thành một phần của order; tồn hiện hành vẫn nằm trong product document.

## 6.8 Denormalization

Denormalization trong đồ án là lưu lặp thông tin cần giữ cho giao dịch, như tên/giá item và tên/phí shipping. Dữ liệu lặp có chủ đích nên không đồng bộ ngược snapshot mỗi khi catalog đổi. Với trường hiện hành như stock, ứng dụng đọc product thay vì dùng số lượng còn lại tại thời điểm order được tạo.

## 6.9 Snapshot

Snapshot là bản ghi những giá trị được chốt ở thời điểm mua. Tổng tiền cần được giải thích bằng subtotal, shipping fee và discount đã lưu. Công thức checkout hiện tại là `grand_total = max(0, subtotal + shipping_fee - discount_amount)`. Dữ liệu migration lịch sử được đối soát, không tự tạo timestamp transition không có ở nguồn. Snapshot nghiệp vụ khác snapshot backup toàn database.

## 6.10 Ưu và nhược điểm của model

**Bảng 6.3. Đánh đổi thiết kế hiện tại**

| Lựa chọn | Lợi ích trong project | Chi phí/giới hạn |
|---|---|---|
| Order aggregate | Đọc detail thuận tiện; trạng thái liên quan cùng một write | Document/history tăng kích thước |
| Item/shipping snapshot | Giữ lịch sử giá/tên/phí | Lặp dữ liệu; ảnh chưa được giữ |
| Delivery nhúng | Order/delivery delivered đồng bộ | Nhiều người cập nhật cùng order có thể 409 |
| Product/review riêng | Vòng đời riêng; product không chứa mọi review | Quan hệ được service quản lý |
| Schema v2 + validator | Contract có kiểm soát | Không thay FK/transaction nhiều document |

Nguồn: schema mapping, validator, `CheckoutService`, `OrderWorkflowService`, Phase 07. Appendix B chỉ là JSON rút gọn giải thích model, không phải document dùng trực tiếp để import.

# CHƯƠNG 7 – TẠO VÀ NHẬP DỮ LIỆU

## 7.1 Tạo database

Seeder Phase 06 kiểm tra tên đích đúng hậu tố `_phase06_demo_test` và khác database đang cấu hình. Nếu đích đã tồn tại, chế độ tạo mới từ chối. Database demo được tạo bằng HTTP PUT từ PHP. Hồ sơ Phase 06 có kết quả integrity PASS; Phase 07 đã mở database trong Fauxton. Không thực hiện tạo/reset database trong lượt viết báo cáo.

## 7.2 Tạo document

Document schema v2 có `_id`, `type` và các trường nghiệp vụ. Seeder cài validator và tạo tài khoản/sản phẩm/order tổng hợp; workflow service được dùng để dựng các trạng thái giao hàng. Khi demo CRUD bằng GUI cần dùng một product fixture có đủ variant/images/active, không dùng JSON rút gọn trong Phụ lục B. Bằng chứng hiện có cho create/update product qua ứng dụng nằm ở `catalog:admin-smoke`, không phải GUI CRUD.

## 7.3 Import dữ liệu mẫu

`scripts/import_seed.php` import fixture `database/couchdb/seeds/retail_order_delivery.json`, có guard `_test`, kiểm tra document hiện có, từ chối khác biệt và verify sau ghi. `scripts/seed_phase06_demo.php` xây tập demo phục vụ đầy đủ order/delivery scenarios. Hai bộ này có mục đích khác nhau: fixture tái lập dữ liệu ban đầu; demo tạo trạng thái dùng trình bày. Không chạy seed fixture lên demo đã có dữ liệu để cố làm counts khớp.

## 7.4 Số lượng dữ liệu

**Bảng 7.1. Baseline demo đã xác minh trong Phase 06/07**

| Type | Số lượng |
|---|---:|
| customer | 6 |
| staff (gồm staff/manager) | 2 |
| product | 18 |
| voucher | 1 |
| shipping_method | 2 |
| order | 27 |
| Tổng business document | **56** |
| Design document | **2** |
| Tổng document | **58** |

Không có cart/review trong baseline demo này; checkout/đánh giá có thể tạo thêm khi thao tác. Database app được Phase 07 quan sát có 348 business docs: cart 4, customer 4, order 13, product 48, review 270, shipping_method 2, staff 2, voucher 5. Không sử dụng dữ liệu tài khoản/receiver từ database đó cho hình báo cáo.

## 7.5 Các trạng thái order trong dataset

**Bảng 7.2. Phân bố 27 order baseline**

| Trạng thái | pending | confirmed | packing | shipping | delivered | cancelled |
|---|---:|---:|---:|---:|---:|---:|
| Số order | 5 | 4 | 4 | 6 | 6 | 2 |

Tập này đáp ứng query trạng thái, query theo khách/ngày và đọc terminal order. Sau demo checkout sẽ có thêm pending order; kết quả mới phải ghi theo thời điểm mới, không ép khớp bảng baseline.

## 7.6 Các tình huống delivery

**Bảng 7.3. Trạng thái delivery trong 6 order shipping**

| Trạng thái | created | picked_up | in_transit | out_for_delivery | failed_delivery |
|---|---:|---:|---:|---:|---:|
| Số order | 1 | 1 | 2 | 1 | 1 |

Có 4 tracked delivered orders và 2 legacy delivered orders không tracking. Có hai failed events trong lịch sử, trong đó một order đã retry. Query theo trạng thái hiện hành `failed_delivery` trả 1; đó không phải tổng số event thất bại lịch sử. Bộ baseline chưa có guest order nên query guest trả 0; HTTP tests đã chứng minh luồng guest bằng fixture khác.

## 7.7 Kiểm tra dữ liệu trong Fauxton

Chọn database demo và All Documents, mở product để xem variants/images, mở `order:P06-0020` để xem items/payment/shipping và delivery. Với order mẫu, Phase 07 ghi order shipping, COD unpaid, delivery in_transit và history phù hợp. Dùng query projection khi chỉ cần status/totals để tránh mở receiver.

[CHÈN HÌNH 7.1: Product document tổng hợp với variants và images TẠI ĐÂY]

[CHÈN HÌNH 7.2: Order document tổng hợp với item/shipping/payment snapshot TẠI ĐÂY]

[CHÈN HÌNH 7.3: delivery_tracking của order:P06-0020 TẠI ĐÂY]

[CHÈN HÌNH 7.4: status_history của order tổng hợp TẠI ĐÂY]

# CHƯƠNG 8 – IMPORT / EXPORT / BACKUP / RESTORE

## 8.1 Export database

Project dùng `scripts/database_backup.php` để xuất dữ liệu đang có trong CouchDB. Action `backup` đọc `_all_docs` với `include_docs=true`, `attachments=true`, mỗi trang tối đa 500 rows. File JSON có `format_version: 1`, database nguồn, thời điểm UTC, document_count và documents. Script không ghi đè tệp backup đã tồn tại; filename không cho đường dẫn tùy ý.

Việc xuất fixture từ SQL khác việc backup CouchDB. `export_sql_server_snapshot.ps1` và SQL migration exporter đọc snapshot nguồn; transformer chuyển sang schema v2 và importer ghi vào database test. Không gọi seed mẫu là export của database đang chạy.

## 8.2 Import database

`import_seed.php` dùng dữ liệu fixture; `import_sql_snapshot.php` dùng artifact migration đã review. Cả hai có guard, kiểm tra dữ liệu đã có và kết quả từng document, hỗ trợ resume khi phù hợp. `_bulk_docs` không phải transaction toàn bộ batch; response HTTP thành công cần được kiểm tra từng phần tử. Phase 05/06 ghi import/verify 347 fixture documents trên database cô lập PASS.

## 8.3 Backup

Lệnh sau đã chạy thành công ngày 10/10/2026 trên demo DB. Chọn database qua environment của riêng lệnh và tạm ngừng thao tác ghi trước khi export:

```powershell
docker compose exec -T -e COUCHDB_DATABASE=shopquan_ao_phase06_demo_test app composer db:backup -- nosql-demo-20261010.json
```

File `var/backups/nosql-demo-20261010.json` có 84.910 byte và 60 document; JSON parse và count đối chiếu CouchDB đều PASS theo [Backup Verification](../BACKUP_VERIFICATION.md). Backup chứa cả thông tin account/hash nên không commit, không đưa nguyên file vào phụ lục. Phân trang không bảo đảm snapshot nguyên tử nếu có ghi đồng thời. Script lấy các document hiện hữu qua `_all_docs`; đây là logical export, không chứng minh giữ đầy đủ revision tree, deleted history, cấu hình server hay security object. File ảnh upload ở volume riêng cần một quy trình sao lưu riêng.

[CHÈN HÌNH 8.1: Kết quả backup thực tế và document_count, không mở credential/hash TẠI ĐÂY]

## 8.4 Restore

Action restore yêu cầu backup hợp lệ, count khớp, không có ID trùng và target `_test` chưa tồn tại. Script bỏ metadata revision cũ rồi gửi `_bulk_docs` theo batch 40; kiểm tra ID, lỗi và revision của từng kết quả cùng doc_count cuối. Vì CouchDB tạo revision mới, đây là phục hồi nội dung vào DB mới, không giữ nguyên revision lịch sử.

```powershell
docker compose exec -T app composer db:restore -- nosql-demo-20261010.json nosql_report_restore_check_test
```

Lệnh không ghi đè source DB. Nếu đích tồn tại, script từ chối; khi restore lỗi giữa chừng, đích có thể chứa một phần dữ liệu và cần được kiểm tra riêng, không báo thành công chỉ vì database xuất hiện.

Trong backup này, một số order đã có `delivery_tracking` với lịch sử nhiều bước; validator yêu cầu tracking của document mới bắt đầu ở `created`. Vì vậy restore nhập index, business documents rồi mới cài validator từ backup, trong các batch riêng. Nội dung được đối chiếu toàn bộ theo `_id`; validator sau đó từ chối order có status sai trên database đích với HTTP 403. Không khẳng định validator đã chạy khi nhập từng business document.

```text
CouchDB demo → backup JSON → tệp được bảo vệ
                          → restore vào DB test mới
                          → đối chiếu count/nội dung/design docs/query
                          → kiểm tra validator và khả năng đọc qua ứng dụng
```

[CHÈN HÌNH 8.2: Restore thực tế vào DB mới và đối chiếu dữ liệu TẠI ĐÂY]

**Bảng 8.1. Tình trạng bằng chứng import và phục hồi**

| Nội dung | Source có | Bằng chứng thực nghiệm |
|---|---|---|
| Import fixture | Có | PASS theo Phase 05/06, database cô lập |
| Seed demo + verify | Có | PASS theo Phase 06/07 |
| Export/backup CouchDB JSON | Có | PASS ngày 10/10/2026: 60 document, JSON/count khớp |
| Restore vào DB mới | Có | PASS ngày 10/10/2026: 60/60, data parity, Mango/index/validator |
| Backup upload files | Volume có | CHƯA XÁC MINH quy trình backup/restore file |
| Backup production nhất quán | Không thuộc script test hiện tại | CHƯA TRIỂN KHAI |

Không dùng `seed:verify` để verify một bản restore demo, vì seed verifier yêu cầu đúng bộ fixture khác. Lệnh `db:restore-verify` đã so nội dung bỏ metadata revision, chạy Mango query/_explain, đối chiếu index và thử validator. Kết quả chi tiết nằm trong [Restore Verification](../RESTORE_VERIFICATION.md). Tiêu chí 6 đạt trên database demo local; ảnh minh họa vẫn cần chụp và chèn vào bản Word.

# CHƯƠNG 9 – TRUY VẤN DỮ LIỆU BẰNG MANGO QUERY

Các JSON dưới đây được lấy nguyên nội dung từ database/couchdb/demo_queries, không đổi selector để làm kết quả đẹp hơn. Chạy trong Mango Query của database demo; API tương ứng là POST /shopquan_ao_phase06_demo_test/_find. Phase 07 ghi 12/12 HTTP 200. Ba query 02, 11, 12 có bằng chứng chạy trực tiếp trên GUI; các query còn lại đã kiểm chứng qua API, chưa có biên bản UI riêng.

Query 01–04 là nhóm lọc cơ bản. Query 05–12 minh họa trường lồng, khoảng ngày, nhiều điều kiện và tracking. Chúng là truy vấn phức hợp trong phạm vi đồ án, không phải aggregation/analytics lớn. fields là projection, limit là giới hạn từng request; với dữ liệu lớn cần bookmark để đọc tiếp.

## 9.1 Tìm đơn pending

**Yêu cầu:** Tìm đơn pending trong database minh họa.

Nguồn request: `database/couchdb/demo_queries/01_pending_orders.json`.

```json
{
  "selector": {
    "type": {"$eq": "order"},
    "status": {"$eq": "pending"}
  },
  "use_index": ["_design/catalog_indexes", "admin_orders"],
  "sort": [{"ordered_at": "desc"}],
  "fields": ["_id", "legacy_id", "status", "ordered_at", "totals", "payment"],
  "limit": 10,
  "execution_stats": true
}
```

**Giải thích:** Lọc type order và status pending; admin_orders hỗ trợ sort ngày giảm dần. Dùng để xem các đơn chờ xác nhận.

**Kết quả Phase 07:** HTTP 200, 5 document(s). Đã kiểm chứng bằng API; lượt chạy riêng trên GUI CHƯA XÁC MINH. Không coi số này là kết quả vừa chạy ngày 09/10/2026.

## 9.2 Tìm đơn shipping

**Yêu cầu:** Tìm đơn shipping trong database minh họa.

Nguồn request: `database/couchdb/demo_queries/02_shipping_orders.json`.

```json
{
  "selector": {
    "type": {"$eq": "order"},
    "status": {"$eq": "shipping"}
  },
  "use_index": ["_design/catalog_indexes", "admin_orders"],
  "sort": [{"ordered_at": "desc"}],
  "fields": ["_id", "status", "ordered_at", "items", "shipping", "payment", "delivery_tracking", "totals"],
  "limit": 20,
  "execution_stats": true
}
```

**Giải thích:** Lọc đơn đang giao; projection có items, shipping, payment và tracking để đối chiếu thông tin giao hàng.

**Kết quả Phase 07:** HTTP 200, 6 document(s). Đã kiểm chứng trên Fauxton và API. Không coi số này là kết quả vừa chạy ngày 09/10/2026.

## 9.3 Tìm đơn delivered

**Yêu cầu:** Tìm đơn delivered trong database minh họa.

Nguồn request: `database/couchdb/demo_queries/03_delivered_orders.json`.

```json
{
  "selector": {
    "type": {"$eq": "order"},
    "status": {"$eq": "delivered"}
  },
  "use_index": ["_design/catalog_indexes", "admin_orders"],
  "sort": [{"ordered_at": "desc"}],
  "fields": ["_id", "status", "ordered_at", "payment", "delivery_tracking", "status_history"],
  "limit": 20,
  "execution_stats": true
}
```

**Giải thích:** Lọc order delivered; có thể gồm order có tracking và legacy chưa tracking. Không suy ra mọi đơn đã paid.

**Kết quả Phase 07:** HTTP 200, 6 document(s). Đã kiểm chứng bằng API; lượt chạy riêng trên GUI CHƯA XÁC MINH. Không coi số này là kết quả vừa chạy ngày 09/10/2026.

## 9.4 Tìm đơn cancelled

**Yêu cầu:** Tìm đơn cancelled trong database minh họa.

Nguồn request: `database/couchdb/demo_queries/04_cancelled_orders.json`.

```json
{
  "selector": {
    "type": {"$eq": "order"},
    "status": {"$eq": "cancelled"}
  },
  "use_index": ["_design/catalog_indexes", "admin_orders"],
  "sort": [{"ordered_at": "desc"}],
  "fields": ["_id", "status", "ordered_at", "status_history", "totals"],
  "limit": 20,
  "execution_stats": true
}
```

**Giải thích:** Lọc order cancelled và giữ history/totals để giải thích quá trình hủy. Không xóa order khi hủy.

**Kết quả Phase 07:** HTTP 200, 2 document(s). Đã kiểm chứng bằng API; lượt chạy riêng trên GUI CHƯA XÁC MINH. Không coi số này là kết quả vừa chạy ngày 09/10/2026.

## 9.5 Tìm order của customer

**Yêu cầu:** Tìm order của customer trong database minh họa.

Nguồn request: `database/couchdb/demo_queries/05_customer_orders.json`.

```json
{
  "selector": {
    "type": {"$eq": "order"},
    "customer.customer_id": {"$eq": "P06-001"}
  },
  "use_index": ["_design/catalog_indexes", "customer_orders"],
  "sort": [{"ordered_at": "desc"}],
  "fields": ["_id", "status", "ordered_at", "totals", "payment"],
  "limit": 20,
  "execution_stats": true
}
```

**Giải thích:** Đường dẫn lồng customer.customer_id dùng legacy ID P06-001. Không thay bằng customer:P06-001. Query dùng customer_orders.

**Kết quả Phase 07:** HTTP 200, 5 document(s). Đã kiểm chứng bằng API; lượt chạy riêng trên GUI CHƯA XÁC MINH. Không coi số này là kết quả vừa chạy ngày 09/10/2026.

## 9.6 Tìm order theo khoảng thời gian

**Yêu cầu:** Tìm order theo khoảng thời gian trong database minh họa.

Nguồn request: `database/couchdb/demo_queries/06_orders_by_date.json`.

```json
{
  "selector": {
    "type": {"$eq": "order"},
    "ordered_at": {
      "$gte": "2026-09-01T00:00:00Z",
      "$lt": "2026-11-01T00:00:00Z"
    }
  },
  "use_index": ["_design/catalog_indexes", "admin_orders_by_date"],
  "sort": [{"ordered_at": "asc"}],
  "fields": ["_id", "status", "ordered_at", "totals"],
  "limit": 50,
  "execution_stats": true
}
```

**Giải thích:** Khoảng ngày nửa mở: từ 01/09/2026, trước 01/11/2026. Sort tăng dần bằng admin_orders_by_date. Bounds phù hợp lần seed trong Phase 07, cần kiểm tra lại nếu seed ngày khác.

**Kết quả Phase 07:** HTTP 200, 27 document(s). Đã kiểm chứng bằng API; lượt chạy riêng trên GUI CHƯA XÁC MINH. Không coi số này là kết quả vừa chạy ngày 09/10/2026.

## 9.7 COD pending chưa thanh toán

**Yêu cầu:** COD pending chưa thanh toán trong database minh họa.

Nguồn request: `database/couchdb/demo_queries/07_unpaid_cod.json`.

```json
{
  "selector": {
    "type": {"$eq": "order"},
    "status": {"$eq": "pending"},
    "payment.method": {"$eq": "cod"},
    "payment.paid": {"$eq": false}
  },
  "use_index": ["_design/catalog_indexes", "admin_orders"],
  "sort": [{"ordered_at": "desc"}],
  "fields": ["_id", "status", "ordered_at", "payment", "totals"],
  "limit": 20,
  "execution_stats": true
}
```

**Giải thích:** Selector thực tế có status pending, payment.method cod và payment.paid false. Đây là COD chưa trả trong nhóm pending, không phải tất cả COD chưa trả ở mọi trạng thái.

**Kết quả Phase 07:** HTTP 200, 5 document(s). Đã kiểm chứng bằng API; lượt chạy riêng trên GUI CHƯA XÁC MINH. Không coi số này là kết quả vừa chạy ngày 09/10/2026.

## 9.8 Guest order

**Yêu cầu:** Guest order trong database minh họa.

Nguồn request: `database/couchdb/demo_queries/08_guest_orders.json`.

```json
{
  "selector": {
    "type": {"$eq": "order"},
    "guest_order": {"$eq": true}
  },
  "fields": ["_id", "status", "ordered_at", "guest_order", "delivery_tracking", "totals"],
  "limit": 20,
  "execution_stats": true
}
```

**Giải thích:** guest_order true phân biệt đơn khách vãng lai. Baseline không có loại đơn này nên kết quả rỗng hợp lệ; không chứng minh có một guest document mẫu để trình bày.

**Kết quả Phase 07:** HTTP 200, 0 document(s). Đã kiểm chứng bằng API; lượt chạy riêng trên GUI CHƯA XÁC MINH. Không coi số này là kết quả vừa chạy ngày 09/10/2026.

## 9.9 Delivery in_transit

**Yêu cầu:** Delivery in_transit trong database minh họa.

Nguồn request: `database/couchdb/demo_queries/09_delivery_in_transit.json`.

```json
{
  "selector": {
    "type": {"$eq": "order"},
    "delivery_tracking.status": {"$eq": "in_transit"}
  },
  "fields": ["_id", "status", "ordered_at", "delivery_tracking.tracking_code", "delivery_tracking.status", "delivery_tracking.history"],
  "limit": 20,
  "execution_stats": true
}
```

**Giải thích:** Lọc trường lồng delivery_tracking.status. Không ghim index dedicated vì project chưa có index delivery status.

**Kết quả Phase 07:** HTTP 200, 2 document(s). Đã kiểm chứng bằng API; lượt chạy riêng trên GUI CHƯA XÁC MINH. Không coi số này là kết quả vừa chạy ngày 09/10/2026.

## 9.10 Delivery out_for_delivery

**Yêu cầu:** Delivery out_for_delivery trong database minh họa.

Nguồn request: `database/couchdb/demo_queries/10_delivery_out_for_delivery.json`.

```json
{
  "selector": {
    "type": {"$eq": "order"},
    "delivery_tracking.status": {"$eq": "out_for_delivery"}
  },
  "fields": ["_id", "status", "ordered_at", "delivery_tracking.tracking_code", "delivery_tracking.status", "delivery_tracking.history"],
  "limit": 20,
  "execution_stats": true
}
```

**Giải thích:** Lọc last-mile đang giao tới khách. Array history được trả về để đối chiếu event cuối với trạng thái hiện hành.

**Kết quả Phase 07:** HTTP 200, 1 document(s). Đã kiểm chứng bằng API; lượt chạy riêng trên GUI CHƯA XÁC MINH. Không coi số này là kết quả vừa chạy ngày 09/10/2026.

## 9.11 Delivery failed_delivery

**Yêu cầu:** Delivery failed_delivery trong database minh họa.

Nguồn request: `database/couchdb/demo_queries/11_delivery_failed.json`.

```json
{
  "selector": {
    "type": {"$eq": "order"},
    "delivery_tracking.status": {"$eq": "failed_delivery"}
  },
  "fields": ["_id", "status", "ordered_at", "delivery_tracking.tracking_code", "delivery_tracking.status", "delivery_tracking.history"],
  "limit": 20,
  "execution_stats": true
}
```

**Giải thích:** Lọc trạng thái hiện hành failed_delivery. Không đếm tất cả failed events lịch sử: đơn đã retry có thể hiện in_transit.

**Kết quả Phase 07:** HTTP 200, 1 document(s). Đã kiểm chứng trên Fauxton và API. Không coi số này là kết quả vừa chạy ngày 09/10/2026.

## 9.12 Tìm order theo tracking code

**Yêu cầu:** Tìm order theo tracking code trong database minh họa.

Nguồn request: `database/couchdb/demo_queries/12_tracking_code.json`.

```json
{
  "selector": {
    "type": {"$eq": "order"},
    "delivery_tracking.tracking_code": {"$eq": "P06-TRK-0020"}
  },
  "use_index": ["_design/catalog_indexes", "delivery_tracking_code"],
  "fields": ["_id", "status", "ordered_at", "items", "payment", "delivery_tracking"],
  "limit": 5,
  "execution_stats": true
}
```

**Giải thích:** Tra mã synthetic P06-TRK-0020 qua index delivery_tracking_code. Có thể mở order trả về để liên hệ customer detail.

**Kết quả Phase 07:** HTTP 200, 1 document(s). Đã kiểm chứng trên Fauxton và API. Không coi số này là kết quả vừa chạy ngày 09/10/2026.

[CHÈN HÌNH 9.1: Mango Query shipping và 6 kết quả theo baseline TẠI ĐÂY]

[CHÈN HÌNH 9.2: Query failed_delivery và cảnh báo fallback index TẠI ĐÂY]

[CHÈN HÌNH 9.3: Query tracking code và order trả về TẠI ĐÂY]

Nguồn: Phase 07 và 12 file JSON; lý thuyết selector/projection/index tham khảo [Mango API chính thức](https://docs.couchdb.org/en/stable/api/database/find.html). Mọi thay đổi dữ liệu sau baseline cần ghi lại thời điểm và kết quả mới. Đừng reset database chỉ để số trên hình khớp báo cáo.

# CHƯƠNG 10 – MANGO INDEX VÀ TỐI ƯU TRUY VẤN

## 10.1 Vì sao cần index

Khi selector không có đường truy cập phù hợp, database có thể phải xét nhiều documents để tìm một nhóm nhỏ kết quả. Trong đồ án, query failed delivery trả một order nhưng đã xét 58 documents; query tracking xét một document. Index không thay đổi ý nghĩa kết quả, nhưng ảnh hưởng lượng công việc để tìm kết quả. Chỉ số examined hữu ích hơn việc nhìn một thời gian đơn lẻ trên dataset nhỏ.

## 10.2 Index hiện có

**Bảng 10.1. Chín JSON Mango indexes theo source**

| Index | Fields theo thứ tự | Partial selector | Mục đích |
|---|---|---|---|
| active_products | type, active | product và active=true | Catalog public |
| admin_products | type | product | Catalog quản trị |
| active_reviews | type, active | review và active=true | Review public |
| admin_reviews | type | review | Review quản trị |
| account_usernames | type, auth.username | customer hoặc staff | Lookup đăng nhập |
| customer_orders | type, customer.customer_id, ordered_at | order | Lịch sử theo chủ đơn |
| admin_orders | type, status, ordered_at | order | Filter order status |
| admin_orders_by_date | type, ordered_at | order | Danh sách/ngày không cố định status |
| delivery_tracking_code | type, delivery_tracking.tracking_code | order | Tra tracking code |

Primary `_all_docs` có sẵn và không nằm trong số chín JSON index. Source là `database/couchdb/indexes/catalog_indexes.json`, script áp dụng là `scripts/create_catalog_indexes.php`. Partial selector giới hạn loại dữ liệu được đưa vào index; query cần phù hợp điều kiện đó. Việc thiết kế index phải xuất phát từ selector/sort của workload, không chỉ thêm index cho mọi field.

[CHÈN HÌNH 10.1: Manage Indexes với chín JSON index và primary TẠI ĐÂY]

## 10.3 Index theo trạng thái order

`admin_orders` có tiền tố type/status và trường ordered_at. Query pending/shipping/delivered/cancelled cố định hai điều kiện đầu và sắp theo ngày. Khi admin không chọn status, repository có đường truy cập theo ngày phù hợp, không xem index theo status là lựa chọn duy nhất cho mọi danh sách.

## 10.4 Customer Order index

`customer_orders` phục vụ selector type order và legacy customer ID, rồi sort ngày. Repository luôn gắn customer ID từ phiên/token; tham số trên browser không được chọn tùy ý một customer khác. Index là công cụ tìm dữ liệu, không phải cơ chế phân quyền; controller/service vẫn kiểm tra ownership khi mở detail.

## 10.5 Tracking code index

`delivery_tracking_code` hỗ trợ tra đúng mã và kiểm tra mã có ở order khác. Mango index này không khai báo unique constraint. Service sinh mã ngẫu nhiên và query kiểm tra trước khi dùng; không khẳng định đây là bảo đảm uniqueness nguyên tử xuyên nhiều document trong mọi trường hợp cạnh tranh.

## 10.6 `_explain` và execution statistics

Request `_explain` giúp kiểm tra index mà query planner chọn. Khi so sánh với `_find`, giữ selector/use_index/sort tương ứng; không sửa selector giữa hai request rồi coi đó là cùng phép đo. [Mango `_explain` và `_find`](https://docs.couchdb.org/en/stable/api/database/find.html)

**Bảng 10.2. Kết quả `_explain` đã ghi trong Phase 07**

| Query | Index được chọn | Kết luận giới hạn |
|---|---|---|
| 01 pending | catalog_indexes / admin_orders | Khớp index yêu cầu |
| 05 customer | catalog_indexes / customer_orders | Khớp trường owner/ngày |
| 12 tracking | catalog_indexes / delivery_tracking_code | Khớp tra mã |

**Bảng 10.3. Statistics trực tiếp trong Fauxton – Phase 07**

| Query | Results | Docs examined | Thời gian được ghi | Diễn giải |
|---|---:|---:|---:|---|
| Shipping | 6 | 6 | Khoảng 2 ms | Đường index phù hợp |
| Failed delivery | 1 | 58 | Khoảng 3 ms | Fallback; UI cảnh báo thiếu index phù hợp |
| Tracking code | 1 | 1 | Khoảng 1 ms | Tra cứu chọn lọc |

Các con số chỉ phản ánh một lượt trên bộ 58 documents. Không có benchmark tải lớn, kiểm soát cache, số lần lặp hoặc đối chứng với MongoDB. Chưa có index dedicated cho delivery status/guest; việc bổ sung là hướng phát triển khi workload yêu cầu, không được làm chỉ để cải thiện hình báo cáo.

[CHÈN HÌNH 10.2: Kết quả _explain thực tế và index được chọn TẠI ĐÂY]

# CHƯƠNG 11 – _ID, _REV VÀ CONFLICT

## 11.1 `_id`

ID được dùng trong URL document và để xác định đối tượng. Project dùng prefix product/order/customer/staff/cart; checkout order dùng `order:checkout:<hash>` để liên hệ một thao tác idempotent. Customer có cả CouchDB ID và legacy ID cho nghiệp vụ. Hai trường có liên hệ nhưng không hoán đổi trong query nếu source đang dùng legacy ID.

## 11.2 `_rev`

Revision có dạng generation–digest, ví dụ minh họa `1-<digest>` và `2-<digest>`. CouchDB cấp revision; application gửi bản hiện hành khi PUT. Revision không phải thời gian tạo, thứ tự trạng thái hay mã schema. Có thể có nhiều revision do journal/stock-related workflow trước khi đơn hiển thị hoàn tất; không suy ra generation 3 nghĩa là đúng ba bước nghiệp vụ.

## 11.3 Cập nhật document

```text
GET product fixture → nhận rev 1
PUT nội dung mới + rev 1 → nhận rev 2
GET lại → kiểm tra nội dung và rev 2
```

Script Phase 07 thực hiện đúng trình tự này với product fixture. Trong Fauxton, trước khi lưu một chỉnh sửa cần đọc document mới nhất; bỏ `_rev` không phải cách vượt conflict. Không sửa field revision bằng tay thành một digest tùy ý.

[CHÈN HÌNH 11.1: Revision trước và sau cập nhật trên dữ liệu cô lập TẠI ĐÂY]

## 11.4 Conflict 409

`phase07_nosql_fixture_demo.php` tạo DB riêng chưa tồn tại, lưu product rev 1, cập nhật hợp lệ lên rev 2, rồi gửi stale PUT với rev 1. Phase 07 ghi **HTTP 409** và xác nhận nội dung rev 2 còn nguyên. Đây là conflict do stale write trên một node; không phải thực nghiệm divergent revisions do replication. [CouchDB conflict model](https://docs.couchdb.org/en/stable/replication/conflicts.html)

Hình minh chứng cần là output thực tế hoặc response HTTP thật. Không gõ lại dòng PASS trong console để giả làm test. Script có cleanup database fixture với guard; không chạy script trong lượt biên soạn này và không thêm câu lệnh DELETE thủ công vào demo chính.

[CHÈN HÌNH 11.2: Output fixture hoặc HTTP response 409 thật TẠI ĐÂY]

## 11.5 Cách PHP xử lý conflict

**Bảng 11.1. Các cách xử lý theo module**

| Vị trí | Phản ứng khi 409 |
|---|---|
| CouchDbClient | Trả status/body; không tự merge |
| AccountRepository tạo account | Ánh xạ sang lỗi username đã đăng ký |
| AccountRepository cập nhật | Yêu cầu tải lại/thử lại theo lỗi account |
| ProductAdminService | Báo sản phẩm vừa thay đổi; yêu cầu tải lại |
| OrderWorkflowService | Đọc lại document và áp dụng logic tối đa 8 lượt |
| CheckoutService | Retry/reservation/journal theo từng bước và giới hạn |

Đoạn rút từ `OrderWorkflowService::updateOrder()` minh họa ý tưởng:

```php
for ($attempt = 0; $attempt < 8; ++$attempt) {
    $order = $this->documents->get($orderId);
    if ($order === null) throw new OrderWorkflowException('Đơn hàng không còn tồn tại.');
    $updated = $change($order);
    if ($updated === $order) return $order;
    $response = $this->documents->put($updated);
    if ($response->statusCode === 409) continue;
    $this->assertWrite($response, 'cập nhật đơn hàng');
    return $updated;
}
```

Callback được chạy lại trên dữ liệu mới nên kiểm tra transition phải ở trong callback, không chỉ ở lần đọc đầu. Đây là retry có giới hạn, không phải tự động hòa trộn mọi thay đổi của hai người dùng. Sau ghi, caller đọc lại để lấy document mới; snippet không phải toàn bộ implementation.

# CHƯƠNG 12 – VALIDATION VÀ DESIGN DOCUMENT

## 12.1 Design Document

`_design/domain_validation` khai báo `language: javascript` và `validate_doc_update`. `_design/catalog_indexes` dùng `language: query` chứa index definitions. Chúng là document đặc biệt có revision như dữ liệu khác, nhưng không phải các type nghiệp vụ trong bảng 6.2. Phải có quyền quản trị phù hợp để cập nhật design document; customer không sửa chúng qua ứng dụng.

[CHÈN HÌNH 12.1: Design document domain_validation trong Fauxton TẠI ĐÂY]

## 12.2 Validator

Validator source nhận `newDoc`, `oldDoc`, `userCtx`, `secObj`, kiểm tra type/schema/cấu trúc và throw forbidden khi sai. Các trường hợp `_deleted` và design document được return sớm trong source. Điều đó có nghĩa ràng buộc history đang mô tả dành cho business updates thông thường, không phải bảo đảm chống mọi thao tác quản trị/xóa document.

Tám types được chấp nhận; schema version bắt buộc 2. Product có variants/images/active; giá/tồn không âm; cart item có quantity dương; review có rating nguyên 1–5. Validator chạy ở database khi ghi, bổ sung cho PHP validation. [Validate document update](https://docs.couchdb.org/en/stable/ddocs/ddocs.html)

## 12.3 Validation Order status

Validator chặn status ngoài `pending`, `confirmed`, `packing`, `shipping`, `delivered`, `cancelled`; kiểm tra receiver, items và totals không âm. Đoạn trích ngắn:

```javascript
var statuses = ["pending", "confirmed", "packing", "shipping", "delivered", "cancelled"];
// Source còn kiểm tra receiver và items trong cùng điều kiện.
if (statuses.indexOf(newDoc.status) === -1) {
  throw({forbidden: "order status is invalid"});
}
```

Đây là **minh họa rút gọn**, thông báo trên không phải nguyên văn đầy đủ của source. Rule chuyển order pending→confirmed→packing thuộc PHP. Validator chỉ kiểm tra `status_history` là array, chưa xác minh append-only toàn bộ lịch sử order tại database layer. Không trình bày validator như cơ chế bảo đảm mọi workflow Order.

## 12.4 Validation Delivery status

Tracking có enum riêng; field code/date/history được kiểm tra. Event có thời gian parse được, note và actor; timestamp không giảm, status hiện hành khớp event cuối. Khi delivered, `delivered_at` khớp event cuối và order cũng delivered. Tracking mới phải bắt đầu created khi order shipping; tracking đã có không được bỏ hẳn trong update bình thường.

Transitions delivery được validator kiểm tra khi trạng thái thay đổi. Có ngoại lệ completion từ customer/guest receipt theo `source` event, nhưng source marker không tự xác thực một customer ở database. Ownership, quyền endpoint và CSRF vẫn do PHP; không coi các field người dùng có thể gửi qua công cụ quản trị là bằng chứng phân quyền.

## 12.5 History append-only: phạm vi thật

Delivery history giữ prefix cũ: mảng mới không ngắn hơn và mỗi event cũ phải giống trước. `payment_history` có kiểm tra tương tự khi field có trong newDoc; paid không được đổi lại unpaid. **Giới hạn:** khối kiểm tra payment history nằm dưới điều kiện field tồn tại ở newDoc; source chưa chặn riêng việc bỏ hẳn field này. Vì vậy không tuyên bố payment audit bất biến tuyệt đối. Không sửa validator trong công việc báo cáo.

**Bảng 12.1. Ranh giới validator và service**

| Ràng buộc | Validator source | PHP/service |
|---|---|---|
| Type/schema/basic shape | Có | Kiểm tra input/domain bổ sung |
| Enum order | Có | Có |
| Chuỗi transition order | Chưa kiểm tra toàn bộ | OrderWorkflowService |
| Delivery transition/history | Có trong update bình thường | Service kiểm tra quyền và workflow |
| Payment history | Kiểm tra khi field còn hiện diện | Service nối event/idempotency |
| Owner/CSRF/JWT | Không thay application guard | Controller/auth |
| Unique tracking xuyên docs | Không | Sinh mã và query kiểm tra |
| Tổng tiền/FK/stock nhiều docs | Chỉ shape/range một phần | Pricing/checkout/journal |

## 12.6 Demo dữ liệu sai bị từ chối

Phase 07 fixture gửi order với `status: invalid_status` và nhận HTTP 403. Fixture này cũng có các trường chưa đầy đủ để trở thành một order hợp lệ, nên bằng chứng chứng minh **document sai bị reject**; không đủ để tách riêng từng nhánh validator bằng một ca negative duy nhất. Source trực tiếp chứng minh rule enum. Không coi HTTP 403 này là khách không có quyền admin: nguyên nhân ở đây là validator forbidden.

[CHÈN HÌNH 12.2: Response hoặc output reject HTTP 403 trên fixture thật TẠI ĐÂY]

Chương này nêu đúng những rule có trong source và giới hạn kiểm thử. Không kiểm tra negative case mới trên database ứng dụng đang dùng để tạo bằng chứng cho báo cáo.

# CHƯƠNG 13 – ỨNG DỤNG PHP KẾT NỐI COUCHDB

## 13.1 Kiến trúc

```text
Browser / JSON API
       ↓ Router → Controller → Service
                              ↓ Repository → CouchDbClient → HTTP → CouchDB
Fauxton ───────────────────────────────────────────────────────────→ CouchDB
```

Object wiring nằm trong `public/index.php`. Một số module dùng repository riêng; checkout/order dùng chung `CheckoutRepository`. Guest cart nằm trong session nên không đi qua CouchDB cho mọi thao tác. Fauxton và PHP là hai client của database, không phải hai database độc lập.

[CHÈN HÌNH 13.1: Sơ đồ kiến trúc PHP–CouchDB và vai trò Fauxton TẠI ĐÂY]

## 13.2 PHP kết nối CouchDB

`CouchDbClient` dùng PHP cURL, HTTP Basic cho kết nối backend, JSON encode/decode và timeout. Client tắt follow redirect, bật kiểm tra SSL khi dùng HTTPS, đọc endpoint/credential từ môi trường. App trong Docker gọi service name `couchdb`; browser local gọi port host. Không đưa database password vào JavaScript.

Đoạn sử dụng minh họa, dựa trên API client thực tế; chỉ là đọc một order tổng hợp:

```php
$client = new CouchDbClient(
    (string) getenv('COUCHDB_URL'),
    (string) getenv('COUCHDB_USER'),
    (string) getenv('COUCHDB_PASSWORD'),
);
$path = rawurlencode($database) . '/' . rawurlencode('order:P06-0020');
$response = $client->request('GET', $path);
if ($response->statusCode === 200) {
    $order = $response->json();
}
```

Đoạn này cần autoload và `$database` của môi trường demo, không hardcode secret. Repository chuyển các response 404 thành not-found phù hợp, còn router dùng thông báo lỗi chung khi dependency không khả dụng.

## 13.3 Authentication

Website dùng session và CSRF; mật khẩu tài khoản ứng dụng được hash/verify bằng PHP. Account dữ liệu migration chưa có hash hợp lệ phải provision trước khi login. API dùng JWT HS256, TTL mặc định 900 giây và kiểm tra account/role theo source. Tài khoản website khác CouchDB credential và khác cơ chế user management của Fauxton.

Chưa có email reset tự động, refresh-token hoặc hệ quản trị account đầy đủ trên web. Tài khoản staff provision qua terminal có guard test, không được mô tả thành màn hình “Thêm nhân viên” chưa tồn tại.

## 13.4 Product

ProductRepository query catalog, ProductAdminService quản lý create/edit/soft hide/restore. Manager có quyền sửa dữ liệu, staff có quyền xem theo guard. File ảnh upload được kiểm tra loại/kích thước, lưu ngoài public root và phục vụ qua route media. Các snapshot order không bị ghi lại khi product thay đổi.

## 13.5 Cart

GuestCartService dùng session; MemberCartRepository lưu cart theo customer. Khi login, các dòng guest được merge theo biến thể và giới hạn theo tồn. Cart giữ lựa chọn dòng, size/quantity; checkout chỉ xử lý phần được chọn và đọc lại dữ liệu hiện hành.

## 13.6 Checkout

CheckoutService cùng PricingService tính lại giá, shipping, voucher và totals. Chỉ COD được bật cho đơn mới. Journal nhận diện thao tác, stock/voucher markers hỗ trợ retry và bù trừ. Không dựa vào `_bulk_docs` như một transaction bảo đảm mọi stock/order cùng thành công. Phần này minh họa trách nhiệm application layer khi database chủ yếu bảo đảm atomicity một document.

## 13.7 Order

OrderController giới hạn history/detail theo chủ đơn; admin dùng OrderAdminController và workflow service. Danh sách dùng cursor/bookmark, mỗi trang 25 orders. Customer truy cập order khác nhận not-found, tránh tiết lộ rằng ID có tồn tại. Người chưa đăng nhập vào admin được redirect 302 tới login; tài khoản đã đăng nhập thiếu quyền nhận 403 theo source.

## 13.8 Delivery Tracking

Admin POST `/admin/orders/{id}/delivery` có CSRF; API `/api/admin/orders/{id}/delivery` dùng JWT staff/manager. Service kiểm tra trạng thái, note, actor và revision trước ghi. Customer được đọc timeline qua order sở hữu; SSE cũng dùng ownership đó. Các module thực thi một workflow chung thay vì API tự viết một luật giao hàng khác.

Nguồn: `public/index.php`, `src/Auth`, `src/Catalog`, `src/Cart`, `src/Checkout`, `src/Orders`, client và `docs/openapi.yaml`; paths cụ thể trong source map. Phần này giữ trọng tâm tích hợp NoSQL, không trình bày toàn bộ website như một báo cáo PHP riêng.

# CHƯƠNG 14 – DEMO ỨNG DỤNG

## 14.1 Tài khoản và quyền người dùng

Demo dùng đăng ký/login customer, login staff/manager và minh họa quyền. Đăng ký tạo customer document với hash; khi login, session chứa danh tính ứng dụng. Staff/manager đã được chuẩn bị trong demo DB. Không chụp password, hash, JWT hoặc credential file. Có thể nói “quản lý tài khoản và phân quyền trong phạm vi đăng ký/đăng nhập/provision”, không nói có màn hình admin CRUD người dùng đầy đủ.

[CHÈN HÌNH 14.1: Trang đăng ký/đăng nhập và thông tin role an toàn TẠI ĐÂY]

## 14.2 CRUD sản phẩm

Chọn module product để chứng minh CRUD. Create qua form manager; Read bằng catalog/detail; Update tên/giá/variant theo rule; Delete trong ứng dụng là **soft hide**, thay active để sản phẩm không còn ở catalog công khai; Restore cho phép hiển thị lại. Order snapshot vẫn giữ giá trị cũ. Khi biểu diễn rubric, cần nói rõ delete mềm khác CouchDB DELETE.

Phase 06 smoke/HTTP report có bằng chứng create/edit/validation/hide/restore. Chưa có ảnh browser cho toàn chuỗi CRUD. Demo chỉ dùng sản phẩm tổng hợp riêng, không xóa sản phẩm đang được dùng để chụp baseline.

[CHÈN HÌNH 14.2: Catalog PHP và product detail tổng hợp TẠI ĐÂY]

[CHÈN HÌNH 14.3: Manager product form và thao tác ẩn/khôi phục TẠI ĐÂY]

## 14.3 Order Management

Customer chọn variant, thêm giỏ và checkout COD. Sau thành công mở History/Detail để đọc items/totals/payment, rồi chuyển Fauxton xem document vừa tạo. Manager mở Admin Orders, filter status và cập nhật các bước hợp lệ. Sau một bước, refresh Fauxton để chỉ revision/status_history mới. Không thực hiện chuyển thẳng pending→delivered bằng editor để minh họa workflow.

[CHÈN HÌNH 14.4: Checkout với COD, shipping và totals TẠI ĐÂY]

[CHÈN HÌNH 14.5: Customer Order History và Detail TẠI ĐÂY]

[CHÈN HÌNH 14.6: Admin Order List có filter/payment/delivery TẠI ĐÂY]

[CHÈN HÌNH 14.7: Admin Order Detail và form transition hợp lệ TẠI ĐÂY]

## 14.4 Delivery Tracking

Order vào shipping thì tracking được tạo. Admin chuyển picked_up→in_transit→out_for_delivery→delivered và quan sát timeline. Với đơn có failed_delivery, dùng retry hợp lệ, không xóa event thất bại. Customer chỉ thấy dữ liệu giao hàng của đơn mình, không thấy assigned staff/internal controls. Order legacy không tracking có thông báo rõ, không bổ sung lịch sử giả.

[CHÈN HÌNH 14.8: Customer Delivery Timeline có failed event và retry TẠI ĐÂY]

[CHÈN HÌNH 14.9: Order legacy không có tracking TẠI ĐÂY]

[CHÈN HÌNH 14.10: Customer/Admin Order Detail ở viewport 390px TẠI ĐÂY]

## 14.5 Các chức năng hỗ trợ

Review có gửi đánh giá, phản hồi staff, ẩn/khôi phục manager. Voucher hỗ trợ percent/cash/shipping và được tính lại tại checkout. Revenue cộng `grand_total` của đơn delivered, hiển thị payment riêng; đây không phải doanh thu tiền đã thu hay báo cáo lợi nhuận. Realtime cập nhật order/detail bằng SSE đã có; Chương 15 trình bày cụ thể.

[CHÈN HÌNH 14.11: Review hoặc Revenue với dữ liệu tổng hợp TẠI ĐÂY]

Kịch bản 8–12 phút nằm trong `NOSQL_DEMO_SCRIPT.md`. Buổi demo chính ưu tiên Order/Delivery và tool; CRUD/supporting modules có bước rút gọn hoặc chứng cứ đã chuẩn bị. Cần xác nhận health/readiness và dataset trước buổi bảo vệ; Phase 08 chưa có public URL.

# CHƯƠNG 15 – REALTIME VÀ _CHANGES

## 15.1 Luồng hiện có

```text
Admin → PHP workflow → CouchDB order update
                         ↓ _changes (_doc_ids)
Customer Browser ← EventSource/SSE ← PHP OrderRealtimeController
```

Phase 04 triển khai và Phase 06 ghi nhận browser cập nhật không F5. Browser không gọi trực tiếp CouchDB. PHP theo dõi đúng order, đọc document mới rồi phát snapshot đã lọc; `_changes` là cơ chế phát hiện dữ liệu đổi, không phải một bảng chứa mọi business event. Lịch sử nghiệp vụ vẫn lấy từ arrays trong order. [Changes feed API](https://docs.couchdb.org/en/stable/api/database/changes.html)

## 15.2 Cơ chế hữu hạn và reconnect

Endpoints customer `/account/orders/{id}/events`, guest `/orders/{id}/events`. Server tạo checkpoint, đọc lại order để giảm khe hở update, rồi longpoll tối đa 8 giây. Response gửi initial snapshot, update hoặc heartbeat và đóng. EventSource nhận retry 5000 ms, gửi Last-Event-ID khi reconnect. Session được đóng trước stream để tránh khóa các request cùng phiên.

## 15.3 Dữ liệu và quyền

Payload gồm status, payment_status, delivery code/status/date/history và order history đã lọc. Assigned staff/auth/raw CouchDB document không được gửi cho customer. DOM cập nhật bằng textContent; server-rendered HTML vẫn có khi JavaScript không hoạt động. Ownership được kiểm tra trước subscribe và khi đọc lại.

## 15.4 Kết quả và giới hạn

Phase 04 browser đã thấy shipping, nhiều delivery steps, failed_delivery, retry và delivered tự cập nhật. Phase 06 xác nhận in_transit không reload. Reload khôi phục snapshot đã xác minh; mất mạng/online reconnect thực tế **CHƯA XÁC MINH**. Mỗi lượt SSE chiếm worker Apache trong thời gian chờ, tải đồng thời lớn chưa đo. Không có WebSocket, Redis hoặc worker realtime riêng; proxy/cloud chưa kiểm thử.

[CHÈN HÌNH 15.1: Customer detail sau cập nhật realtime, ghi chú cách kiểm chứng không reload TẠI ĐÂY]

Một ảnh tĩnh chỉ cho thấy trạng thái hiển thị; cần video ngắn hoặc demo trực tiếp để chứng minh không F5. Không lấy hai ảnh trước/sau làm bằng chứng duy nhất cho cơ chế realtime.

# CHƯƠNG 16 – KIỂM THỬ

## 16.1 Cơ sở tổng hợp

Các kết quả dưới đây là kết quả **được ghi trong Phase reports**, không phải log vừa chạy khi viết báo cáo. Source test cho biết phạm vi assertions; report cho biết lượt chạy và kết quả. Công việc hiện tại chỉ tạo tài liệu, không chạy DB mutation/test cleanup, không sửa code để tạo thêm PASS.

**Bảng 16.1. Kết quả kiểm thử đã có bằng chứng**

| STT | Nội dung | Kết quả | Nguồn/thời điểm và phạm vi |
|---:|---|---|---|
| 1 | PHP syntax | PASS | Phase 06: 89 PHP files; Phase 07 lint fixture script |
| 2 | Composer metadata | PASS có warning | Phase 06; chưa khai báo license |
| 3 | JWT | PASS | auth:jwt-smoke, cấp/verify/tamper/issuer |
| 4 | Catalog admin | PASS | create/edit/validation/hide/restore, role guard |
| 5 | Checkout/service | PASS | checkout:smoke, stock/idempotency/recovery |
| 6 | HTTP integration | PASS | checkout:http-smoke, web/API/guest/customer |
| 7 | Order ownership | PASS | Các smoke; cross-customer/guest và not-found |
| 8 | Order transition/cancellation | PASS | Phase 02/06, terminal/paid cancellation rules |
| 9 | Delivery/failed/retry | PASS | Phase 02/03B/06, transition/history/sync |
| 10 | CSRF | PASS | Smoke; error banner ở HTTP, success trong browser |
| 11 | Payment audit | PASS theo phạm vi tests | COD/manual verification; không gateway/refund |
| 12 | CouchDB diagnostics | PASS | _find/_explain read-only đúng index |
| 13 | Dependency unavailable | PASS | Phase 05/06: generic 503 qua port cô lập |
| 14 | Seed isolated | PASS | 347 fixture docs import/verify và cleanup |
| 15 | Demo integrity | PASS | Phase 06/07: counts/status/totals/tracking/retry |
| 16 | Browser desktop | PASS | Phase 03B/06: customer/admin, khoảng 1280px |
| 17 | Browser mobile 390px | PASS | Phase 03B/06: không page overflow, table có vùng cuộn |
| 18 | Realtime browser/API | PASS có giới hạn | Phase 04/06; network offline/online chưa mô phỏng |
| 19 | 12 Mango requests | PASS | Phase 07: 12/12 HTTP 200, guest=0 |
| 20 | GUI Mango queries | PASS | Phase 07: query 02/11/12 trên Fauxton |
| 21 | Index/explain | PASS | Phase 07: 9 JSON index + primary; 3 explain |
| 22 | Stale revision | PASS | Phase 07: 1→2, stale PUT409, dữ liệu mới giữ nguyên |
| 23 | Validator reject | PASS trong fixture | Phase 07: document sai trả403; chưa tách từng rule |
| 24 | Backup/restore round-trip | Đạt | 10/10/2026: backup 60, restore 60, data parity, Mango/index/validator PASS |
| 25 | Replication/attachments | CHƯA TRIỂN KHAI | Nghiên cứu, không test deployment |
| 26 | Local runtime ngày biên soạn | Đạt ngày 10/10/2026 | Docker app/CouchDB healthy; `/health/ready` trả HTTP 200 |
| 27 | Render/public deployment | CHƯA TRIỂN KHAI | Phase 08 BLOCKED/NOT DEPLOYED |

## 16.2 Những kết quả cần diễn giải đúng

`seed:verify` từ chối DB đang có document ngoài fixture ở Phase 01 là guard, không phải website bị lỗi. Isolated verification ở Phase 05/06 giải quyết nhu cầu kiểm tra seed mà không reset DB app. Dataset counts của các DB khác nhau không được cộng chung.

Browser Phase 03B sửa một bug header quá cao ở 390px; table cuộn trong wrapper vẫn được coi là responsive hợp lệ vì không kéo ngang toàn trang và thông tin còn đọc được. Success message có browser evidence; invalid-CSRF error banner có HTTP assertion, không mô tả cả hai đều được thao tác cùng cách trong browser.

Không có test tải lớn, pentest đầy đủ, backup production hay restart-persistence online. Báo cáo không dùng PASS của smoke local để suy ra các đặc tính đó. Hướng chạy lại tests được ghi Phụ lục D, nhưng không thay kết quả lịch sử bằng một kết quả giả định.

# CHƯƠNG 17 – ĐÁNH GIÁ

## 17.1 Kết quả đạt được

Project đã có ứng dụng PHP kết nối CouchDB và các luồng phù hợp bài toán: mua hàng, order management, delivery tracking, failed/retry và terminal delivered. Database demo tổng hợp có nhiều trạng thái và lịch sử để giải thích mô hình. Fauxton đã được sử dụng để quan sát order/design documents, chạy query và đọc index. Fixture revision/validator bổ sung bằng chứng về stale write và dữ liệu sai bị reject.

Về rubric, nội dung lý thuyết gắn với source, so sánh tool có tài liệu chính thức, query có request thực tế và số kết quả theo thời điểm. Importer và backup/restore trên demo local đã có kiểm thử thực tế, gồm data parity và validator sau phục hồi. Tài liệu đã tổ chức chương/bảng/code và vị trí hình, nhưng chưa phải bản nộp hoàn tất vì ảnh và trang bìa còn thiếu.

## 17.2 Ưu điểm

Order aggregate thuận tiện cho màn detail; snapshot bảo vệ ý nghĩa giao dịch khi danh mục đổi. Tracking nằm cùng order giúp cập nhật đồng bộ delivery/order delivered trong một document. REST/JSON làm đường kết nối từ PHP rõ ràng; repository tập trung các thao tác đọc/ghi. Validator đặt một lớp kiểm tra trước lưu, còn service giữ rule liên document và phân quyền. Fauxton giúp giải thích các lớp này bằng dữ liệu quan sát được.

## 17.3 Hạn chế

**Bảng 17.1. Giới hạn và ảnh hưởng**

| Giới hạn | Ảnh hưởng hiện tại |
|---|---|
| Không lưu ảnh order snapshot | Detail dùng placeholder; không có ảnh lịch sử |
| Không shipper/GPS/provider | Tracking là cập nhật nội bộ staff, không vị trí vận chuyển thật |
| Checkout COD; bank/card thủ công | Chưa có gateway/refund tự động |
| Tài khoản chưa có admin CRUD UI đầy đủ | Demo user chỉ trong phạm vi auth/role/provision |
| History tăng trong order | Document có thể lớn; chưa đo tải/vòng đời dài |
| Delivery status thiếu index dedicated | Query demo fallback quét58 documents |
| Validator chưa phủ mọi invariant | status_history chưa append-only; bỏ payment_history là giới hạn |
| Logical restore không bao phủ toàn server | Chưa chứng minh revision tree, security object và upload volume |
| Seed/demo baseline thay đổi khi thao tác | Cần ghi thời điểm mới, không giữ counts cố định |
| SSE dùng Apache worker | Chưa có capacity test/offline-online test |
| Deployment chưa có | Không có public URL hay evidence production |

## 17.4 Hướng phát triển

Những mục sau **CHƯA TRIỂN KHAI**: object storage/image snapshot, payment gateway/refund, email/SMS notification, mobile app hoặc shipper app, tích hợp nhà vận chuyển/GPS, replication nâng cao và cluster. Chúng là hướng mở rộng, không phải chức năng được đưa vào kết quả môn học hiện tại.

Công việc gần hơn để củng cố bằng chứng là: chụp ảnh backup/restore thật; bổ sung guest order demo ở một lượt được cho phép; kiểm tra các giới hạn validator trong một nhiệm vụ code riêng nếu cần. Không sửa schema/nghiệp vụ chỉ để báo cáo trông đầy đủ hơn.

## KẾT LUẬN

Đề tài đã dùng Fauxton và CouchDB để tìm hiểu cách quản trị dữ liệu tài liệu trong bài toán quản lý đơn hàng và giao hàng. Fauxton giúp quan sát database, document, design document, index và truy vấn; website PHP thể hiện các quy trình nghiệp vụ tương ứng. Hai giao diện có vai trò khác nhau nhưng cùng liên hệ với dữ liệu lưu trong CouchDB, giúp phần trình bày kết nối được lý thuyết với ứng dụng.

Mô hình order chứa items, shipping/payment snapshot, totals và lịch sử cho thấy lợi ích của document database khi dữ liệu thường được đọc theo một giao dịch. Delivery tracking nhúng trong order có trạng thái và history riêng, hỗ trợ giao thất bại, thử lại và giao thành công. Những ưu điểm đó đi cùng chi phí dữ liệu lặp, document tăng kích thước và trách nhiệm xử lý quan hệ/stock ở PHP. Việc lựa chọn document model vì vậy phải dựa trên luồng truy cập cụ thể.

Phần thực nghiệm đã có các Mango requests từ source, index và `_explain`, quan sát `_id`/`_rev`, stale-write 409 và validator reject 403. Những cơ chế này minh họa lọc dữ liệu, lựa chọn đường truy cập, bảo vệ cập nhật đồng thời và ràng buộc document. Chúng không thay thế phân quyền ứng dụng hay transaction nhiều document; báo cáo đã chỉ ra ranh giới và giới hạn hiện tại để tránh kết luận vượt bằng chứng.

Ứng dụng PHP minh họa auth/role, CRUD mềm sản phẩm, cart/checkout, order/delivery và SSE qua `_changes`. Các Phase reports ghi nhận kiểm thử local, browser desktop và mobile 390px đã đạt trong phạm vi đã nêu. Replication, attachment, gateway và triển khai online chưa được coi là kết quả đã thực hiện. Backup/restore vào database test mới đã qua kiểm chứng nội dung, Mango/index và validator ngày 10/10/2026.

Kết quả của đồ án là một ứng dụng và bộ tài liệu có cơ sở source để trình bày NoSQL theo bài toán cụ thể. Trước khi nộp, cần điền trang bìa và chèn ảnh thật, gồm ảnh round-trip từ database test đã tạo. Các bước này hoàn thiện minh chứng cho rubric mà không thay đổi chức năng chỉ để phù hợp báo cáo. Báo cáo hiện là bản nội dung có thể chuyển sang Word để chỉnh sửa, chưa phải bản in có đủ hình và số trang cuối cùng.

## TÀI LIỆU THAM KHẢO

Tài liệu chính thức được đối chiếu ngày 09/10/2026. Bằng chứng project thuộc thời điểm ghi trong từng Phase report. Không dùng ảnh demo của tài liệu ngoài làm ảnh project.

1. Apache CouchDB, [Technical Overview](https://docs.couchdb.org/en/stable/intro/overview.html).
2. Apache CouchDB, [Getting Started và Fauxton](https://docs.couchdb.org/en/stable/intro/tour.html).
3. Apache CouchDB, [Mango query, index và explain](https://docs.couchdb.org/en/stable/api/database/find.html).
4. Apache CouchDB, [Design Documents](https://docs.couchdb.org/en/stable/ddocs/ddocs.html).
5. Apache CouchDB, [Replication and conflict model](https://docs.couchdb.org/en/stable/replication/conflicts.html).
6. Apache CouchDB, [Changes API](https://docs.couchdb.org/en/stable/api/database/changes.html).
7. Apache CouchDB, [Attachments](https://docs.couchdb.org/en/stable/api/document/attachments.html).
8. Apache CouchDB, [Replication introduction](https://docs.couchdb.org/en/stable/replication/intro.html).
9. Apache CouchDB, [Compaction](https://docs.couchdb.org/en/stable/maintenance/compaction.html).
10. MongoDB, [Compass overview](https://www.mongodb.com/docs/compass/).
11. MongoDB, [Compass schema analysis](https://www.mongodb.com/docs/compass/schema/).
12. MongoDB, [Compass indexes](https://www.mongodb.com/docs/compass/indexes/).
13. MongoDB, [Compass performance](https://www.mongodb.com/docs/compass/performance/).
14. Hồ sơ project: README, source và các Phase 01–08; source map đi kèm liệt kê từng file. Phase 03B nằm trong report 03; không có report 09 riêng.

# PHỤ LỤC A – MỘT SỐ MANGO QUERY

Request đầy đủ, nguyên nội dung nằm tại Chương 9 và `database/couchdb/demo_queries`. Phụ lục này là mục tra nhanh, không lặp lại cả12 request.

**Bảng A.1. Chọn query cho buổi bảo vệ**

| Mục tiêu | File | Điều cần giải thích |
|---|---|---|
| Query cơ bản | 01_pending_orders.json | Selector type/status, sort, fields |
| Query theo owner | 05_customer_orders.json | Nested path và legacy customer ID |
| Query theo thời gian | 06_orders_by_date.json | $gte/$lt, khoảng nửa mở |
| Nhiều điều kiện | 07_unpaid_cod.json | Chỉ COD pending chưa thanh toán |
| Nested delivery | 11_delivery_failed.json | Current status khác tổng failed events |
| Tra mã/index | 12_tracking_code.json | use_index và explain |

Ví dụ selector tối thiểu để giải thích, **không thay request đã đo**:

```json
{"selector":{"type":"order","status":"shipping"},"fields":["_id","status"],"limit":20}
```

# PHỤ LỤC B – JSON ORDER MẪU RÚT GỌN

JSON dưới đây **được dựng để minh họa field**, dùng placeholder và giá trị tổng hợp; không phải bản dump của một người mua, không phải output đã đo và không đủ để import qua validator. Các field receiver, metadata, auth và actor nhạy cảm được lược bỏ. Mã `P06-*` giúp liên hệ với demo, không khẳng định toàn bộ số tiền/dòng hàng dưới đây là nội dung thật của order:P06-0020.

```json
{
  "_id": "order:EXAMPLE-REPORT",
  "_rev": "3-<DIGEST_DO_COUCHDB_CAP>",
  "type": "order",
  "schema_version": 2,
  "customer": {"customer_id": "P06-001"},
  "guest_order": false,
  "items": [{
    "product_id": "P06-001", "variant_id": "<VARIANT_ID>",
    "product_name": "Sản phẩm tổng hợp", "size": "M",
    "quantity": 1, "unit_price": 100000, "line_total": 100000
  }],
  "shipping": {"method_code": "BD", "method_name": "Bưu điện", "fee": 20000, "currency": "VND"},
  "payment": {"method": "cod", "status": "unpaid", "paid": false},
  "totals": {"total_quantity": 1, "subtotal": 100000, "shipping_fee": 20000, "discount_amount": 0, "grand_total": 120000},
  "status": "shipping",
  "ordered_at": "<THOI_DIEM_UTC>",
  "status_history": [{"status": "pending", "at": "<THOI_DIEM_UTC>"}],
  "delivery_tracking": {
    "tracking_code": "P06-TRK-EXAMPLE", "status": "in_transit",
    "estimated_delivery_date": "<YYYY-MM-DD>", "delivered_at": null,
    "history": [{"status": "in_transit", "at": "<THOI_DIEM_UTC>", "note": "Ví dụ rút gọn"}]
  }
}
```

Lịch sử ở ví dụ được rút ngắn nên không minh họa một transition hợp lệ đầy đủ. Để chứng minh dữ liệu thật, dùng hình JSON trên database demo và giải thích theo từng trường thực tế.

# PHỤ LỤC C – CÁC LỆNH CHẠY PROJECT

Các lệnh dưới đây được đối chiếu Composer/README; chỉ là hướng dẫn, không tự chạy trong lượt viết tài liệu. Trước mọi lệnh ghi, phải xác nhận target là DB demo/test đúng phạm vi. Không đưa `demo:reset`, DELETE database hoặc xóa volume vào kịch bản chính.

```powershell
Set-Location D:\PHP_NOSQL\PHP_COUCHDB
docker compose up --build -d
docker compose ps
Invoke-WebRequest http://127.0.0.1:8081/health
Invoke-WebRequest http://127.0.0.1:8081/health/ready
docker compose exec -T app composer demo:verify
docker compose exec -T -e COUCHDB_DATABASE=shopquan_ao_phase06_demo_test app composer couchdb:diagnostics
```

Chỉ trên môi trường test mới được chuẩn bị riêng:

```powershell
docker compose exec -T app composer seed:verify-isolated
# demo:seed chỉ tạo DB mới, từ chối DB đã tồn tại:
docker compose exec -T app composer demo:seed
```

Backup/restore là lệnh Chương 8. Lệnh catalog:indexes/update validator là mutation quản trị, không cần chạy khi chỉ quan sát. Script fixture Phase 07 có tự cleanup; nếu dùng biểu diễn trực tiếp, chuẩn bị trong một lượt test riêng và tuân thủ guard, không thực hiện thao tác phá hủy thủ công.

# PHỤ LỤC D – DANH SÁCH TEST

**Bảng D.1. Lệnh có trong project và mục đích**

| Lệnh | Source | Mục đích |
|---|---|---|
| composer auth:jwt-smoke | scripts/jwt_token_smoke.php | JWT |
| composer catalog:admin-smoke | scripts/smoke_product_admin.php | Product CRUD/role |
| composer checkout:smoke | scripts/smoke_checkout.php | Service/domain/CouchDB |
| composer checkout:http-smoke | scripts/http_smoke_checkout.php | Web/API integration |
| composer couchdb:diagnostics | scripts/couchdb_query_diagnostics.php | Read-only query/index |
| composer couchdb:failure-smoke | scripts/smoke_couchdb_unavailable.php | Generic 503 |
| composer seed:verify-isolated | scripts/verify_seed_isolated.php | Seed trong DB tạm |
| composer demo:verify | scripts/verify_phase06_demo.php | Demo baseline integrity |
| php scripts/phase07_nosql_fixture_demo.php | Cùng tên | Revision/409/reject 403, có cleanup |
| php -l [file.php] | PHP CLI | Syntax từng PHP file |
| node --check public/assets/js/order-realtime.js | Node CLI | Syntax JS realtime |
| composer validate --no-check-publish | Composer | Cấu trúc package metadata |

Các smoke scripts có thể tạo/xóa DB tạm của chính chúng; đây là phạm vi kiểm thử có guard, không phải read-only. Khi chạy lại cần ghi log/thời điểm và cập nhật Chương 16, không chỉ giữ nhãn PASS cũ. Backup/restore round-trip ngày 10/10/2026 được ghi riêng ở Chương 8 và `docs/RESTORE_VERIFICATION.md`.

## Gợi ý dàn trang khi chuyển sang Word

Dùng mẫu của trường nếu có. Nếu chưa có mẫu: giấy A4, font hỗ trợ tiếng Việt, body khoảng 13 pt, line spacing 1.3–1.5, code monospace 9–10 pt, caption rõ dưới hình và trên bảng. Bảng rộng có thể dùng trang ngang riêng, tránh thu font quá nhỏ. Giữ heading với đoạn kế tiếp; không chia ảnh và caption sang hai trang. Tạo mục lục/danh mục hình/bảng tự động sau khi chèn hình thật.

Với khoảng 32 vị trí hình, nhiều bảng và JSON/code, mục tiêu 35–50 trang có thể đạt sau dàn trang nhưng **CHƯA XÁC MINH số trang**. Không thêm đoạn lặp hoặc phóng ảnh vô lý để đủ số trang. Bản hiện tại sẵn sàng chuyển Word cho biên tập; chưa sẵn sàng nộp như một báo cáo đã có toàn bộ hình.

