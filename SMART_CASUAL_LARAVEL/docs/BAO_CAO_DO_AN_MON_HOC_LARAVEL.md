
| TRƯỜNG ĐH CÔNG THƯƠNG TP. HCM<br>KHOA CÔNG NGHỆ THÔNG TIN<br>BỘ MÔN KỸ THUẬT PHẦN MỀM<br>❖❖❖ | CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM<br>Độc lập – Tự do – Hạnh phúc<br>— — — — — — — — — |
| --- | --- |



BÁO CÁO ĐỒ ÁN CUỐI MÔN HỌC
HỌC PHẦN: LẬP TRÌNH MÃ NGUỒN MỞ

ĐỀ TÀI:
XÂY DỰNG WEBSITE BÁN HÀNG THỜI TRANG "SMART CASUAL"
BẰNG LARAVEL FRAMEWORK VÀ CƠ SỞ DỮ LIỆU MYSQL


| Sinh viên 1 (Trưởng nhóm): | Nguyễn Huy Hoàng — MSSV: 2001230266 — Lớp: 14DHTH02 |
| --- | --- |
| Sinh viên 2 (Thành viên): | Nguyễn Việt Vương — MSSV: 2001231062 — Lớp: 14DHTH02 |
| Sinh viên 3 (Thành viên): | Nguyễn Ngọc Tường Vi — MSSV: 2001231032 — Lớp: 14DHTH12 |
| Giảng viên hướng dẫn: | Thầy Nguyễn Minh Hiến |
| Ngành đào tạo: | Công nghệ Thông tin — Chuyên ngành Kỹ thuật Phần mềm |
| Thời gian thực hiện: | Học kỳ 1 — Năm học 2026 – 2027 |



TP. HỒ CHÍ MINH, THÁNG 10 NĂM 2026


# LỜI CẢM ƠN


Lời đầu tiên, nhóm sinh viên chúng em xin gửi lời cảm ơn sâu sắc và chân thành nhất đến Thầy Nguyễn Minh Hiến, giảng viên trực tiếp phụ trách và giảng dạy học phần Lập trình mã nguồn mở, Bộ môn Kỹ thuật Phần mềm, Khoa Công nghệ Thông tin, Trường Đại học Công Thương TP. Hồ Chí Minh. Trong suốt quá trình học tập và nghiên cứu đề tài, Thầy đã tận tình truyền đạt những kiến thức nền tảng vững chắc về kiến trúc framework Laravel, hướng dẫn tổ chức mã nguồn chuẩn mực, đồng thời định hướng phương pháp xử lý các bài toán kỹ thuật thực tế như cơ chế khóa dòng giao dịch bi quan, tối ưu hóa cơ sở dữ liệu quan hệ và phân quyền người dùng đa cấp.


Nhóm cũng xin bày tỏ lòng biết ơn chân thành đến toàn thể quý Thầy Cô trong Khoa Công nghệ Thông tin đã nhiệt tình giảng dạy, trang bị cho chúng em nền tảng tư duy lập trình và phương pháp nghiên cứu khoa học qua các học phần chuyên ngành. Mặc dù các thành viên trong nhóm đã nỗ lực hết mình để hoàn thiện dự án, song sản phẩm và báo cáo chắc chắn khó tránh khỏi những thiếu sót nhất định. Nhóm kính mong nhận được những nhận xét, góp ý quý báu của Thầy Cô để hệ thống tiếp tục được hoàn thiện và nâng cao hơn nữa trong tương lai.


# NHẬN XÉT CỦA GIẢNG VIÊN HƯỚNG DẪN


................................................................................................................................................................................................


................................................................................................................................................................................................


................................................................................................................................................................................................


................................................................................................................................................................................................


................................................................................................................................................................................................


................................................................................................................................................................................................


................................................................................................................................................................................................


................................................................................................................................................................................................


................................................................................................................................................................................................


................................................................................................................................................................................................


Điểm đánh giá (bằng số): ....................       Điểm đánh giá (bằng chữ): ....................................


Tp. Hồ Chí Minh, ngày ..... tháng ..... năm 2026
Giảng viên hướng dẫn
(Ký và ghi rõ họ tên)




Thầy Nguyễn Minh Hiến


# MỤC LỤC


# LỜI CẢM ƠN


# NHẬN XÉT CỦA GIẢNG VIÊN HƯỚNG DẪN


# DANH MỤC TỪ VIẾT TẮT


# DANH MỤC BẢNG BIỂU


# DANH MỤC HÌNH ẢNH


# CHƯƠNG 1. GIỚI THIỆU ĐỀ TÀI


## 1.1. Lý do chọn đề tài


## 1.2. Mục tiêu đề tài


## 1.3. Phạm vi thực hiện


# CHƯƠNG 2. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG


## 2.1. Phân tích đối tượng và vai trò người dùng


## 2.2. Sơ đồ UML Use Case tổng quan


## 2.3. Sơ đồ và thiết kế Cơ sở dữ liệu (ERD)


## 2.4. Danh mục 13 bảng nghiệp vụ chi tiết


## 2.5. Thiết kế Triggers và tính toán tự động


# CHƯƠNG 3. CÔNG NGHỆ, KIẾN TRÚC VÀ TRIỂN KHAI


## 3.1. Kiến trúc tổng thể hệ thống


## 3.2. Danh mục công nghệ và thư viện sử dụng


## 3.3. Cơ chế xác thực và phân quyền (Multi-Auth & Middleware)


## 3.4. Mô hình ORM Eloquent và bảo vệ toàn vẹn dữ liệu


## 3.5. Yêu cầu môi trường hệ thống


## 3.6. Các bước cài đặt chi tiết từ mã nguồn


## 3.7. Danh sách tài khoản thử nghiệm hệ thống


## 3.8. Các hạng mục mở rộng đã thực hiện


# CHƯƠNG 4. KẾT QUẢ VÀ KIỂM THỬ HỆ THỐNG


## 4.1. Phân hệ Khách hàng (Customer Storefront)


## 4.2. Phân hệ Quản trị viên (Admin Portal)


## 4.3. Bảng đối chiếu yêu cầu kỹ thuật (Traceability Matrix)


## 4.4. Phương pháp kiểm thử tự động (Feature Tests)


## 4.5. Kết quả kiểm thử tự động


# CHƯƠNG 5. ĐÁNH GIÁ NHÓM, HẠN CHẾ VÀ HƯỚNG PHÁT TRIỂN


## 5.1. Bảng phân công nhiệm vụ chi tiết


## 5.2. Tỷ lệ hoàn thành công việc và Minh chứng làm việc nhóm


## 5.3. Các hạn chế hiện tại


## 5.4. Kế hoạch nâng cấp và phát triển mở rộng


# KẾT LUẬN


# TÀI LIỆU THAM KHẢO


# DANH MỤC TỪ VIẾT TẮT


| Từ viết tắt | Ý nghĩa và giải thích chuyên ngành |
| --- | --- |
| 3NF | Third Normal Form — Dạng chuẩn 3 trong thiết kế cơ sở dữ liệu quan hệ |
| AOV | Average Order Value — Giá trị đơn hàng trung bình của các giao dịch |
| COD | Cash On Delivery — Phương thức thanh toán bằng tiền mặt khi nhận hàng |
| CRUD | Create, Read, Update, Delete — Bốn thao tác dữ liệu cơ bản |
| CSRF | Cross-Site Request Forgery — Tấn công giả mạo yêu cầu từ phía người dùng tin cậy |
| ERD | Entity Relationship Diagram — Sơ đồ quan hệ thực thể mô tả cấu trúc CSDL |
| IDOR | Insecure Direct Object References — Lỗ hổng tham chiếu trực tiếp đối tượng |
| MVC | Model - View - Controller — Mô hình kiến trúc phân tách Dữ liệu - Giao diện - Điều hướng |
| ORM | Object-Relational Mapping — Kỹ thuật ánh xạ dữ liệu quan hệ sang đối tượng |
| PDO | PHP Data Objects — Giao diện truy cập cơ sở dữ liệu chuẩn trong PHP |
| RBAC | Role-Based Access Control — Kiểm soát truy cập dựa trên vai trò |
| SKU | Stock Keeping Unit — Đơn vị phân loại hàng hóa lưu kho theo biến thể kích cỡ |
| XSS | Cross-Site Scripting — Hình thức tấn công chèn mã kịch bản độc hại vào trang web |



# DANH MỤC BẢNG BIỂU


• Bảng 2.1: Ma trận chuyển đổi trạng thái đơn hàng (ALLOWED_TRANSITIONS) và tác vụ đi kèm


• Bảng 2.2: Bảng đặc tả Use Case Đặt hàng trực tuyến (Place Order)


• Bảng 2.3: Bảng đặc tả Use Case Hủy đơn hàng (Cancel Order)


• Bảng 3.1: Danh mục công nghệ, thư viện và vai trò trong hệ thống Smart Casual


• Bảng 3.2: Bảng tra cứu các sự cố thường gặp khi cài đặt và giải pháp xử lý


• Bảng 3.3: Bảng quy tắc sinh mã khóa chính và mã biến thể trong hệ thống Smart Casual


• Bảng 3.4: Danh sách tài khoản thử nghiệm hệ thống phân quyền Multi-Auth


• Bảng 4.1: Bảng đối chiếu yêu cầu kỹ thuật đồ án với hiện thực hệ thống Smart Casual


• Bảng 5.1: Bảng phân công nhiệm vụ chi tiết của các thành viên trong nhóm


• Bảng 5.2: Bảng đánh giá tỷ lệ đóng góp của từng thành viên trong nhóm


# DANH MỤC HÌNH ẢNH


• Hình 2.1: Sơ đồ UML Use Case tổng quan hệ thống Smart Casual


• Hình 2.2: Sơ đồ thực thể liên kết (ERD) chi tiết 13 bảng nghiệp vụ


• Hình 2.3: Mô hình phối hợp giữa Service Layer (CheckoutService) và Database Triggers


• Hình 2.4: Sơ đồ chuyển đổi trạng thái đơn hàng (State Machine Diagram)


• Hình 3.1: Kiến trúc tổng thể hệ thống và luồng xử lý HTTP Request


• Hình 4.1: Giao diện danh mục sản phẩm và bộ lọc tìm kiếm tại route /products


• Hình 4.2: Giao diện chi tiết sản phẩm và chọn kích cỡ tại route /products/{id}


• Hình 4.3: Giao diện giỏ hàng và cập nhật số lượng tại route /cart


• Hình 4.4: Giao diện thanh toán đơn hàng tại route /checkout


• Hình 4.5: Giao diện lịch sử và chi tiết đơn hàng của khách hàng tại route /account/orders


• Hình 4.6: Bảng điều khiển tổng quan Admin Dashboard tại route /admin


• Hình 4.7: Giao diện quản lý sản phẩm và form thêm mới tại route /admin/products


• Hình 4.8: Giao diện quản lý danh mục sản phẩm tại route /admin/categories


• Hình 4.9: Giao diện chi tiết đơn hàng và cập nhật trạng thái tại route /admin/orders/{id}


• Hình 4.10: Giao diện báo cáo thống kê doanh thu tại route /admin/revenue


• Hình 4.11: Giao diện kiểm duyệt đánh giá khách hàng tại route /admin/reviews


• Hình 4.12: Giao diện thông báo lỗi Validation khi người dùng nhập dữ liệu không hợp lệ


• Hình 4.13: Thông báo phản hồi (Flash Message) sau khi thực hiện thao tác thành công


• Hình 4.14: Màn hình kết quả thực thi 23 kịch bản kiểm thử tự động qua lệnh php artisan test


• Hình 5.1: Lộ trình 4 giai đoạn nâng cấp và mở rộng hệ thống Smart Casual


• Hình 5.2: Biểu đồ thống kê lịch sử đóng góp (Contributors Graph) của nhóm trên GitHub


# CHƯƠNG 1. GIỚI THIỆU ĐỀ TÀI


Chương này trình bày bối cảnh thực tế dẫn đến việc lựa chọn đề tài xây dựng website bán hàng thời trang Smart Casual. Tiếp đó, chương xác định các mục tiêu cụ thể cần đạt được theo chuẩn đầu ra của học phần và phạm vi chức năng mà hệ thống hướng tới phục vụ các nhóm người dùng khác nhau.


## 1.1. Lý do chọn đề tài


Trong những năm gần đây, thói quen mua sắm của người tiêu dùng Việt Nam đã có sự chuyển dịch rõ rệt từ các cửa hàng truyền thống sang các nền tảng thương mại điện tử. Ngành thời trang, đặc biệt là phân khúc thời trang nam phong cách thường nhật thanh lịch (Smart Casual), đang ghi nhận mức tăng trưởng doanh thu ấn tượng nhờ sự kết hợp hài hòa giữa tính tiện dụng hàng ngày và sự chỉn chu tại môi trường công sở. Khác với các mặt hàng tiêu dùng thông thường, sản phẩm thời trang mang tính đặc thù rất cao bởi một mẫu trang phục luôn đi kèm nhiều biến thể kích cỡ (S, M, L, XL) và màu sắc khác nhau. Điều này đặt ra bài toán phức tạp cho các doanh nghiệp bán lẻ trong việc kiểm soát số lượng tồn kho theo từng biến thể chi tiết, tối ưu hóa giao diện chọn kích cỡ trực quan cho khách hàng và xử lý đơn hàng nhanh chóng.


Qua khảo sát thực tế tại các cửa hàng thời trang vừa và nhỏ, nhóm nhận thấy phần lớn các đơn vị vẫn quản lý tồn kho và tiếp nhận đơn đặt hàng một cách thủ công qua mạng xã hội hoặc sử dụng các phần mềm đơn giản. Cách làm này thường dẫn đến sai sót khi khách đặt mua các kích cỡ đã hết hàng, mất nhiều thời gian đối soát công nợ và không cung cấp được báo cáo doanh thu theo thời gian thực. Bên cạnh đó, việc phụ thuộc hoàn toàn vào các sàn thương mại điện tử lớn khiến các thương hiệu chịu chi phí chiết khấu cao và hạn chế khả năng xây dựng tệp khách hàng trung thành.


Xuất phát từ nhu cầu thực tiễn đó, nhóm sinh viên đã quyết định lựa chọn đề tài: 'Xây dựng website bán hàng thời trang Smart Casual bằng Laravel Framework và cơ sở dữ liệu MySQL'. Đề tài hướng tới việc xây dựng một hệ thống thương mại điện tử chuyên biệt, giải quyết triệt để bài toán biến thể kích cỡ, quản lý giỏ hàng kép linh hoạt cho cả khách vãng lai lẫn thành viên, kiểm soát tồn kho theo thời gian thực và cung cấp bảng điều khiển quản trị trực quan cho người quản lý.


## 1.2. Mục tiêu đề tài


Dự án hướng tới việc vận dụng toàn diện các kiến thức đã học trong học phần Lập trình mã nguồn mở để xây dựng một website bán hàng thời trang thực tế. Mục tiêu của đề tài được phân định thành hai nhóm chính: nhóm mục tiêu đáp ứng các chuẩn đầu ra của học phần (CLO) [1] và nhóm mục tiêu kỹ thuật chuyên sâu về kiến trúc phần mềm.


Về chuẩn đầu ra học phần, dự án trước hết rèn luyện cho các thành viên năng lực phân tích yêu cầu, thiết kế kiến trúc phần mềm và lập trình ứng dụng web hoàn chỉnh dựa trên ngôn ngữ PHP 8.3 cùng framework Laravel 11.x (CLO1) [2], [3]. Các thành viên áp dụng công nghệ Blade Templates kết hợp thư viện Bootstrap 5.2.3 để hiện thực hóa giao diện người dùng tương thích linh hoạt trên nhiều kích thước màn hình, đồng thời đánh giá được chất lượng trải nghiệm của khách hàng thông qua bố cục hài hòa và điều hướng thuận tiện (CLO2) [5]. Quá trình xây dựng ứng dụng cũng rèn luyện kỹ năng phối hợp làm việc nhóm thông qua việc phân chia module độc lập, điều phối mã nguồn qua hệ thống Git/GitHub và chủ động giải quyết các xung đột kỹ thuật phát sinh (CLO3). Thông qua một sản phẩm phần mềm có thể vận hành thực tế cho ngành bán lẻ thời trang (CLO4), nhóm thể hiện tinh thần tự học, nghiên cứu sâu các giải pháp khóa giao dịch và bảo vệ toàn vẹn dữ liệu (CLO5).


Về mặt kỹ thuật, hệ thống tập trung vào 4 bài toán trọng tâm. Về cơ sở dữ liệu, mục tiêu là thiết kế một cấu trúc quan hệ chặt chẽ trên MySQL 8.4 [4]. Hầu hết 11 bảng danh mục và phân hệ người dùng đều đạt chuẩn 3NF, riêng bảng DON_HANG và DANH_GIA được phi chuẩn hóa có chủ đích để lưu trữ snapshot hóa đơn và hỗ trợ khách vãng lai. Về kiến trúc, hệ thống áp dụng mô hình MVC kết hợp Service Layer nhằm tách biệt hoàn toàn các quy tắc nghiệp vụ phức tạp ra khỏi Controller, đảm bảo mã nguồn dễ bảo trì và mở rộng. Về bảo mật, mục tiêu là thiết lập cơ chế Multi-Auth với hai guard độc lập, ngăn chặn leo thang đặc quyền và các lỗ hổng phổ biến như IDOR, CSRF và SQL Injection. Cuối cùng, về kiểm thử phần mềm, nhóm đặt mục tiêu xây dựng 23 kịch bản kiểm thử tự động với PHPUnit [6] để xác thực tính đúng đắn của các luồng nghiệp vụ thanh toán và quản trị.


## 1.3. Phạm vi thực hiện


Hệ thống website bán hàng thời trang Smart Casual được thiết kế nhằm phục vụ 4 nhóm người dùng chính: Khách hàng vãng lai (Guest), Khách hàng thành viên (Customer), Nhân viên bán hàng (Staff) và Quản lý cửa hàng (Manager).


Đối với phân hệ khách hàng, hệ thống cho phép khách hàng vãng lai duyệt danh mục, tìm kiếm theo từ khóa, lọc theo phân loại sản phẩm, chọn kích cỡ, quản lý giỏ hàng trong Session và tiến hành đặt hàng thông qua hình thức thanh toán khi nhận hàng (COD) hoặc chuyển khoản ngân hàng. Cả khách vãng lai và khách thành viên đều có thể gửi đánh giá và chấm điểm sao cho sản phẩm. Khi đăng ký tài khoản thành viên, khách hàng được cấp khu vực cá nhân để theo dõi trạng thái đơn hàng và xem lại lịch sử mua sắm. Đối với phân hệ quản trị, nhân viên bán hàng được phân quyền cập nhật sản phẩm, xử lý đơn hàng và kiểm duyệt nhận xét. Quản lý cửa hàng được cấp thêm quyền xem báo cáo thống kê doanh thu, quản lý tài khoản nhân viên và cấu hình mã giảm giá.


Các chức năng đã hoàn thành bao gồm: quản lý danh mục và sản phẩm kèm nhiều biến thể kích cỡ; giỏ hàng kép thông minh tự động đồng bộ từ Session vào CSDL khi đăng nhập; quy trình thanh toán nguyên tử kết hợp khóa dòng bi quan lockForUpdate() và 4 Database Triggers tự động cập nhật tổng tiền; cùng bảng điều khiển quản trị thống kê doanh thu theo thời gian thực.


Bên cạnh các kết quả đạt được, phiên bản hiện tại vẫn còn 3 giới hạn nằm ngoài phạm vi thực hiện: hệ thống chưa kết nối cổng thanh toán trực tuyến tự động (VNPay/MoMo), chưa tích hợp dịch vụ gửi email xác nhận qua hàng đợi nền (SMTP Queue), và trang danh mục sản phẩm chưa được hỗ trợ bởi bộ nhớ đệm phân tán Redis.


# CHƯƠNG 2. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG


Chương này đi sâu vào phân tích các đối tượng sử dụng hệ thống, mô tả tương tác chức năng thông qua sơ đồ UML Use Case kèm bảng đặc tả chi tiết, trình bày cấu trúc cơ sở dữ liệu quan hệ qua sơ đồ ERD, danh mục 13 bảng nghiệp vụ cùng bảng hỗ trợ kỹ thuật CHECKOUT_IDEMPOTENCY, và phân tích cơ chế tính toán tự động bằng Database Triggers.


## 2.1. Phân tích đối tượng và vai trò người dùng


Hệ thống phân định rõ quyền hạn của 4 tác nhân: Khách vãng lai, Khách thành viên, Nhân viên bán hàng và Quản lý cửa hàng. Khách vãng lai có thể xem sản phẩm, giỏ hàng, đặt hàng và gửi đánh giá (lưu tên trực tiếp). Khách thành viên kế thừa toàn bộ quyền của khách vãng lai và có thêm tính năng quản lý tài khoản, xem lịch sử đơn hàng. Nhân viên bán hàng quản lý sản phẩm, đơn hàng và duyệt đánh giá. Quản lý cửa hàng kế thừa quyền nhân viên và nắm quyền xem báo cáo doanh số, quản lý nhân sự.


## 2.2. Sơ đồ UML Use Case tổng quan


Sơ đồ Use Case tổng quan mô tả mối quan hệ kế thừa và tương tác của 4 tác nhân với hệ thống được thể hiện tại Hình 2.1 dưới đây.


*Hình 2.1: Sơ đồ UML Use Case tổng quan hệ thống Smart Casual*


Để làm rõ luồng xử lý của các nghiệp vụ then chốt, nhóm xây dựng Bảng đặc tả Use Case cho hai chức năng cốt lõi: Đặt hàng trực tuyến (Bảng 2.2) và Hủy đơn hàng (Bảng 2.3).


**Bảng 2.2: Bảng đặc tả Use Case Đặt hàng trực tuyến (Place Order)**


| Tên Use Case | Đặt hàng trực tuyến (Place Order) — Mã: UC-05 |
| --- | --- |
| Tác nhân (Actor) | Khách hàng vãng lai (Guest) hoặc Khách hàng thành viên (Customer) |
| Mục đích | Cho phép khách hàng tạo đơn hàng mua các sản phẩm thời trang trong giỏ hàng và chọn hình thức thanh toán. |
| Tiền điều kiện | Giỏ hàng phải có ít nhất 1 sản phẩm có số lượng hợp lệ và còn hàng trong kho (SoLuong > 0). |
| Hậu điều kiện | Đơn hàng mới được tạo trong bảng DON_HANG ở trạng thái pending; chi tiết lưu vào CHI_TIET_DON_HANG; tồn kho CHI_TIET_SP bị trừ; giỏ hàng bị xóa; mã voucher giảm số lượt dùng; khóa CHECKOUT_IDEMPOTENCY được tạo. |
| Luồng sự kiện chính | 1. Khách hàng truy cập trang Giỏ hàng (/cart) và bấm 'Tiến hành đặt hàng'.<br>2. Hệ thống hiển thị màn hình Thanh toán (/checkout) kèm tóm tắt đơn hàng và ô nhập voucher.<br>3. Khách nhập thông tin nhận hàng, chọn phương thức vận chuyển và hình thức thanh toán (COD hoặc Chuyển khoản).<br>4. (Tùy chọn) Khách nhập mã voucher và bấm 'Áp dụng', hệ thống trừ tiền chiết khấu.<br>5. Khách bấm nút 'Xác nhận Đặt hàng'.<br>6. Hệ thống thực thi giao dịch nguyên tử trong CheckoutService: kiểm tra lockForUpdate(), trừ tồn kho, tạo DON_HANG và CHI_TIET_DON_HANG, kích hoạt triggers CSDL, xóa giỏ hàng.<br>7. Hệ thống chuyển hướng khách sang màn hình Chi tiết đơn hàng vừa tạo kèm Flash message thành công. |
| Luồng thay thế | 3a. Dữ liệu nhập không hợp lệ: Thiếu họ tên hoặc SĐT sai định dạng, hệ thống chặn gửi form và hiển thị lỗi validation.<br>4a. Voucher không hợp lệ: Hết lượt dùng hoặc đơn hàng chưa đạt giá trị tối thiểu, hệ thống báo lỗi và giữ nguyên tổng tiền.<br>6a. Biến thể hết hàng (Overselling): Nếu tồn kho nhỏ hơn số lượng đặt mua, hệ thống hủy giao dịch và thông báo sản phẩm vừa hết hàng. |



**Bảng 2.3: Bảng đặc tả Use Case Hủy đơn hàng (Cancel Order)**


| Tên Use Case | Hủy đơn hàng (Cancel Order) — Mã: UC-13b |
| --- | --- |
| Tác nhân (Actor) | Khách hàng sở hữu đơn (Storefront) hoặc Nhân viên bán hàng / Quản lý (Admin Portal) |
| Mục đích | Cho phép hủy một đơn hàng chưa xuất kho, tự động hoàn trả số lượng sản phẩm vào kho và hoàn lại lượt voucher. |
| Tiền điều kiện | Đơn hàng phải ở một trong ba trạng thái hợp lệ cho phép hủy: pending (Chờ xác nhận), confirmed (Đã xác nhận), hoặc packing (Đang đóng gói). |
| Hậu điều kiện | Trạng thái đơn hàng chuyển thành cancelled; toàn bộ số lượng sản phẩm được cộng hoàn lại vào kho CHI_TIET_SP; voucher được cộng hoàn 1 lượt dùng; lý do hủy lưu vào trường GhiChu. |
| Luồng sự kiện chính | 1. Tác nhân truy cập trang Chi tiết đơn hàng (/account/orders/{id} hoặc /admin/orders/{id}).<br>2. Tác nhân bấm nút 'Hủy đơn hàng' và nhập lý do hủy vào hộp thoại xác nhận.<br>3. Hệ thống kiểm tra trạng thái hiện tại dựa trên ma trận ALLOWED_TRANSITIONS.<br>4. Hệ thống thực thi CheckoutService::cancelOrder() trong DB::transaction(): cộng hoàn tồn kho CHI_TIET_SP, tăng lượt MaGiamGia, cập nhật TrangThaiDonHang = 'cancelled' và ghi lý do hủy vào GhiChu.<br>5. Hệ thống hiển thị thông báo phản hồi 'Đã hủy đơn hàng thành công và hoàn trả số lượng vào kho'. |
| Luồng thay thế | 3a. Đơn hàng không đủ điều kiện hủy: Nếu đơn đã chuyển sang shipping (Đang giao) hoặc delivered (Đã giao), hệ thống từ chối yêu cầu và hiển thị lỗi: 'Không thể hủy đơn hàng ở trạng thái Đang giao hàng / Đã giao thành công'.<br>1a. Khách hàng truy cập đơn của người khác (IDOR): Nếu khách sửa ID đơn trên URL, hệ thống chặn truy cập và trả về lỗi 403 Forbidden hoặc 404 Not Found. |



## 2.3. Sơ đồ và thiết kế Cơ sở dữ liệu (ERD)


Sơ đồ quan hệ thực thể (ERD) thể hiện cấu trúc 13 bảng nghiệp vụ của hệ thống Smart Casual được minh họa tại Hình 2.2 dưới đây.


| [HÌNH 2.2: SƠ ĐỒ THỰC THỂ LIÊN KẾT (ERD) CHI TIẾT 13 BẢNG NGHIỆP VỤ]<br>• Mô tả: 13 bảng nghiệp vụ đầy đủ khóa chính (PK), khóa ngoại (FK) và liên kết.<br>• Phân hệ: Người dùng (NGUOI_DUNG, KHACH_HANG, NHAN_VIEN); Hàng hóa (DANH_MUC, SAN_PHAM, KICH_THUOC, CHI_TIET_SP, HINH_ANH_SP); Giao dịch (GIO_HANG, MA_GIAM_GIA, PHUONG_THUC_VAN_CHUYEN, DON_HANG, CHI_TIET_DON_HANG, DANH_GIA). |
| --- |



*Hình 2.2: Sơ đồ thực thể liên kết (ERD) chi tiết 13 bảng nghiệp vụ*


Về nguyên tắc thiết kế, 11 bảng danh mục và phân hệ người dùng đều đạt chuẩn 3NF. Riêng đối với bảng DON_HANG và DANH_GIA, nhóm đã thực hiện kỹ thuật phi chuẩn hóa có chủ đích (Intentional Denormalization). Cụ thể, bảng DON_HANG lưu trữ trực tiếp TongSoLuong, TongTien và ThanhTien nhằm tạo ra một chứng từ tài chính bất biến (Snapshot) tại thời điểm đặt hàng, giúp giá trị thanh toán không bị sai lệch khi giá sản phẩm hoặc phí ship biến động trong tương lai, đồng thời tối ưu hiệu năng hiển thị danh sách đơn hàng cho admin mà không phải tính hàm SUM() lặp lại. Bảng DANH_GIA lưu trực tiếp TenKhachHang nhằm phục vụ khách hàng vãng lai gửi nhận xét khi MaKH mang giá trị NULL, đồng thời bảo toàn tên người viết ngay cả khi người dùng đổi tên tài khoản sau này.


## 2.4. Danh mục 13 bảng nghiệp vụ chi tiết


Hệ thống bao gồm 13 bảng nghiệp vụ chính: NGUOI_DUNG (tài khoản định danh cơ sở), KHACH_HANG (thông tin khách hàng, tích điểm), NHAN_VIEN (nhân sự, chức vụ NhanVien/QuanLy), DANH_MUC (phân loại thời trang), SAN_PHAM (thông tin chung, chất liệu, giá gốc, giá khuyến mãi), KICH_THUOC (bảng kích cỡ chuẩn S, M, L, XL, XXL), CHI_TIET_SP (biến thể kích cỡ, tồn kho, SKU duy nhất), HINH_ANH_SP (danh sách ảnh sản phẩm), GIO_HANG (giỏ hàng thành viên lưu CSDL), MA_GIAM_GIA (voucher chiết khấu phần trăm hoặc số tiền), PHUONG_THUC_VAN_CHUYEN (hình thức giao hàng Tiêu chuẩn / Hỏa tốc), DON_HANG (thông tin đơn hàng, tổng tiền, trạng thái), CHI_TIET_DON_HANG (dòng mặt hàng, số lượng, đơn giá xuất kho), và DANH_GIA (nhận xét và điểm số sao của khách hàng).


Bên cạnh 13 bảng nghiệp vụ trên, hệ thống sử dụng một bảng kỹ thuật chuyên dụng mang tên CHECKOUT_IDEMPOTENCY (khởi tạo qua Migration 000004). Bảng này lưu trữ idempotency_key, customer_id, request_hash (mã băm SHA-256), order_id và status nhằm chặn đứng việc trừ kho hoặc tạo trùng lặp đơn hàng khi người dùng bấm gửi yêu cầu thanh toán nhiều lần liên tiếp.


## 2.5. Thiết kế Triggers và tính toán tự động


Mô hình tương tác giữa tầng dịch vụ CheckoutService và 4 Database Triggers trên MySQL 8.4 được mô tả tại Hình 2.3 dưới đây.


| [HÌNH 2.3: MÔ HÌNH PHỐI HỢP GIỮA SERVICE LAYER (CHECKOUTSERVICE) VÀ DATABASE TRIGGERS]<br>• Luồng: Client gửi đơn -> Controller -> CheckoutService::placeOrder() -> DB::transaction().<br>• Khóa dòng bi quan lockForUpdate() trên CHI_TIET_SP kiểm tra tồn kho.<br>• Trigger BEFORE INSERT trên DON_HANG gán phí ship và tính ThanhTien tạm.<br>• Triggers AFTER INSERT/UPDATE/DELETE trên CHI_TIET_DON_HANG tự động UPDATE DON_HANG (TongSoLuong, TongTien, ThanhTien). |
| --- |



*Hình 2.3: Mô hình phối hợp giữa Service Layer (CheckoutService) và Database Triggers*


Bốn Database Triggers hoạt động đồng bộ: trg_tudong_lay_phiship_insert (BEFORE INSERT ON DON_HANG) tự động lấy cước phí vận chuyển và tính ThanhTien khởi tạo; ba triggers trg_capnhat_tong_donhang_after_insert, after_update, after_delete (trên CHI_TIET_DON_HANG) tự động tính toán lại TongSoLuong, TongTien và ThanhTien = max(0, TongTien + PhiVanChuyen - GiamGia). Nhờ đó, trường ThanhTien luôn được đồng bộ tức thì và chuẩn xác.


Nhóm quyết định chọn Database Triggers thay vì Eloquent Observers vì 3 ưu thế: (1) Đảm bảo toàn vẹn dữ liệu ở cấp độ CSDL cao nhất ngay cả khi thao tác trực tiếp qua MySQL CLI hoặc batch update; (2) Tối ưu hóa hiệu năng và giảm độ trễ mạng khi việc tính gộp SUM() thực thi trực tiếp trên bộ nhớ máy chủ MySQL; (3) Loại trừ rủi ro bảo trì khi lập trình viên quên đăng ký Observer trong AppServiceProvider.


Chi tiết máy trạng thái hữu hạn kiểm soát vòng đời đơn hàng thông qua hằng số ALLOWED_TRANSITIONS trong CheckoutService được minh họa tại Hình 2.4 và tổng hợp tại Bảng 2.1.


| [HÌNH 2.4: SƠ ĐỒ CHUYỂN ĐỔI TRẠNG THÁI ĐƠN HÀNG (STATE MACHINE DIAGRAM)]<br>• Luồng chuẩn: pending -> confirmed -> packing -> shipping -> delivered.<br>• Luồng hủy: pending, confirmed, packing đều cho phép chuyển sang cancelled.<br>• Tác vụ tự động khi hủy: hoàn trả tồn kho CHI_TIET_SP và phục hồi số lượt voucher MA_GIAM_GIA. |
| --- |



*Hình 2.4: Sơ đồ chuyển đổi trạng thái đơn hàng (State Machine Diagram)*


**Bảng 2.1: Ma trận chuyển đổi trạng thái đơn hàng (ALLOWED_TRANSITIONS) và tác vụ đi kèm**


| Trạng thái hiện tại | Trạng thái tiếp theo | Tên tiếng Việt | Tác nhân | Tác vụ tự động đi kèm trong CheckoutService |
| --- | --- | --- | --- | --- |
| pending | confirmed, cancelled | Chờ xác nhận | Khách / NV | Chuyển cancelled: hoàn tồn kho CTSP và phục hồi lượt voucher. |
| confirmed | packing, cancelled | Đã xác nhận | Khách / NV | Chuyển packing: chuẩn bị đóng gói; chuyển cancelled: hoàn kho và voucher. |
| packing | shipping, cancelled | Đang đóng gói | Khách / Quản lý | Khách vẫn có thể tự bấm hủy trên Storefront; chuyển shipping: bàn giao vận chuyển. |
| shipping | delivered | Đang giao hàng | Nhân viên | Khóa nút Hủy; chuyển delivered: tự động cập nhật DaThanhToan = true nếu COD. |
| delivered / cancelled | (Kết thúc) | Hoàn tất / Đã hủy | — | Trạng thái đóng, không cho phép kích hoạt chuyển tiếp trạng thái nào khác. |



# CHƯƠNG 3. CÔNG NGHỆ, KIẾN TRÚC VÀ TRIỂN KHAI


Chương này làm rõ kiến trúc phân tầng của hệ thống, danh mục công nghệ sử dụng, giải trình lý do duy trì Laravel 11.x, cơ chế bảo mật Multi-Auth, quy tắc sinh mã chuỗi trong Eloquent Model, các bước cài đặt chi tiết từ mã nguồn và các hạng mục mở rộng đã đạt được.


## 3.1. Kiến trúc tổng thể hệ thống


Kiến trúc hệ thống Smart Casual và luồng xử lý HTTP Request được minh họa chi tiết tại Hình 3.1.


| [HÌNH 3.1: KIẾN TRÚC TỔNG THỂ HỆ THỐNG VÀ LUỒNG XỬ LÝ HTTP REQUEST]<br>• 8 tầng kiến trúc: Client Browser -> Web Server (public/index.php, routing) -> Middleware (CSRF, Multi-Auth, ManagerOnly) -> Controllers -> Service Layer (CartService, CheckoutService) -> Eloquent Models -> MySQL 8.4 (Triggers) -> Blade Views Rendering -> HTTP Response. |
| --- |



*Hình 3.1: Kiến trúc tổng thể hệ thống và luồng xử lý HTTP Request*


## 3.2. Danh mục công nghệ và thư viện sử dụng


Danh mục 9 công nghệ và thư viện nền tảng được tổng hợp tại Bảng 3.1.


**Bảng 3.1: Danh mục công nghệ, thư viện và vai trò trong hệ thống Smart Casual**


| STT | Công nghệ / Thư viện | Phiên bản | Vai trò và phạm vi ứng dụng trong dự án |
| --- | --- | --- | --- |
| 1 | PHP | 8.3 | Ngôn ngữ lập trình máy chủ chính; sử dụng Match Expression, Type Hinting nghiêm ngặt [3]. |
| 2 | Laravel Framework | 11.x | Framework nền tảng; cung cấp cấu trúc MVC, Routing, Middleware, Eloquent ORM [2]. |
| 3 | MySQL Server | 8.4 Community | Hệ quản trị CSDL quan hệ; lưu trữ 13 bảng nghiệp vụ, quản lý khóa ngoại và 4 Triggers [4]. |
| 4 | Blade Template | Tích hợp sẵn | Công cụ dựng giao diện phía máy chủ (SSR); kế thừa layouts/app và layouts/admin, chống XSS. |
| 5 | Bootstrap | 5.2.3 | Thư viện CSS/JS dựng giao diện; cung cấp hệ thống lưới Grid Responsive, Modal, Buttons [5]. |
| 6 | FontAwesome | 6.x Free | Bộ thư viện biểu tượng vector; hiển thị các icon trực quan trên menu và nút thao tác. |
| 7 | PHPUnit | 10.x | Khung kiểm thử tự động; thiết kế và chạy 23 kịch bản kiểm thử Feature/Unit tests [6]. |
| 8 | Composer | 2.x | Trình quản lý các gói phụ thuộc PHP; tối ưu hóa cơ chế nạp lớp tự động PSR-4. |
| 9 | Git / GitHub | Mới nhất | Hệ thống quản lý mã nguồn phân tán; điều phối nhánh làm việc, quản lý lịch sử commit. |



Về lý do sử dụng phiên bản Laravel 11.x: Nhóm đã chủ động nghiên cứu và bắt tay vào khởi tạo dự án từ sớm, trước thời điểm giảng viên cung cấp tệp yêu cầu đồ án chính thức. Tại thời điểm nhóm bắt đầu triển khai, Laravel 11.x là phiên bản chính thức phổ biến và ổn định nhất, tương thích tối đa với hệ thống phòng máy tính thực hành tại HUIT (chạy PHP 8.2 và 8.3). Khi nhận được file yêu cầu môn học, kiến trúc cốt lõi của dự án đã vận hành hoàn chỉnh và ổn định. Do đó, nhóm quyết định duy trì Laravel 11.x để đảm bảo tiến độ và chất lượng, đồng thời đã xây dựng sẵn phương án nâng cấp lên Laravel 12 và 13 bằng cách chạy composer outdated, kiểm tra breaking changes và chạy lại bộ test suite.


## 3.3. Cơ chế xác thực và phân quyền (Multi-Auth & Middleware)


Hệ thống cấu hình 2 guard độc lập trong config/auth.php: guard web (sử dụng bảng KHACH_HANG) và guard admin (sử dụng bảng NHAN_VIEN). Sự tách biệt này giúp loại bỏ nguy cơ chiếm quyền điều khiển phiên làm việc (Session Hijacking) và leo thang đặc quyền. Lớp bảo vệ bao gồm AdminMiddleware chặn các truy cập trái phép vào tiền tố /admin/*, và ManagerOnlyMiddleware bảo vệ tuyến đường báo cáo doanh thu /admin/revenue, chỉ cho phép nhân viên có chức vụ QuanLy truy cập.


## 3.4. Mô hình ORM Eloquent và bảo vệ toàn vẹn dữ liệu


Hệ thống thích ứng với cơ sở dữ liệu tiếng Việt và khóa chính dạng chuỗi thông qua việc ghi đè các thuộc tính trong Model: protected $table, protected $primaryKey, public $incrementing = false, và protected $keyType = 'string'. Quy tắc sinh mã khóa chính và mã biến thể thực tế trong mã nguồn được thể hiện tại Bảng 3.3.


**Bảng 3.3: Bảng quy tắc sinh mã khóa chính và mã biến thể trong hệ thống Smart Casual**


| Tên thực thể | Cột khóa chính | Cơ chế sinh mã | Hàm / Cú pháp cụ thể trong mã nguồn | Ví dụ thực tế |
| --- | --- | --- | --- | --- |
| Đơn hàng | MaDonHang | Tự động 100% | 'DH' . date('Ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5)) | DH20261026E3A9F |
| Khách hàng | MaKH | Tự động 100% | 'KH' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5)) | KH7A1B9 |
| Sản phẩm | MaSP | Admin tự nhập | strtoupper($validated['MaSP']) kết hợp kiểm tra tính duy nhất (Unique) | A25, Q13 |
| Biến thể SKU | MaCTSP | Tự động theo MaSP | 'CT' . $sp->MaSP . 'Sz' . $validated['MaKichThuoc'] | CTA25SzM |
| Hình ảnh SP | MaHinhAnh | Tự động theo MaSP | $sp->MaSP . 'H01' | A25H01 |
| Đánh giá | MaDanhGia | Tự động 100% | 'DG' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)) | DG8A2F1C4B |



Riêng trên bảng GIO_HANG có khóa chính phức hợp hai cột (MaKH, MaCTSP), Eloquent mặc định không hỗ trợ. Nhóm đã xử lý triệt để bằng cách ghi đè phương thức setKeysForSaveQuery() trong GioHang.php để chèn điều kiện kép WHERE MaKH = ? AND MaCTSP = ? vào câu truy vấn UPDATE/DELETE của MySQL.


## 3.5. Yêu cầu môi trường hệ thống


Hệ thống yêu cầu máy chủ cài đặt PHP >= 8.2 (khuyến nghị PHP 8.3), Composer 2.x, MySQL Server >= 8.0 (khuyến nghị MySQL 8.4 Community Server), các tiện ích mở rộng bắt buộc: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, fileinfo.


## 3.6. Các bước cài đặt chi tiết từ mã nguồn


Người dùng thực hiện tuần tự 7 bước cài đặt sau:


Bước 1: Tải mã nguồn từ kho GitHub:


| git clone https://github.com/VuongNguyen1632005/PHP_NOSQL.git<br>cd SMART_CASUAL_LARAVEL |
| --- |



Bước 2: Cài đặt các gói phụ thuộc PHP:


| composer install |
| --- |



Bước 3: Thiết lập tệp cấu hình .env (hỗ trợ Windows và Linux):


| # Trên Windows:<br>copy .env.example .env<br><br># Trên Linux / macOS:<br>cp .env.example .env |
| --- |



Bước 4: Khởi tạo khóa bảo mật ứng dụng:


| php artisan key:generate |
| --- |



Bước 5: Khởi tạo và nạp cơ sở dữ liệu mẫu (Khuyến nghị nạp trực tiếp file dump):


| mysql -u root -p smart_casual_shop < database/smart_casual_shop_dump.sql<br># Hoặc chạy migration: php artisan migrate --seed |
| --- |



Bước 6: Khởi tạo CSDL kiểm thử smart_casual_test để chạy test:


| mysql -u root -e "CREATE DATABASE smart_casual_test CHARACTER SET utf8mb4;"<br>php artisan test |
| --- |



Bước 7: Tạo symbolic link ảnh và khởi chạy máy chủ:


| php artisan storage:link<br>php artisan serve |
| --- |



Dự án vận hành 100% dựa trên các thư viện tĩnh đã đóng gói sẵn trong public/assets/ (Bootstrap 5.2.3, FontAwesome 6). Toàn bộ 21 view Blade liên kết qua thẻ link và script truyền thống, hoàn toàn không cần chạy npm install hay npm run build khi nộp bài.


**Bảng 3.2: Bảng tra cứu các sự cố thường gặp khi cài đặt và giải pháp xử lý**


| Lỗi gặp phải | Nguyên nhân kỹ thuật | Cách xử lý triệt để |
| --- | --- | --- |
| Access denied for user 'root'@'localhost' | Sai mật khẩu MySQL trong tệp .env | Mở .env sửa DB_PASSWORD chính xác, sau đó chạy php artisan config:clear. |
| Target class [CartService] does not exist | Chưa cập nhật nạp lớp tự động PSR-4 | Chạy lệnh composer dump-autoload trong terminal. |
| Hình ảnh sản phẩm bị vỡ (Lỗi 404) | Chưa tạo Symbolic Link thư mục storage | Chạy lệnh php artisan storage:link để tạo liên kết sang public/storage. |
| Lỗi Seeder nạp file JSON | Tệp Seeder tham chiếu lệch thư mục | Nạp trực tiếp tệp kết xuất database/smart_casual_shop_dump.sql vào MySQL. |
| Unknown database 'smart_casual_test' | Chưa tạo CSDL kiểm thử cho PHPUnit | Tạo database rỗng smart_casual_test trong MySQL trước khi chạy test. |



## 3.7. Danh sách tài khoản thử nghiệm hệ thống


Danh sách tài khoản demo được thể hiện tại Bảng 3.4.


**Bảng 3.4: Danh sách tài khoản thử nghiệm hệ thống phân quyền Multi-Auth**


| Vai trò | Email đăng nhập | Mật khẩu | Guard | Quyền hạn |
| --- | --- | --- | --- | --- |
| Quản lý (Manager) | admin@smartcasual.vn | Admin@12345 | admin | Toàn quyền quản trị, xem doanh thu /admin/revenue. |
| Nhân viên (Staff) | nv_kho01@smartcasual.vn | Staff@12345 | admin | Xử lý đơn hàng, quản lý sản phẩm, chặn xem doanh thu. |
| Khách hàng (Customer) | khachhang1@gmail.com | Password@123 | web | Mua hàng, giỏ hàng CSDL, xem lịch sử đơn cá nhân. |



## 3.8. Các hạng mục mở rộng đã thực hiện


Đối chiếu với Mục 5 của quy định học phần, nhóm đã hoàn thành 5 hạng mục mở rộng: (1) Quản lý tệp tin tải lên qua Laravel Storage; (2) Giao diện người dùng tương thích đa thiết bị (Responsive) với Bootstrap 5; (3) Phân quyền nâng cao với ManagerOnlyMiddleware; (4) Khóa dòng bi quan lockForUpdate() kiểm soát giao dịch tranh chấp kho; (5) Tự động hóa tính toán bằng 4 Database Triggers. Hệ thống áp dụng cơ chế ẩn sản phẩm an toàn bằng cờ logic TrangThai = false, không dùng trait SoftDeletes.


# CHƯƠNG 4. KẾT QUẢ VÀ KIỂM THỬ HỆ THỐNG


Chương này minh họa kết quả triển khai giao diện phân hệ Khách hàng và Quản trị, trình bày Bảng đối chiếu yêu cầu kỹ thuật (Traceability Matrix), phương pháp kiểm thử tự động trên CSDL độc lập và trích xuất kết quả 23 test cases.


## 4.1. Phân hệ Khách hàng (Customer Storefront)


Phân hệ Khách hàng bao gồm các màn hình chính:


| [HÌNH 4.1: GIAO DIỆN DANH MỤC SẢN PHẨM (CATALOG)]<br>• Route: /products<br>• Khách vãng lai, hiển thị lưới sản phẩm, tìm kiếm, lọc danh mục, phân trang 16 sp/trang. |
| --- |



*Hình 4.1: Giao diện danh mục sản phẩm và bộ lọc tìm kiếm tại route /products*


| [HÌNH 4.2: GIAO DIỆN CHI TIẾT SẢN PHẨM VÀ CHỌN KÍCH CỠ]<br>• Route: /products/{id}<br>• Chọn size S, M, L, XL, hiển thị tồn kho, form nhận xét và đánh giá sao. |
| --- |



*Hình 4.2: Giao diện chi tiết sản phẩm và chọn kích cỡ tại route /products/{id}*


| [HÌNH 4.3: GIAO DIỆN GIỎ HÀNG]<br>• Route: /cart<br>• Tăng giảm số lượng, xóa mặt hàng, hiển thị tạm tính. |
| --- |



*Hình 4.3: Giao diện giỏ hàng và cập nhật số lượng tại route /cart*


| [HÌNH 4.4: GIAO DIỆN THANH TOÁN ĐƠN HÀNG]<br>• Route: /checkout<br>• Form người nhận, chọn phương thức vận chuyển và thanh toán COD/Bank, áp dụng voucher. |
| --- |



*Hình 4.4: Giao diện thanh toán đơn hàng tại route /checkout*


| [HÌNH 4.5: GIAO DIỆN LỊCH SỬ ĐƠN HÀNG CỦA KHÁCH HÀNG]<br>• Route: /account/orders<br>• Danh sách đơn đã mua, trạng thái đơn và nút Hủy đơn (nếu pending, confirmed, packing). |
| --- |



*Hình 4.5: Giao diện lịch sử và chi tiết đơn hàng của khách hàng tại route /account/orders*


Khi khách hàng để trống các trường bắt buộc hoặc nhập số điện thoại sai định dạng, hệ thống chặn gửi form và hiển thị thông báo lỗi Validation viền đỏ trực tiếp bên dưới từng ô nhập liệu như minh họa tại Hình 4.12.


| [HÌNH 4.12: GIAO DIỆN THÔNG BÁO LỖI VALIDATION KHI NGƯỜI DÙNG NHẬP DỮ LIỆU KHÔNG HỢP LỆ]<br>• Route: /checkout (hoặc /register)<br>• Thao tác: Bấm 'Đặt hàng' khi chưa nhập Họ tên, Số điện thoại hoặc Địa chỉ.<br>• Hiển thị: Viền đỏ và thông báo lỗi validation chi tiết bên dưới từng trường. |
| --- |



*Hình 4.12: Giao diện thông báo lỗi Validation khi người dùng nhập dữ liệu không hợp lệ*


Đồng thời, khi khách hàng thực hiện thành công các thao tác thay đổi dữ liệu (như thêm sản phẩm vào giỏ hoặc áp dụng mã giảm giá), hệ thống gửi thông báo phản hồi (Flash Message) dạng Alert màu xanh ở phía trên đầu trang như thể hiện tại Hình 4.13.


| [HÌNH 4.13: THÔNG BÁO PHẢN HỒI (FLASH MESSAGE) SAU KHI THỰC HIỆN THAO TÁC THÀNH CÔNG]<br>• Route: /cart (hoặc /checkout)<br>• Thao tác: Áp dụng thành công mã voucher giảm giá 'SUMMER10'.<br>• Hiển thị: Hộp thông báo màu xanh 'Áp dụng mã giảm giá thành công!'. |
| --- |



*Hình 4.13: Thông báo phản hồi (Flash Message) sau khi thực hiện thao tác thành công*


## 4.2. Phân hệ Quản trị viên (Admin Portal)


Phân hệ Quản trị viên bao gồm các màn hình vận hành chuyên nghiệp:


| [HÌNH 4.6: BẢNG ĐIỀU KHIỂN TỔNG QUAN ADMIN DASHBOARD]<br>• Route: /admin<br>• Thẻ thống kê doanh số, đơn hàng mới, sản phẩm và khách hàng. |
| --- |



*Hình 4.6: Bảng điều khiển tổng quan Admin Dashboard tại route /admin*


| [HÌNH 4.7: GIAO DIỆN QUẢN LÝ SẢN PHẨM]<br>• Route: /admin/products<br>• Danh sách sản phẩm, giá bán, nút Sửa, Ẩn sản phẩm (TrangThai) và Thêm mới. |
| --- |



*Hình 4.7: Giao diện quản lý sản phẩm và form thêm mới tại route /admin/products*


| [HÌNH 4.8: GIAO DIỆN QUẢN LÝ DANH MỤC]<br>• Route: /admin/categories<br>• Quản lý các nhóm thời trang Áo sơ mi, Quần tây, Áo Polo... |
| --- |



*Hình 4.8: Giao diện quản lý danh mục sản phẩm tại route /admin/categories*


| [HÌNH 4.9: GIAO DIỆN CHI TIẾT ĐƠN HÀNG VÀ ĐỔI TRẠNG THÁI]<br>• Route: /admin/orders/{id}<br>• Xem danh sách mặt hàng, đổi trạng thái theo ALLOWED_TRANSITIONS. |
| --- |



*Hình 4.9: Giao diện chi tiết đơn hàng và cập nhật trạng thái tại route /admin/orders/{id}*


| [HÌNH 4.10: GIAO DIỆN BÁO CÁO DOANH THU]<br>• Route: /admin/revenue<br>• Chỉ tài khoản Quản lý truy cập; biểu đồ và bảng doanh số theo thời gian, chỉ số AOV. |
| --- |



*Hình 4.10: Giao diện báo cáo thống kê doanh thu tại route /admin/revenue*


| [HÌNH 4.11: GIAO DIỆN KIỂM DUYỆT ĐÁNH GIÁ KHÁCH HÀNG]<br>• Route: /admin/reviews<br>• Danh sách nhận xét của khách, nút Ẩn/Hiện đánh giá ngoài trang chủ. |
| --- |



*Hình 4.11: Giao diện kiểm duyệt đánh giá khách hàng tại route /admin/reviews*


## 4.3. Bảng đối chiếu yêu cầu kỹ thuật (Traceability Matrix)


Bảng đối chiếu yêu cầu kỹ thuật (Traceability Matrix) tại Bảng 4.1 ánh xạ trực tiếp từng yêu cầu bắt buộc và mở rộng của học phần với các tệp mã nguồn và kịch bản kiểm thử trong dự án Smart Casual.


**Bảng 4.1: Bảng đối chiếu yêu cầu kỹ thuật đồ án với hiện thực hệ thống Smart Casual**


| STT | Mã YC | Yêu cầu theo Đề cương | Hiện thực trong mã nguồn | Kịch bản kiểm thử (Feature Test) | Trạng thái |
| --- | --- | --- | --- | --- | --- |
| 1 | REQ-3.1-01 | Laravel Framework MVC chuẩn | app/Http/Controllers, app/Models, routes/web.php | Khởi chạy máy chủ, 23 bài test tự động | Hoàn thành |
| 2 | REQ-3.1-02 | CSDL MySQL, Eloquent, Migration | 4 tệp Migration, 13 Eloquent Models | CatalogAndProductTest (quan hệ Category-Product) | Hoàn thành |
| 3 | REQ-3.1-03 | Seeder nạp dữ liệu mẫu | ShopQuanAoSeeder.php nạp 841 bản ghi | Chạy thành công php artisan db:seed | Hoàn thành |
| 4 | REQ-3.1-04 | Blade + Bootstrap, tách User/Admin | layouts/app.blade.php, layouts/admin.blade.php | home page loads, admin login screen loads | Hoàn thành |
| 5 | REQ-3.2-01 | Tối thiểu 2 vai trò: Admin & Customer | Guard web (KHACH_HANG), guard admin (NHAN_VIEN) | AuthenticationAndRbacTest (đăng nhập 2 guard) | Hoàn thành |
| 6 | REQ-3.2-02 | Phân quyền bảo vệ route bằng Middleware | AdminMiddleware, ManagerOnlyMiddleware | admin guests redirected, manager only revenue | Hoàn thành |
| 7 | REQ-3.3-01 | CRUD đầy đủ trên bảng quan hệ | CRUD SAN_PHAM (1-N CHI_TIET_SP), DANH_MUC (1-N SP) | admin can create product, admin can update category | Hoàn thành |
| 8 | REQ-3.3-02 | Đăng nhập, đăng xuất theo vai trò | CustomerAuthController, AdminAuthController | customer login/logout, admin login/logout | Hoàn thành |
| 9 | REQ-3.3-03 | Tìm kiếm + phân trang giữ từ khóa | CatalogController::index, phân trang 16 sp/trang | search products, pagination & query retention | Hoàn thành |
| 10 | REQ-3.3-04 | Validation form có báo lỗi rõ ràng | Validate trong Checkout, Auth, ProductController | validation on invalid checkout, validation product | Hoàn thành |
| 11 | REQ-3.3-05 | Flash message sau thêm/sửa/xóa | session('success'), session('error') trên layouts | Kiểm tra session flash trong test order placement | Hoàn thành |
| 12 | REQ-5.0-01 | Upload ảnh qua Laravel Storage | Storage::disk('public'), symbolic link storage | Đăng tải và hiển thị ảnh trong AdminProductController | Hoàn thành |
| 13 | REQ-5.0-02 | Responsive đa thiết bị (Mobile) | Bootstrap 5 Grid System, Navbar Hamburger | Kiểm tra giao diện qua Viewport mô phỏng di động | Hoàn thành |
| 14 | REQ-5.0-03 | Khóa dòng bi quan lockForUpdate | lockForUpdate() trong DB::transaction tại Checkout | order placement decrements stock atomically | Hoàn thành |
| 15 | REQ-5.0-04 | Tự động hóa bằng Database Triggers | 4 Triggers MySQL tính ThanhTien, TongTien, TongSL | trigger update recalculates old and new orders | Hoàn thành |



## 4.4. Phương pháp kiểm thử tự động (Feature Tests)


Kiểm thử tự động được thiết kế trên môi trường CSDL độc lập smart_casual_test cấu hình trong phpunit.xml. Bộ kiểm thử áp dụng phương pháp giả lập người dùng thông qua các phương thức actingAs($user, 'web') và actingAs($admin, 'admin').


Về phạm vi kiểm thử: Toàn bộ các bài test hiện được thực thi tuần tự trên một luồng xử lý đơn. Bộ test xác thực được tính đúng đắn của logic trừ kho và giao dịch nhưng chưa mô phỏng môi trường tải cao với hàng trăm kết nối đồng thời thực tế. Trong tổng số 23 bài test được ghi nhận, có 1 bài test mặc định của framework (Tests\Unit\ExampleTest), do đó thực chất hệ thống có 22 bài test Feature chuyên sâu.


## 4.5. Kết quả kiểm thử tự động


Hình ảnh chụp màn hình console khi thực thi lệnh php artisan test được minh họa tại Hình 4.14, ghi nhận toàn bộ 23 kịch bản kiểm thử đều đạt kết quả PASS hoàn hảo.


| [HÌNH 4.14: MÀN HÌNH KẾT QUẢ THỰC THI 23 KỊCH BẢN KIỂM THỬ TỰ ĐỘNG QUA LỆNH PHP ARTISAN TEST]<br>• Lệnh: php artisan test<br>• Hiển thị: 23 passed (118 assertions) trong thời gian 1.42s. |
| --- |



*Hình 4.14: Màn hình kết quả thực thi 23 kịch bản kiểm thử tự động qua lệnh php artisan test*


Trích xuất nguyên văn nhật ký thực thi 23 kịch bản kiểm thử tự động:


| PASS  Tests\Unit\ExampleTest<br>  ✓ that true is true                                                                         0.03s<br><br>   PASS  Tests\Feature\AuthenticationAndRbacTest<br>  ✓ customer registration success and redirection to home                                      0.09s<br>  ✓ customer registration validation fails with invalid data                                   0.05s<br>  ✓ customer login success and logout                                                          0.06s<br>  ✓ customer login fails with invalid credentials                                              0.05s<br>  ✓ admin login screen loads                                                                   0.03s<br>  ✓ admin login success and redirection to dashboard                                           0.05s<br>  ✓ admin guests are redirected to admin login                                                 0.04s<br>  ✓ manager only revenue route is forbidden for staff                                          0.05s<br><br>   PASS  Tests\Feature\CartAndCheckoutTest<br>  ✓ guest can add item to session cart and view cart                                           0.05s<br>  ✓ authenticated customer cart persists in database                                           0.06s<br>  ✓ cart quantity can be updated and subtotal recalculated                                     0.05s<br>  ✓ order placement decrements variant stock atomically                                        0.08s<br>  ✓ order placement with insufficient stock throws exception                                   0.06s<br>  ✓ checkout idempotency prevents duplicate orders on re-submission                            0.07s<br>  ✓ trigger update order items recalculates both old and new orders                           0.05s<br><br>   PASS  Tests\Feature\CatalogAndProductTest<br>  ✓ database relationships between category and product                                       0.03s<br>  ✓ storefront home page loads with products                                                  0.05s<br>  ✓ search products by keyword                                                                0.04s<br>  ✓ filter products by category                                                               0.06s<br>  ✓ pagination and query string retention                                                     0.06s<br>  ✓ product detail page loads                                                                 0.04s<br>  ✓ submit product review                                                                     0.04s<br><br>  Tests:    23 passed (118 assertions)<br>  Duration: 1.42s |
| --- |



Kết quả kiểm thử khẳng định các luồng nghiệp vụ cốt lõi đã được kiểm tra và vận hành chính xác theo đúng phạm vi kịch bản được thiết kế.


# CHƯƠNG 5. ĐÁNH GIÁ NHÓM, HẠN CHẾ VÀ HƯỚNG PHÁT TRIỂN


Chương này tổng kết quá trình làm việc nhóm qua Bảng phân công chi tiết, Bảng tỷ lệ đóng góp, minh chứng tương tác GitHub, đồng thời chỉ ra 4 hạn chế kỹ thuật và đề ra lộ trình 4 giai đoạn nâng cấp hệ thống.


## 5.1. Bảng phân công nhiệm vụ chi tiết


Bảng phân công nhiệm vụ chi tiết của các thành viên được thể hiện tại Bảng 5.1.


**Bảng 5.1: Bảng phân công nhiệm vụ chi tiết của các thành viên trong nhóm**


| STT | Họ và tên | MSSV - Lớp | Phân hệ phụ trách chi tiết | Tệp mã nguồn & Hiện vật đảm nhiệm | Mức độ hoàn thành |
| --- | --- | --- | --- | --- | --- |
| 1 | Nguyễn Huy Hoàng<br>(Trưởng nhóm) | 2001230266<br>14DHTH02 | 1. Khởi tạo dự án & kiến trúc lõi.<br>2. CSDL 13 bảng & 4 Triggers, CHECKOUT_IDEMPOTENCY.<br>3. Service Layer (CheckoutService, CartService).<br>4. Multi-Auth Guards & Middleware.<br>5. Admin Controllers (Product, Order, Revenue).<br>6. Toàn bộ 23 bài test PHPUnit. | • database/migrations/*<br>• app/Models/*<br>• app/Services/*<br>• app/Http/Middleware/*<br>• app/Http/Controllers/Admin/*<br>• tests/Feature/*, tests/Unit/* | 100% |
| 2 | Nguyễn Việt Vương<br>(Thành viên) | 2001231062<br>14DHTH02 | 1. Hệ thống Blade Master Layouts.<br>2. Giao diện Phân hệ Khách hàng (Catalog, Detail, Cart, Checkout, Order).<br>3. Giao diện Phân hệ Quản trị (Dashboard, Products, Orders, Categories, Revenue).<br>4. Tối ưu Responsive Bootstrap 5.<br>5. Storefront Controllers (Catalog, Cart, Checkout, Order).<br>6. JavaScript tương tác client. | • resources/views/layouts/*<br>• resources/views/catalog/*<br>• resources/views/orders/*<br>• resources/views/cart/*<br>• resources/views/checkout/*<br>• resources/views/admin/*<br>• app/Http/Controllers/CatalogController.php...<br>• public/assets/css/*, js/* | 100% |
| 3 | Nguyễn Ngọc Tường Vi<br>(Thành viên) | 2001231032<br>14DHTH12 | 1. Chuẩn hóa dữ liệu & nạp Seeder 841 bản ghi.<br>2. Chức năng Đăng ký, Đăng nhập khách hàng.<br>3. Mô-đun Đánh giá sản phẩm (submitReview và AdminReviewController).<br>4. Soạn thảo README.md, xuất file dump SQL, hoàn thiện Báo cáo Word và Slide thuyết trình. | • database/seeders/ShopQuanAoSeeder.php<br>• database/smart_casual_shop_dump.sql<br>• app/Http/Controllers/Auth/CustomerAuthController.php<br>• app/Http/Controllers/Admin/AdminReviewController.php<br>• resources/views/auth/*, reviews/*<br>• README.md, Slide & Báo cáo | 100% |



## 5.2. Tỷ lệ hoàn thành công việc và Minh chứng làm việc nhóm


Bảng đánh giá tỷ lệ đóng góp của từng thành viên trong nhóm được thể hiện tại Bảng 5.2.


**Bảng 5.2: Bảng đánh giá tỷ lệ đóng góp của từng thành viên trong nhóm**


| STT | Họ và tên | MSSV - Lớp | Trách nhiệm chính | Mức độ hoàn thành | Tỷ lệ đóng góp | Chữ ký xác nhận |
| --- | --- | --- | --- | --- | --- | --- |
| 1 | Nguyễn Huy Hoàng | 2001230266<br>14DHTH02 | Kiến trúc hệ thống, Backend, CSDL 13 bảng, Triggers, Multi-Auth, 23 Tests | 100% | 40% |  |
| 2 | Nguyễn Việt Vương | 2001231062<br>14DHTH02 | Toàn bộ giao diện Blade, Tương thích di động, JS client, Storefront Controllers | 100% | 30% |  |
| 3 | Nguyễn Ngọc Tường Vi | 2001231032<br>14DHTH12 | Dữ liệu Seeder 841 bản ghi, Module Đánh giá, Xác thực, Dump SQL & Báo cáo | 100% | 30% |  |
| TỔNG | Cả nhóm (3 SV) | — | Hoàn thành toàn diện 5 chương báo cáo và sản phẩm mã nguồn | 100% | 100% | — |



## 5.2.1. Minh chứng làm việc nhóm và Quản lý mã nguồn qua GitHub


Kho lưu trữ mã nguồn chính thức của nhóm tại địa chỉ: https://github.com/VuongNguyen1632005/PHP_NOSQL.git. Nhóm áp dụng chính sách phân nhánh nghiêm ngặt: nhánh main chứa bản phát hành ổn định, các thành viên tạo nhánh tính năng (feature/*) để phát triển độc lập, thực hiện rebase và chạy 23 kịch bản kiểm thử cục bộ trước khi hợp nhất Pull Request. Minh chứng lịch sử đóng góp commit trên GitHub được thể hiện tại Hình 5.2.


| [HÌNH 5.2: BIỂU ĐỒ THỐNG KÊ LỊCH SỬ ĐÓNG GÓP (CONTRIBUTORS GRAPH) CỦA NHÓM TRÊN GITHUB]<br>• Đường dẫn: https://github.com/VuongNguyen1632005/PHP_NOSQL/graphs/contributors<br>• Thao tác: Vào mục Insights -> chọn Contributors.<br>• Hiển thị: Lịch sử commit theo thời gian thể hiện sự tham gia thường xuyên của các thành viên: Nguyen Huy Hoang, VuongNguyen163... |
| --- |



*Hình 5.2: Biểu đồ thống kê lịch sử đóng góp (Contributors Graph) của nhóm trên GitHub*


## 5.3. Các hạn chế hiện tại


Nhóm thẳng thắn nhìn nhận 4 hạn chế kỹ thuật hiện tại: (1) Chưa kết nối cổng thanh toán trực tuyến tự động (VNPay/MoMo Webhook IPN); (2) Chưa có dịch vụ gửi email thông báo tự động qua hàng đợi nền (Laravel Mail SMTP Queue); (3) Chưa tích hợp tầng bộ nhớ đệm phân tán Redis Caching cho Catalog; (4) Bộ kiểm thử tự động hiện thực thi tuần tự, chưa kiểm thử tải cao đồng thời và chưa có test case tự động cho luồng hủy đơn hoàn kho, voucher và chuyển trạng thái.


## 5.4. Kế hoạch nâng cấp và phát triển mở rộng


Lộ trình 4 giai đoạn nâng cấp hệ thống được thể hiện tại Hình 5.1:


| [HÌNH 5.1: LỘ TRÌNH 4 GIAI ĐOẠN NÂNG CẤP VÀ MỞ RỘNG HỆ THỐNG SMART CASUAL]<br>• Giai đoạn 1: Tích hợp cổng thanh toán trực tuyến VNPay Sandbox & Webhook IPN.<br>• Giai đoạn 2: Tích hợp Laravel Mail SMTP và hàng đợi Database Queue gửi email kèm hóa đơn PDF.<br>• Giai đoạn 3: Tối ưu hiệu năng Catalog với bộ nhớ đệm Redis Cache.<br>• Giai đoạn 4: Xây dựng RESTful API chuẩn mực (/api/v1/*) xác thực Laravel Sanctum cho ứng dụng di động Flutter. |
| --- |



*Hình 5.1: Lộ trình 4 giai đoạn nâng cấp và mở rộng hệ thống Smart Casual*


# KẾT LUẬN


Đồ án 'Xây dựng website bán hàng thời trang Smart Casual bằng Laravel Framework và cơ sở dữ liệu MySQL' đã hoàn thành các mục tiêu đề ra trong học phần Lập trình mã nguồn mở tại Trường Đại học Công Thương TP. Hồ Chí Minh [1]. Hệ thống được xây dựng trên nền tảng Laravel 11.x kết hợp cơ sở dữ liệu MySQL 8.4, đáp ứng tốt chu trình bán lẻ thời trang cốt lõi từ quản lý danh mục, biến thể kích cỡ sản phẩm, giỏ hàng kép, quy trình đặt hàng nguyên tử đến bảng điều khiển quản trị phân quyền cho nhân viên và quản lý cửa hàng. Mặc dù hệ thống vẫn còn một số giới hạn thực tế như chưa kết nối cổng thanh toán trực tuyến tự động và hệ thống gửi email xác nhận đã được nhóm phân tích thẳng thắn tại mục Hạn chế, dự án đã chứng minh được tính khả thi và khả năng vận hành ổn định trong phạm vi yêu cầu học phần.


Trong quá trình thực hiện đồ án, nhóm đã giải quyết được các thách thức kỹ thuật phức tạp: xử lý tranh chấp tồn kho bằng khóa dòng bi quan lockForUpdate() kết hợp Database Transaction tại lớp dịch vụ CheckoutService; xử lý khóa chính phức hợp hai cột của bảng GIO_HANG trong Eloquent ORM bằng cách ghi đè phương thức setKeysForSaveQuery; bảo vệ tính toàn vẹn tài chính bằng 4 Database Triggers; và xây dựng trọn bộ 23 kịch bản kiểm thử tự động với PHPUnit đạt kết quả 100% PASS.


Thông qua đồ án này, các thành viên trong nhóm đã làm chủ quy trình phát triển phần mềm chuẩn mực theo mô hình MVC kết hợp Service Layer, nắm vững kỹ thuật phân quyền Multi-Auth Guards và Middleware tùy chỉnh, đồng thời rèn luyện kỹ năng làm việc nhóm hiệu quả qua Git/GitHub. Đây là hành trang quý báu giúp các thành viên tự tin tiếp cận các dự án phần mềm thực tế trong tương lai.


# TÀI LIỆU THAM KHẢO


[1] Khoa Công nghệ Thông tin — Trường Đại học Công Thương TP. HCM, Đề cương chi tiết học phần Lập trình mã nguồn mở, Bộ môn Kỹ thuật Phần mềm, 2026.


[2] T. Otwell, Laravel Documentation: The PHP Framework for Web Artisans (Version 11.x), Laravel LLC, 2026. [Trực tuyến]. Khả dụng: https://laravel.com/docs/11.x


[3] The PHP Group, PHP 8.3 Manual and Language Reference, 2026. [Trực tuyến]. Khả dụng: https://www.php.net/manual/en/


[4] Oracle Corporation, MySQL 8.4 Reference Manual, Oracle Corporation, 2026. [Trực tuyến]. Khả dụng: https://dev.mysql.com/doc/refman/8.4/en/


[5] M. Otto và J. Thornton, Bootstrap 5.2 Documentation: The most popular HTML, CSS, and JS library in the world, Bootstrap Core Team, 2026. [Trực tuyến]. Khả dụng: https://getbootstrap.com/docs/5.2/


[6] S. Bergmann, PHPUnit 10 Manual: The PHP Testing Framework, 2026. [Trực tuyến]. Khả dụng: https://phpunit.de/documentation.html
