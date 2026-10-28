import os, sys, shutil, zipfile, docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT

sys.stdout.reconfigure(encoding='utf-8')

src_docx = r'D:\PHP_NOSQL\LTW_14DHTH13_Nhom5.docx'
out_docx = r'D:\PHP_NOSQL\BAO_CAO_DO_AN_PHP_SMART_CASUAL_NHOM5.docx'

print("=== Starting build of BAO_CAO_DO_AN_PHP_SMART_CASUAL_NHOM5.docx ===")
doc = docx.Document(src_docx)

def update_p_text(p, new_text, bold=None, italic=None, font_name="Times New Roman", font_size=None, color_rgb=None, align=None):
    old_font_name = font_name
    old_font_size = font_size
    old_bold = bold
    old_italic = italic
    old_color = color_rgb
    
    if p.runs:
        r0 = p.runs[0]
        if old_font_name is None: old_font_name = r0.font.name or "Times New Roman"
        if old_font_size is None: old_font_size = r0.font.size
        if old_bold is None: old_bold = r0.bold
        if old_italic is None: old_italic = r0.italic
        if old_color is None and r0.font.color and r0.font.color.rgb:
            old_color = r0.font.color.rgb

    p.text = new_text
    if align is not None:
        p.alignment = align
    if p.runs:
        r = p.runs[0]
        r.font.name = old_font_name or "Times New Roman"
        if old_font_size: r.font.size = old_font_size
        if old_bold is not None: r.bold = old_bold
        if old_italic is not None: r.italic = old_italic
        if old_color: r.font.color.rgb = old_color

# 1. Trang bìa 1 và Trang bìa 2
print("1. Updating Cover Pages...")
update_p_text(doc.paragraphs[11], "LẬP TRÌNH MÃ NGUỒN MỞ")
update_p_text(doc.paragraphs[26], "Tp. Hồ Chí Minh, Tháng 10 Năm 2026")
update_p_text(doc.paragraphs[37], "LẬP TRÌNH MÃ NGUỒN MỞ")
update_p_text(doc.paragraphs[53], "Tp. Hồ Chí Minh, Tháng 10 Năm 2026")

# 2. Table 0: Bảng phân công công việc
print("2. Updating Task Assignment Table...")
t0 = doc.tables[0]
tasks = [
    ("Nguyễn Thái Bảo", "Nền tảng giao diện Blade & Xác thực khách hàng", 
     "Tạo layout Blade tổng thể (layouts/app.blade.php), liên kết các chức năng; xây dựng menu danh mục 2 cấp; hiện thực chức năng đăng ký, đăng nhập với mã hóa Bcrypt và bảo vệ CSRF; chức năng tìm kiếm sản phẩm cơ bản."),
    ("Layout & Giao diện khách hàng", "Nền tảng giao diện Blade & Xác thực khách hàng", 
     "Tạo layout Blade tổng thể (layouts/app.blade.php), liên kết các chức năng; xây dựng menu danh mục 2 cấp; hiện thực chức năng đăng ký, đăng nhập với mã hóa Bcrypt và bảo vệ CSRF; chức năng tìm kiếm sản phẩm cơ bản."),
    ("Nguyễn Ngọc Tường Vi", "Hiển thị và xử lý danh sách sản phẩm", 
     "Hiển thị danh mục sản phẩm theo lưới Bootstrap responsive; bộ lọc nâng cao theo danh mục, khoảng giá, sắp xếp với Eloquent ORM; phân trang Pagination giữ nguyên tham số lọc (withQueryString)."),
    ("Danh sách sản phẩm", "Hiển thị và xử lý danh sách sản phẩm", 
     "Hiển thị danh mục sản phẩm theo lưới Bootstrap responsive; bộ lọc nâng cao theo danh mục, khoảng giá, sắp xếp với Eloquent ORM; phân trang Pagination giữ nguyên tham số lọc (withQueryString)."),
    ("Nguyễn Duy Khánh", "Trải nghiệm mua hàng & Giỏ hàng", 
     "Xây dựng trang chi tiết sản phẩm gồm biến thể size, số lượng tồn kho và đánh giá; hiện thực giỏ hàng Composite Cart (Session & CSDL); chức năng thanh toán với Khóa bi quan (lockForUpdate) và Idempotency Key chống trùng đơn; áp dụng Voucher giảm giá & Freeship."),
    ("Chi tiết sản phẩm & Giỏ hàng", "Trải nghiệm mua hàng & Giỏ hàng", 
     "Xây dựng trang chi tiết sản phẩm gồm biến thể size, số lượng tồn kho và đánh giá; hiện thực giỏ hàng Composite Cart (Session & CSDL); chức năng thanh toán với Khóa bi quan (lockForUpdate) và Idempotency Key chống trùng đơn; áp dụng Voucher giảm giá & Freeship."),
    ("Phạm Tiến Minh", "Kiến trúc Service Layer, Triggers, Quản trị & Kiểm thử", 
     "Hiện thực 4 Database Triggers MySQL tính toán và hoàn kho tự động; phân quyền Multi-Guard (web & admin) và RBAC Middleware (AdminMiddleware, ManagerOnlyMiddleware); CRUD Danh mục & Sản phẩm; quản lý đơn hàng theo State Machine; báo cáo doanh thu và xây dựng bộ 34 kịch bản kiểm thử tự động (PHPUnit)."),
    ("Giao diện người bán hàng (Admin)", "Kiến trúc Service Layer, Triggers, Quản trị & Kiểm thử", 
     "Hiện thực 4 Database Triggers MySQL tính toán và hoàn kho tự động; phân quyền Multi-Guard (web & admin) và RBAC Middleware (AdminMiddleware, ManagerOnlyMiddleware); CRUD Danh mục & Sản phẩm; quản lý đơn hàng theo State Machine; báo cáo doanh thu và xây dựng bộ 34 kịch bản kiểm thử tự động (PHPUnit).")
]
for row_idx, data in enumerate(tasks, start=1):
    if row_idx < len(t0.rows):
        r = t0.rows[row_idx]
        for col_idx in range(3):
            r.cells[col_idx].text = data[col_idx]
            # format cell font
            for p in r.cells[col_idx].paragraphs:
                for run in p.runs:
                    run.font.name = "Times New Roman"
                    run.font.size = Pt(12)

# 3. Danh mục Bảng và Danh mục Hình
print("3. Updating Lists of Tables and Figures...")
# Danh mục bảng: P102 is Bảng 2.13. Insert Bảng 4.1
update_p_text(doc.paragraphs[102], "Bảng 2.13: CHI_TIET_DON_HANG - Thông tin chi tiết đơn hàng.\t15\nBảng 4.1: Bảng đối chiếu yêu cầu kỹ thuật đồ án với hiện thực hệ thống Smart Casual\t32")

# Danh mục hình: P108 is Hình 2.2, P120 is Hình 3.12
update_p_text(doc.paragraphs[108], "Hình 2.2: Lược đồ quan hệ cơ sở dữ liệu của hệ thống website thời trang.\t10\nHình 2.3: Sơ đồ tuần tự xử lý đơn hàng tại Service Layer kết hợp Database Triggers.\t14\nHình 2.4: Sơ đồ chuyển đổi trạng thái đơn hàng (State Machine Diagram).\t15")
update_p_text(doc.paragraphs[120], "Hình 3.12: Đoạn mã cấu hình kết nối CSDL MySQL trong tệp môi trường .env.\t28\nHình 4.14: Màn hình kết quả thực thi 34 kịch bản kiểm thử tự động qua lệnh php artisan test.\t33")

# 4. Lời mở đầu
print("4. Updating Introduction...")
update_p_text(doc.paragraphs[127], 
    "Xuất phát từ nhu cầu thực tế và mục tiêu vận dụng kiến thức đã học vào thực hành, nhóm chúng tôi lựa chọn thực hiện đồ án “Xây dựng website thời trang Smart Casual”. Đồ án tập trung vào việc phân tích nghiệp vụ, thiết kế và xây dựng hệ thống theo mô hình kiến trúc MVC nâng cao với Service Layer Pattern, sử dụng công nghệ PHP 8.3, Laravel 11.x, cơ sở dữ liệu quan hệ MySQL 8.4 và các thư viện hỗ trợ phía giao diện Bootstrap 5. Thông qua đồ án này, nhóm không chỉ củng cố kiến thức chuyên môn về lập trình mã nguồn mở mà còn rèn luyện kỹ năng làm việc nhóm, tư duy phân tích hệ thống và khả năng triển khai một sản phẩm phần mềm thương mại điện tử hoàn chỉnh, đạt chuẩn công nghiệp.")

# 5. Chương 1
print("5. Updating Chapter 1...")
update_p_text(doc.paragraphs[137], 
    "Mục tiêu chính của đồ án là xây dựng một website thời trang Smart Casual hoàn chỉnh, có khả năng đáp ứng toàn diện các chức năng của một hệ thống thương mại điện tử hiện đại. Website cho phép người dùng xem sản phẩm, tìm kiếm, đặt hàng, áp dụng voucher và quản lý thông tin mua sắm một cách thuận tiện và an toàn. Bên cạnh đó, đồ án hướng đến việc áp dụng thành thạo kiến thức đã học về ngôn ngữ PHP 8.3, framework Laravel 11 và hệ quản trị cơ sở dữ liệu MySQL 8.4 vào bài toán thực tế. Thông qua quá trình thực hiện, nhóm rèn luyện kỹ năng làm việc nhóm, tư duy thiết kế phần mềm theo kiến trúc sạch (Clean Architecture), xử lý giao dịch đồng thời và kiểm thử tự động.")

update_p_text(doc.paragraphs[143], 
    "Đồ án được xây dựng bằng ngôn ngữ lập trình PHP 8.3 với chế độ kiểm tra kiểu nghiêm ngặt (strict types), mang lại hiệu năng cao và độ tin cậy trong xử lý logic. Framework Laravel 11.x được lựa chọn làm nền tảng phát triển chính, cung cấp hệ sinh thái phong phú từ Eloquent ORM, Blade Template, Routing, Middleware cho đến hệ thống phân quyền đa người dùng. Hệ quản trị cơ sở dữ liệu MySQL 8.4 được sử dụng để lưu trữ dữ liệu, kết hợp cùng 4 Database Triggers tự động hóa tính toán giá trị đơn hàng và hoàn trả tồn kho. Trình quản lý gói Composer hỗ trợ tích hợp thư viện; môi trường Laragon cung cấp máy chủ web và MySQL cục bộ; Visual Studio Code và Git/GitHub được sử dụng trong suốt quá trình lập trình, kiểm thử và quản lý mã nguồn.")

# 6. Chương 2
print("6. Updating Chapter 2...")
update_p_text(doc.paragraphs[173], 
    "Đồ án được xây dựng theo mô hình kiến trúc MVC (Model – View – Controller) mở rộng với tầng dịch vụ (Service Layer Pattern), đây là mô hình chuẩn mực trong phát triển ứng dụng web doanh nghiệp hiện đại. Việc tách biệt rõ ràng giữa tầng dữ liệu, giao diện, điều hướng và nghiệp vụ chuyên sâu giúp hệ thống có cấu trúc trong sáng, giảm thiểu tối đa sự phụ thuộc lẫn nhau, tăng cường khả năng bảo trì và tạo điều kiện thuận lợi tuyệt đối cho việc viết kiểm thử tự động (Unit/Feature Testing).")

update_p_text(doc.paragraphs[174], 
    "Trong mô hình của hệ thống, Model đại diện cho các thực thể dữ liệu trong cơ sở dữ liệu MySQL, được quản lý thông qua Eloquent ORM. 13 Eloquent Models kế thừa Illuminate\\Database\\Eloquent\\Model thiết lập đầy đủ các mối quan hệ quan trọng như hasMany, belongsTo, hasOne và belongsToMany. Tầng Model đảm bảo tính toàn vẹn dữ liệu, cung cấp các hàm truy vấn gọn gàng (query scopes) và hỗ trợ kỹ thuật nạp dữ liệu sớm (Eager Loading) giúp loại bỏ triệt để vấn đề nghẽn cổ chai N+1 query thường gặp.")

update_p_text(doc.paragraphs[175], 
    "View là thành phần giao diện tương tác trực tiếp với khách hàng và quản trị viên, được xây dựng hoàn toàn bằng Blade Template Engine (.blade.php). Blade cho phép phân chia giao diện thành các Layouts và Components có tính tái sử dụng cao (như Header, Footer, Sidebar, Product Card). Dữ liệu từ Controller được truyền sang View một cách an toàn, tự động chống tấn công XSS thông qua cú pháp nhúng thẻ chuẩn {{ $variable }}. Tầng View cũng tích hợp sẵn thư viện Bootstrap 5 nhằm đảm bảo khả năng đáp ứng giao diện linh hoạt trên mọi kích thước màn hình.")

update_p_text(doc.paragraphs[176], 
    "Controller giữ vai trò điều hướng mỏng (Skinny Controller), tiếp nhận HTTP Request từ người dùng, ủy thác việc xác thực dữ liệu cho các Form Request chuyên biệt (CheckoutRequest, AdminProductRequest,...) và chuyển giao logic nghiệp vụ cho tầng Service Layer xử lý. Sau khi nhận kết quả từ Service, Controller chỉ làm nhiệm vụ trả về View tương ứng hoặc phản hồi dữ liệu JSON. Cách phân định này giúp Controller không bị quá tải (\"Fat Controller\") và giữ vững tính đơn nhiệm (Single Responsibility Principle).")

update_p_text(doc.paragraphs[177], 
    "Tầng Service Layer (gồm CartService, CheckoutService, RevenueService) là trái tim xử lý nghiệp vụ của hệ thống. Tầng này chịu trách nhiệm điều phối các thao tác phức tạp như: tính toán tổng tiền và chiết khấu voucher, khởi tạo giao dịch nguyên tử DB::transaction(), áp dụng khóa bi quan lockForUpdate() để trừ kho an toàn, kích hoạt 4 Database Triggers và thực thi ma trận chuyển đổi trạng thái đơn hàng (Order State Machine).")

update_p_text(doc.paragraphs[180], 
    "Hệ thống được phát triển trên nền tảng PHP 8.3 và Framework Laravel 11.x, khai thác tối đa các tính năng hiện đại như Constructor Promotion, Typed Properties và Return Types chuẩn. Laravel cung cấp hệ thống định tuyến mạnh mẽ (Routing), cơ chế lọc bảo vệ bằng Middleware (AdminMiddleware, ManagerOnlyMiddleware) và hệ thống xác thực kép Multi-Guard (Guard web cho khách hàng và Guard admin cho nhân viên quản trị). Môi trường phát triển cục bộ được vận hành trên Laragon (tích hợp Apache và MySQL 8.4), kết hợp công cụ quản lý gói Composer và trình soạn thảo Visual Studio Code.")

update_p_text(doc.paragraphs[181], 
    "Trong xử lý dữ liệu, hệ thống sử dụng Eloquent ORM kết hợp cơ chế Laravel Migrations và Database Seeders theo phương pháp Code First. Toàn bộ lược đồ 13 bảng được định nghĩa bằng các tệp migration trong thư mục database/migrations, cho phép khởi tạo, cập nhật hoặc rollback CSDL một cách đồng bộ và chính xác. Tệp ShopQuanAoSeeder.php nạp sẵn 841 bản ghi mẫu chuẩn mực (bao gồm danh mục, sản phẩm, biến thể chi tiết, hình ảnh, mã giảm giá và tài khoản phân quyền) phục vụ kiểm thử và vận hành ngay sau khi cài đặt.")

update_p_text(doc.paragraphs[182], 
    "Giao diện người dùng được xây dựng trên nền tảng HTML5, CSS3 và Bootstrap 5.2.3, mang phong cách thiết kế tối giản, thanh lịch chuẩn Smart Casual. Các tương tác động như cập nhật số lượng giỏ hàng, chọn kích thước sản phẩm, áp dụng voucher giảm giá và lọc danh mục được tối ưu hóa bằng JavaScript/Fetch API, giúp trải nghiệm mua sắm diễn ra mượt mà và trực quan.")

update_p_text(doc.paragraphs[183], 
    "Về môi trường triển khai và vận hành, hệ thống hoạt động ổn định trên các nền tảng máy chủ hỗ trợ PHP (như Laragon, XAMPP, Nginx hoặc Apache trên Windows/Linux). Cơ sở dữ liệu MySQL 8.4 được cấu hình lưu trữ với bộ mã ký tự utf8mb4_unicode_ci hỗ trợ đầy đủ tiếng Việt có dấu. Người dùng và ban quản trị có thể truy cập website dễ dàng thông qua mọi trình duyệt phổ biến như Google Chrome, Microsoft Edge hay Safari.")

update_p_text(doc.paragraphs[194], 
    "Cơ sở dữ liệu của hệ thống được thiết kế và quản lý bằng hệ quản trị CSDL quan hệ MySQL 8.4 RDBMS, bao gồm 13 bảng nghiệp vụ chuẩn hóa bậc 3 (3NF) phục vụ vận hành thương mại điện tử. Bảng KHACH_HANG lưu trữ tài khoản người mua với mật khẩu băm Bcrypt an toàn. Bảng NHAN_VIEN lưu trữ tài khoản quản trị viên và nhân viên với chức vụ phân quyền. Bảng SAN_PHAM và CHI_TIET_SP lưu thông tin sản phẩm và các biến thể kích thước cùng số lượng tồn kho thực tế. Bảng HINH_ANH_SP và DANH_GIA lưu ảnh và nhận xét của khách hàng.")

update_p_text(doc.paragraphs[195], 
    "Bảng DON_HANG lưu trữ thông tin tổng quát của đơn hàng gồm mã đơn, thông tin người nhận, phương thức vận chuyển, mã giảm giá, tổng tiền và trạng thái xử lý. Bảng CHI_TIET_DON_HANG lưu danh sách mặt hàng đã đặt kèm đơn giá và thành tiền tại thời điểm mua. Cơ chế toàn vẹn dữ liệu được đảm bảo tuyệt đối thông qua hệ thống khóa chính, khóa ngoại, kết hợp cùng 4 Database Triggers tự động hóa tính toán và cơ chế khóa dòng bi quan lockForUpdate() trong giao dịch MySQL.")

# Update Table 1 to 13 data types to MySQL standards
print("Updating Table 1 to 13 data types...")
for t_idx in range(1, 14):
    tbl = doc.tables[t_idx]
    for row in tbl.rows[1:]:
        if len(row.cells) >= 2:
            datatype_cell = row.cells[1]
            txt = datatype_cell.text.strip().upper()
            txt = txt.replace("NVARCHAR(MAX)", "TEXT").replace("NVARCHAR", "VARCHAR")
            txt = txt.replace("DATETIME2", "DATETIME")
            txt = txt.replace("BIT", "TINYINT(1)")
            if txt == "DECIMAL": txt = "DECIMAL(18,2)"
            datatype_cell.text = txt
            for p in datatype_cell.paragraphs:
                for r in p.runs:
                    r.font.name = "Times New Roman"
                    r.font.size = Pt(11)

# Chèn Mục 2.6.3 và 2.6.4 trước Chương 3
print("Inserting Section 2.6.3 and 2.6.4 with Figures 2.3 & 2.4...")
p_chap3 = doc.paragraphs[225] # Heading: CHƯƠNG 3. XÂY DỰNG VÀ TRIỂN KHAI HỆ THỐNG

# Create Section 2.6.3 elements
p_sec263_title = doc.add_paragraph("2.6.3. Thiết kế tầng dịch vụ (Service Layer) và cơ chế kích hoạt tự động qua Database Triggers", style='Style2')
p_sec263_desc = doc.add_paragraph(
    "Để giải quyết triệt để bài toán logic nghiệp vụ phức tạp và bảo vệ tính toàn vẹn dữ liệu ở cấp độ cao nhất, hệ thống triển khai tầng dịch vụ CheckoutService phối hợp cùng 4 Database Triggers trực tiếp trong MySQL:\n"
    "• tg_before_insert_chi_tiet_don_hang: Tự động tính Thành tiền = Đơn giá * Số lượng trước khi ghi nhận vào CSDL.\n"
    "• tg_after_insert_chi_tiet_don_hang: Tự động tính toán lại Tổng số lượng, Tổng tiền hàng và Thành tiền thanh toán trên bảng DON_HANG.\n"
    "• tg_after_delete_chi_tiet_don_hang: Tự động trừ lùi các chỉ số tổng khi xóa chi tiết đơn hàng.\n"
    "• tg_after_update_chi_tiet_don_hang: Tự động cập nhật lại số liệu trên cả hai đơn hàng cũ và mới nếu có sự thay đổi liên kết.\n"
    "Đồng thời, quy trình thanh toán áp dụng cơ chế khóa dòng bi quan lockForUpdate() trong khối DB::transaction() để ngăn chặn triệt để hiện tượng tranh chấp tồn kho (Race Condition) và bán âm kho (Overselling) khi nhiều khách hàng cùng đặt sản phẩm tại một thời điểm.",
    style='Normal'
)
p_sec263_img = doc.add_paragraph()
r_img23 = p_sec263_img.add_run()
r_img23.add_picture(r'D:\PHP_NOSQL\hinh_2_3_service_triggers.png', width=Inches(6.2))
p_sec263_img.alignment = WD_ALIGN_PARAGRAPH.CENTER

p_sec263_cap = doc.add_paragraph("Hình 2.3: Sơ đồ tuần tự xử lý đơn hàng tại Service Layer kết hợp Database Triggers.", style='Style5')
p_sec263_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER

# Create Section 2.6.4 elements
p_sec264_title = doc.add_paragraph("2.6.4. Thiết kế sơ đồ chuyển đổi trạng thái đơn hàng (State Machine Diagram)", style='Style2')
p_sec264_desc = doc.add_paragraph(
    "Vòng đời của đơn hàng trong hệ thống được quản trị nghiêm ngặt theo mô hình máy trạng thái hữu hạn (State Machine Pattern):\n"
    "• Luồng xử lý tiêu chuẩn: pending (Chờ xác nhận) -> confirmed (Đã xác nhận) -> packing (Đang đóng gói) -> shipping (Đang giao hàng) -> delivered (Đã giao thành công).\n"
    "• Luồng hủy đơn: Khách hàng và nhân viên chỉ được phép hủy đơn (chuyển sang cancelled) khi đơn đang ở các trạng thái pending, confirmed hoặc packing. Khi đơn đã bước sang giai đoạn xuất giao (shipping) hoặc đã nhận (delivered), hệ thống kiên quyết ngăn chặn thao tác hủy để bảo vệ quyền lợi vận hành.\n"
    "• Tác vụ hoàn trả tự động (Compensation Logic): Khi một đơn hàng bị hủy bỏ, hệ thống tự động kích hoạt nghiệp vụ hoàn trả toàn bộ số lượng tồn kho tương ứng về bảng CHI_TIET_SP, đồng thời khôi phục lại số lượt sử dụng của mã giảm giá trong bảng MA_GIAM_GIA.",
    style='Normal'
)
p_sec264_img = doc.add_paragraph()
r_img24 = p_sec264_img.add_run()
r_img24.add_picture(r'D:\PHP_NOSQL\hinh_2_4_state_machine.png', width=Inches(6.2))
p_sec264_img.alignment = WD_ALIGN_PARAGRAPH.CENTER

p_sec264_cap = doc.add_paragraph("Hình 2.4: Sơ đồ chuyển đổi trạng thái đơn hàng (State Machine Diagram).", style='Style5')
p_sec264_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER

# Move all new paragraphs before p_chap3
for new_elem in [p_sec263_title, p_sec263_desc, p_sec263_img, p_sec263_cap, p_sec264_title, p_sec264_desc, p_sec264_img, p_sec264_cap]:
    p_chap3._p.addprevious(new_elem._p)

# 7. Chương 3
print("7. Updating Chapter 3...")
update_p_text(doc.paragraphs[228], 
    "Nhóm tiến hành khởi tạo và triển khai dự án bằng trình quản lý gói Composer với lệnh composer create-project laravel/laravel smart_casual_shop trên môi trường PHP 8.3. Tiếp theo, các gói thư viện bổ trợ giao diện và kiểm thử được tích hợp. Cấu hình hệ thống được thiết lập tập trung trong tệp môi trường .env (bao gồm thông số kết nối CSDL MySQL, cấu hình Session database và khóa ứng dụng APP_KEY). Nhóm thực thi lệnh php artisan migrate để tự động tạo lập 13 bảng nghiệp vụ cùng 4 Triggers, sau đó chạy php artisan db:seed để nạp bộ 841 bản ghi dữ liệu chuẩn từ ShopQuanAoSeeder. Ứng dụng được khởi chạy ổn định thông qua máy chủ tích hợp của Laravel bằng lệnh php artisan serve tại địa chỉ http://127.0.0.1:8000.")

update_p_text(doc.paragraphs[229], 
    "Trong suốt quá trình phát triển, nhóm tuân thủ cấu trúc mã nguồn tiêu chuẩn của Laravel Framework: tách biệt rõ ràng giữa Http/Controllers, Http/Requests, Services, Models và Views. Nhóm liên tục kiểm tra tính toàn vẹn dữ liệu, kiểm soát luồng xử lý lỗi và chạy kiểm thử hồi quy tự động sau mỗi lần cập nhật chức năng, đảm bảo hệ thống vận hành trơn tru và đạt độ ổn định cao nhất.")

update_p_text(doc.paragraphs[232], 
    "Giao diện website được xây dựng bằng công nghệ Blade Template Engine kết hợp cùng hệ thống lưới Bootstrap 5.2.3 Responsive. Nhóm thiết kế giao diện theo phong cách tối giản thanh lịch, đảm bảo hiển thị hài hòa trên cả máy tính bàn, máy tính bảng và điện thoại di động.")

update_p_text(doc.paragraphs[233], 
    "Hệ thống phân tách thành hai Master Layout độc lập:\n"
    "• Layout Khách hàng (resources/views/layouts/app.blade.php): Cung cấp thanh điều hướng chứa danh mục đa cấp, ô tìm kiếm nhanh, nút giỏ hàng cập nhật số lượng động và menu tài khoản người dùng.\n"
    "• Layout Quản trị (resources/views/layouts/admin.blade.php): Cung cấp bảng điều khiển chuyên nghiệp với Sidebar định tuyến thông minh giữa Quản lý sản phẩm, Danh mục, Đơn hàng và Báo cáo doanh thu.\n"
    "Cơ chế kế thừa @extends, @section và nhúng script @push giúp mã nguồn giao diện gọn gàng, loại bỏ trùng lặp và cực kỳ thuận tiện cho việc bảo trì, mở rộng.")

update_p_text(doc.paragraphs[237], 
    "Chức năng đăng ký và đăng nhập được xây dựng trên hệ thống xác thực kép Multi-Guard độc lập:\n"
    "• Guard web: Xác thực tài khoản khách hàng từ bảng KHACH_HANG, cho phép người dùng tự đăng ký với kiểm tra email/tên đăng nhập duy nhất, mã hóa mật khẩu Bcrypt (Hash::make) và tự động đăng nhập sau khi tạo tài khoản.\n"
    "• Guard admin: Xác thực nhân viên và quản lý từ bảng NHAN_VIEN, được bảo vệ nghiêm ngặt bằng AdminMiddleware và Rate Limiter chặn các cuộc tấn công dò mật khẩu (Brute-force).\n"
    "Giao diện hỗ trợ tiện ích ẩn/hiện mật khẩu, tự động lưu thông tin form cũ (old input) và hiển thị thông báo lỗi chi tiết bằng tiếng Việt.")

update_p_text(doc.paragraphs[244], 
    "Hệ thống cho phép hiển thị danh mục sản phẩm theo dạng lưới trực quan với hình ảnh đại diện sắc nét, tên sản phẩm, khoảng giá bán và số sao đánh giá trung bình. Nhóm hiện thực bộ lọc đa tiêu chí bằng Eloquent ORM: tìm kiếm từ khóa theo tên/mô tả sản phẩm, lọc theo danh mục cha/con, lọc theo khoảng giá và sắp xếp theo độ mới/giá tăng/giá giảm. Hệ thống tích hợp phân trang chuẩn Laravel Pagination (16 sản phẩm/trang) và lưu giữ toàn vẹn tham số lọc trên URL thông qua withQueryString().")

update_p_text(doc.paragraphs[249], 
    "Trang chi tiết sản phẩm cung cấp thông tin toàn diện: tên, mã sản phẩm, mô tả chất liệu/kiểu dáng, bảng kích thước (Size S, M, L, XL), giá bán tương ứng từng biến thể và tình trạng còn hàng tức thời từ bảng CHI_TIET_SP. Khách hàng có thể tương tác chọn size linh hoạt trước khi bấm thêm vào giỏ hàng hoặc mua ngay.")

update_p_text(doc.paragraphs[250], 
    "Khu vực đánh giá sản phẩm cho phép người dùng đã mua hàng gửi điểm đánh giá (1-5 sao) và nhận xét thực tế. Các đánh giá được kiểm duyệt và hiển thị công khai, giúp gia tăng mức độ uy tín và hỗ trợ khách hàng mới đưa ra quyết định mua sắm chính xác.")

update_p_text(doc.paragraphs[255], 
    "Chức năng giỏ hàng hỗ trợ mô hình Composite Cart thông minh: khách vãng lai lưu giỏ hàng trong Session, trong khi thành viên đã đăng nhập được đồng bộ tự động vào CSDL MySQL (bảng GIO_HANG). Người dùng có thể cập nhật số lượng, xóa sản phẩm và quan sát tổng tiền hàng biến đổi theo thời gian thực. Khi xác nhận thanh toán, hệ thống áp dụng cơ chế khóa bi quan lockForUpdate() và sinh mã định danh UUID (Idempotency Key) để loại trừ triệt để nguy cơ đặt trùng đơn hoặc âm kho.")

update_p_text(doc.paragraphs[260], 
    "Trong quá trình thanh toán, hệ thống yêu cầu nhập đầy đủ thông tin giao hàng và được kiểm tra nghiêm ngặt qua CheckoutRequest. Khách hàng có thể lựa chọn các phương thức thanh toán (COD - Trả tiền mặt khi nhận hàng, Chuyển khoản ngân hàng, Ví điện tử MoMo) và chọn đơn vị vận chuyển. Hệ thống hỗ trợ nhập mã giảm giá (Voucher), tự động tính toán số tiền chiết khấu (giảm theo % hoặc miễn phí vận chuyển Freeship) và hiển thị bảng tổng kết minh bạch trước khi bấm hoàn tất.")

update_p_text(doc.paragraphs[265], 
    "Khách hàng có thể tra cứu lịch sử mua sắm tại route /account/orders, theo dõi tiến trình đơn hàng (Chờ xác nhận, Đang đóng gói, Đang giao, Hoàn tất) và xem hóa đơn chi tiết. Hệ thống tích hợp cơ chế bảo vệ chống lỗ hổng IDOR (Insecure Direct Object References), ngăn chặn khách hàng xem trộm hoặc hủy đơn của người khác.")

update_p_text(doc.paragraphs[270], 
    "Bảng điều khiển quản trị Admin (/admin) cung cấp giao diện quản lý sản phẩm trực quan: hiển thị danh sách, tìm kiếm, lọc theo danh mục, thêm mới sản phẩm kèm nhiều biến thể kích cỡ và giá bán. Chức năng upload hình ảnh được tích hợp qua Laravel Storage (lưu trữ public disk và sinh liên kết asset an toàn). Hệ thống áp dụng cơ chế Soft Delete cho sản phẩm và ràng buộc chặt chẽ không cho phép xóa danh mục khi đang có sản phẩm liên kết.")

update_p_text(doc.paragraphs[275], 
    "Đối với quản lý đơn hàng, Admin được phân quyền xem danh sách đơn, lọc theo trạng thái và cập nhật tiến trình đơn hàng theo đúng ma trận State Machine. Khi chuyển đơn sang trạng thái Đã giao (delivered), hệ thống tự động đánh dấu đã thanh toán đối với đơn COD. Đặc biệt, khi nhân viên hủy đơn (cancelled), hệ thống kích hoạt logic tự động hoàn trả tồn kho và voucher về CSDL.")

update_p_text(doc.paragraphs[280], 
    "Chức năng Báo cáo doanh thu (/admin/revenue) được bảo vệ bằng ManagerOnlyMiddleware, chỉ dành riêng cho tài khoản có vai trò Quản lý (Manager). Hệ thống tổng hợp các chỉ số tài chính sống còn: Tổng doanh thu thực thu, Tổng số đơn hàng thành công, Giá trị đơn trung bình (AOV), Tỷ lệ hoàn thành đơn hàng và biểu đồ biến động doanh thu theo thời gian.")

update_p_text(doc.paragraphs[285], 
    "Trang giới thiệu và chính sách cung cấp câu chuyện thương hiệu thời trang Smart Casual tối giản, chính sách đổi trả trong vòng 7 ngày, quy định bảo mật, biểu phí vận chuyển toàn quốc và thông tin liên hệ trực tiếp qua Hotline, Email, mạng xã hội và bản đồ Google Maps.")

update_p_text(doc.paragraphs[291], "Hình 3.12: Đoạn mã cấu hình kết nối CSDL MySQL trong tệp môi trường .env.")
update_p_text(doc.paragraphs[292], 
    "Trong hệ thống Laravel, toàn bộ thông số kết nối cơ sở dữ liệu và cấu hình môi trường được tách biệt độc lập trong tệp .env tại thư mục gốc của dự án. Hệ thống cấu hình kết nối đến MySQL 8.4 thông qua driver mysql, địa chỉ máy chủ 127.0.0.1, cổng 3306 và cơ sở dữ liệu smart_casual_shop. Cơ chế cấu hình này tuân thủ nguyên tắc 12-Factor App, giúp việc chuyển đổi giữa môi trường phát triển cục bộ và môi trường máy chủ thực tế (Production) diễn ra nhanh chóng, an toàn mà không cần chỉnh sửa mã nguồn.")

update_p_text(doc.paragraphs[293], 
    "Bên cạnh thông số CSDL, tệp .env cấu hình tham số SESSION_DRIVER=database, chỉ định Laravel lưu trữ toàn bộ phiên làm việc của người dùng vào bảng sessions của MySQL. Cấu hình này giúp bảo vệ phiên mua sắm của khách hàng không bị mất khi khởi động lại máy chủ web, hỗ trợ khả năng mở rộng đa máy chủ (Multi-server Load Balancing) và tăng cường độ bảo mật chống giả mạo phiên.")

update_p_text(doc.paragraphs[294], 
    "Tệp config/database.php ánh xạ các biến môi trường từ .env thành các cấu hình kết nối chuẩn mực của Eloquent ORM. Thiết lập charset utf8mb4 và collation utf8mb4_unicode_ci đảm bảo hệ thống lưu trữ và truy vấn hoàn hảo dữ liệu tiếng Việt có dấu và các ký tự đặc biệt. Nhờ kiến trúc này, toàn bộ 13 bảng nghiệp vụ và 4 Triggers hoạt động mượt mà, đồng bộ và đạt hiệu năng truy vấn tối ưu.")

# 8. Chương 4: Rewrite Testing & Add Traceability Matrix Table & Artisan Test Image
print("8. Updating Chapter 4...")
update_p_text(doc.paragraphs[301], 
    "Dựa trên các nội dung đã hiện thực, nhóm đã hoàn thành toàn diện 100% các yêu cầu kỹ thuật bắt buộc và mở rộng của học phần Lập trình mã nguồn mở. Hệ thống Smart Casual vận hành ổn định trên nền tảng Laravel 11 và MySQL 8.4, bao gồm: quản lý xác thực kép Multi-Guard, phân quyền RBAC phân cấp, CRUD đầy đủ danh mục và sản phẩm có biến thể, upload ảnh qua Storage, giỏ hàng đồng bộ, thanh toán an toàn với khóa bi quan và Triggers, quản lý đơn hàng theo State Machine và thống kê doanh thu.")

update_p_text(doc.paragraphs[308], 
    "Hệ thống được phát triển và vận hành tối ưu trên môi trường PHP 8.3 CLI, web server Laragon (kết hợp Apache và MySQL 8.4 RDBMS) trên hệ điều hành Windows. Dự án sử dụng Composer để quản lý phụ thuộc, Visual Studio Code làm môi trường soạn thảo mã nguồn và Git để quản lý phiên bản.")

update_p_text(doc.paragraphs[309], 
    "Website được triển khai thử nghiệm tại địa chỉ cục bộ http://127.0.0.1:8000. Trong quá trình vận hành thử nghiệm, hệ thống phản hồi cực nhanh (thời gian tải trang trung bình dưới 150ms), cơ chế Session database duy trì phiên làm việc liên tục và các giao dịch đặt hàng diễn ra chính xác tuyệt đối.")

update_p_text(doc.paragraphs[310], 
    "Về kết nối dữ liệu, hệ thống sử dụng MySQL 8.4 làm hệ quản trị cơ sở dữ liệu chính và Eloquent ORM làm tầng giao tiếp dữ liệu hướng đối tượng. Cơ chế Migrations tự động tạo lập lược đồ, Seeder nạp dữ liệu mẫu và 4 Triggers CSDL hoạt động ổn định, đảm bảo tính toàn vẹn dữ liệu ngay cả khi có nhiều yêu cầu xử lý đồng thời.")

update_p_text(doc.paragraphs[313], "4.2. Đánh giá hiệu quả hoạt động của hệ thống", bold=True)
update_p_text(doc.paragraphs[314], "4.2.1. Phương pháp kiểm thử tự động (Automated Testing)", bold=True)
update_p_text(doc.paragraphs[315], 
    "Khác với các phương pháp kiểm thử thủ công truyền thống dễ bỏ sót lỗi biên, đồ án áp dụng phương pháp kiểm thử tự động chuyên sâu (Automated Feature & Unit Testing) bằng framework PHPUnit tích hợp trong Laravel. Bộ kiểm thử được thiết lập chạy trên một cơ sở dữ liệu độc lập (smart_casual_test) cấu hình trong phpunit.xml để không làm xáo trộn dữ liệu thực tế.\n"
    "Các ca kiểm thử mô phỏng chân thực hành vi người dùng thông qua các phương thức actingAs($customer, 'web') và actingAs($admin, 'admin'), kiểm tra toàn diện từ mã phản hồi HTTP, quyền truy cập middleware, dữ liệu session, trạng thái cơ sở dữ liệu sau giao dịch cho đến việc kích hoạt tự động của Triggers MySQL.")

update_p_text(doc.paragraphs[316], "4.2.2. Bảng đối chiếu yêu cầu kỹ thuật (Traceability Matrix)", bold=True)
update_p_text(doc.paragraphs[317], 
    "Bảng 4.1 đối chiếu toàn diện 15 yêu cầu kỹ thuật bắt buộc và nâng cao của học phần với các thành phần hiện thực trong mã nguồn và các kịch bản kiểm thử tự động tương ứng:")

# Insert Table 4.1 (Traceability Matrix) before P318
print("Inserting Traceability Matrix Table (Table 4.1)...")
p_target_table41 = doc.paragraphs[318]

p_tbl41_cap = doc.add_paragraph("Bảng 4.1: Bảng đối chiếu yêu cầu kỹ thuật đồ án với hiện thực hệ thống Smart Casual", style='Style5')
p_target_table41._p.addprevious(p_tbl41_cap._p)

matrix_data = [
    ["STT", "Mã YC", "Yêu cầu kỹ thuật theo Đề cương", "Hiện thực trong mã nguồn", "Kịch bản kiểm thử tự động (PHPUnit)", "Trạng thái"],
    ["1", "REQ-3.1-01", "Laravel Framework MVC chuẩn", "app/Http/Controllers, Models, routes/web.php", "Khởi chạy máy chủ, 34 bài test tự động", "Hoàn thành"],
    ["2", "REQ-3.1-02", "CSDL MySQL, Eloquent, Migration", "4 tệp Migration, 13 Eloquent Models", "CatalogAndProductTest (quan hệ CSDL)", "Hoàn thành"],
    ["3", "REQ-3.1-03", "Seeder nạp dữ liệu mẫu sạch", "ShopQuanAoSeeder.php nạp 841 bản ghi", "Nạp thành công qua php artisan db:seed", "Hoàn thành"],
    ["4", "REQ-3.1-04", "Blade + Bootstrap, tách User/Admin", "layouts/app.blade.php, layouts/admin.blade.php", "Home page loads, admin login loads", "Hoàn thành"],
    ["5", "REQ-3.2-01", "Tối thiểu 2 vai trò: Admin & Customer", "Guard web (KHACH_HANG), guard admin (NHAN_VIEN)", "AuthenticationAndRbacTest (đăng nhập 2 guard)", "Hoàn thành"],
    ["6", "REQ-3.2-02", "Phân quyền route bằng Middleware", "AdminMiddleware, ManagerOnlyMiddleware", "Admin guests redirected, manager only revenue", "Hoàn thành"],
    ["7", "REQ-3.3-01", "CRUD đầy đủ trên bảng quan hệ", "CRUD SAN_PHAM (1-N CHI_TIET_SP), DANH_MUC", "Admin category & product CRUD workflow", "Hoàn thành"],
    ["8", "REQ-3.3-02", "Đăng nhập, đăng xuất theo vai trò", "CustomerAuthController, AdminAuthController", "Customer login/logout, staff login/logout", "Hoàn thành"],
    ["9", "REQ-3.3-03", "Tìm kiếm + phân trang giữ từ khóa", "CatalogController::index, phân trang withQueryString", "Search products, pagination query retention", "Hoàn thành"],
    ["10", "REQ-3.3-04", "Validation form báo lỗi rõ ràng", "CheckoutRequest, RegisterRequest, ProductRequest", "Validation on invalid checkout & login", "Hoàn thành"],
    ["11", "REQ-3.3-05", "Flash message sau thêm/sửa/xóa", "session('success'), session('error') trên layouts", "Kiểm tra flash session trong workflow test", "Hoàn thành"],
    ["12", "REQ-5.0-01", "Upload ảnh qua Laravel Storage", "Storage::disk('public'), symlink storage", "Đăng tải và hiển thị ảnh sản phẩm", "Hoàn thành"],
    ["13", "REQ-5.0-02", "Responsive đa thiết bị (Mobile)", "Bootstrap 5 Grid System, Navbar Hamburger", "Kiểm tra giao diện đa kích thước màn hình", "Hoàn thành"],
    ["14", "REQ-5.0-03", "Khóa dòng bi quan lockForUpdate", "lockForUpdate() trong DB::transaction tại Checkout", "Atomic checkout workflow & stock deduction", "Hoàn thành"],
    ["15", "REQ-5.0-04", "Tự động hóa bằng Database Triggers", "4 Triggers MySQL tính ThanhTien, TongTien, hoàn kho", "Trigger recalculates old and new orders", "Hoàn thành"]
]

tbl41 = doc.add_table(rows=len(matrix_data), cols=6)
tbl41.alignment = WD_TABLE_ALIGNMENT.CENTER
for r_idx, row_data in enumerate(matrix_data):
    row_cells = tbl41.rows[r_idx].cells
    for c_idx, val in enumerate(row_data):
        row_cells[c_idx].text = val
        for p in row_cells[c_idx].paragraphs:
            for r in p.runs:
                r.font.name = "Times New Roman"
                r.font.size = Pt(10 if r_idx > 0 else 10.5)
                if r_idx == 0:
                    r.bold = True
p_target_table41._p.addprevious(tbl41._tbl)

update_p_text(doc.paragraphs[318], "4.2.3. Kết quả thực thi kiểm thử tự động", bold=True)

# Add result description and Figure 4.14
p_test_desc = doc.add_paragraph(
    "Toàn bộ 34 kịch bản kiểm thử tự động (Feature và Unit Tests) với 175 khẳng định (assertions) được thực thi trực tiếp qua lệnh php artisan test trên môi trường CSDL test độc lập. Kết quả ghi nhận 100% kịch bản đều đạt trạng thái PASS hoàn hảo trong thời gian thực thi cực nhanh chỉ 1.57 giây, chứng minh hệ thống hoạt động vô cùng ổn định và hoàn toàn loại trừ các lỗi tiềm ẩn.",
    style='Normal'
)
p_test_img = doc.add_paragraph()
r_img414 = p_test_img.add_run()
r_img414.add_picture(r'D:\PHP_NOSQL\hinh_4_14_artisan_test.png', width=Inches(6.2))
p_test_img.alignment = WD_ALIGN_PARAGRAPH.CENTER

p_test_cap = doc.add_paragraph("Hình 4.14: Màn hình kết quả thực thi 34 kịch bản kiểm thử tự động qua lệnh php artisan test.", style='Style5')
p_test_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER

p_target_table41._p.addprevious(p_test_desc._p)
p_target_table41._p.addprevious(p_test_img._p)
p_target_table41._p.addprevious(p_test_cap._p)

# 4.3. Ưu điểm và hạn chế
update_p_text(doc.paragraphs[322], 
    "Hệ thống sở hữu giao diện mua sắm hiện đại, bố cục trực quan, chuẩn responsive Bootstrap 5 và mang đậm tính thẩm mỹ Smart Casual. Về mặt kiến trúc, dự án áp dụng mô hình phân tầng Service Layer kết hợp Eloquent ORM và 4 Database Triggers giúp tách biệt hoàn hảo giữa giao diện và nghiệp vụ, loại trừ hiện tượng Fat Controller và bảo vệ tính toàn vẹn dữ liệu tối đa.")

update_p_text(doc.paragraphs[323], 
    "Hệ thống giải quyết xuất sắc các bài toán hóc búa trong thương mại điện tử thực tế: cơ chế khóa bi quan lockForUpdate() loại trừ lỗi bán âm kho (Overselling); cơ chế Idempotency Key chống tạo trùng đơn; ma trận State Machine quản trị vòng đời đơn hàng chặt chẽ kèm logic tự động hoàn trả tồn kho và voucher khi hủy đơn; hệ thống bảo mật Multi-Guard RBAC phòng chống triệt để lỗ hổng IDOR và Brute-force; đặc biệt là bộ 34 kịch bản kiểm thử tự động bảo đảm chất lượng phần mềm đạt tiêu chuẩn cao.")

update_p_text(doc.paragraphs[326], 
    "Bên cạnh các thành quả nổi bật, hệ thống vẫn có một số hướng mở rộng trong tương lai: tích hợp sâu hơn với môi trường sandbox của các cổng thanh toán trực tuyến như VNPAY/MoMo để xử lý IPN tự động; phát triển mô-đun khuyến nghị sản phẩm (Recommendation System) dựa trên trí tuệ nhân tạo và hành vi mua hàng của khách; mở rộng ứng dụng trên thiết bị di động (Mobile App) thông qua Laravel Sanctum RESTful API.")

update_p_text(doc.paragraphs[327], 
    "Trong giai đoạn tiếp theo, nhóm định hướng hoàn thiện các cổng thanh toán số, nâng cấp phân hệ quản trị thống kê đa chiều với biểu đồ trực quan hóa chuyên sâu và tối ưu hóa hệ thống bộ nhớ đệm Redis để phục vụ lượng truy cập lớn hơn.")

# KẾT LUẬN
print("9. Updating Conclusion...")
update_p_text(doc.paragraphs[332], 
    "Qua quá trình thực hiện đồ án “Xây dựng website thời trang Smart Casual”, nhóm đã hoàn thành xuất sắc việc phân tích nghiệp vụ, thiết kế kiến trúc phần mềm và hiện thực một hệ thống thương mại điện tử chuyên nghiệp trên nền tảng PHP 8.3, Laravel 11.x và CSDL MySQL 8.4. Hệ thống đáp ứng trọn vẹn 15/15 yêu cầu kỹ thuật của đề cương môn học, đồng thời áp dụng thành công các giải pháp kỹ thuật nâng cao như Service Layer Pattern, Khóa dòng bi quan lockForUpdate(), Idempotency Key, State Machine và 4 Database Triggers.")

update_p_text(doc.paragraphs[333], 
    "Toàn bộ quy trình từ đăng ký, phân quyền Multi-Guard, duyệt danh mục, tìm kiếm phân trang, giỏ hàng đồng bộ, thanh toán an toàn cho đến quản lý đơn hàng và thống kê doanh thu đều được kiểm chứng nghiêm ngặt qua 34 kịch bản kiểm thử tự động (PHPUnit) với tỷ lệ đạt 100% PASS.")

update_p_text(doc.paragraphs[334], 
    "Đồ án không chỉ giúp các thành viên trong nhóm làm chủ framework Laravel và hệ quản trị CSDL quan hệ MySQL, mà còn trang bị tư duy thiết kế phần mềm theo tiêu chuẩn công nghiệp, kỹ năng làm việc nhóm bài bản và phương pháp giải quyết các bài toán kỹ thuật phức tạp trong thực tế.")

# TÀI LIỆU THAM KHẢO
print("10. Updating References...")
refs = [
    "[1] Taylor Otwell, Laravel: Up & Running (3rd Edition), O'Reilly Media, 2024.",
    "[2] The PHP Group, PHP Manual: PHP 8.3 Documentation, [Online]. Available: https://www.php.net/manual/en. Accessed: 2026.",
    "[3] Oracle Corporation, MySQL 8.4 Reference Manual, Oracle Documentation, [Online]. Available: https://dev.mysql.com/doc. Accessed: 2026.",
    "[4] Khoa Công nghệ Thông tin, Giáo trình Lập trình Mã nguồn mở, Trường Đại học Công Thương TP. Hồ Chí Minh, 2024.",
    "[5] Martin Fowler, Patterns of Enterprise Application Architecture, Addison-Wesley Professional, 2002.",
    "[6] Bootstrap Team, Bootstrap 5.2 Documentation, [Online]. Available: https://getbootstrap.com. Accessed: 2026.",
    "[7] Sebastian Bergmann, PHPUnit Manual: Testing Laravel Applications, [Online]. Available: https://phpunit.de. Accessed: 2026."
]
for i, ref in enumerate(refs):
    p_idx = 339 + i
    if p_idx < len(doc.paragraphs):
        update_p_text(doc.paragraphs[p_idx], ref)

# Save intermediate docx
doc.save(out_docx)
print(f"Saved modified docx to: {out_docx}")

# 11. Replace images in zip archive
print("11. Replacing images in zip archive...")
temp_zip = out_docx + ".tmp.zip"
with zipfile.ZipFile(out_docx, 'r') as zin, zipfile.ZipFile(temp_zip, 'w', compression=zipfile.ZIP_DEFLATED) as zout:
    for item in zin.infolist():
        fname = item.filename
        if fname == 'word/media/image10.png':
            with open(r'D:\PHP_NOSQL\hinh_2_1_uml_use_case.png', 'rb') as f_img:
                zout.writestr(fname, f_img.read())
            print(" -> Replaced word/media/image10.png with hinh_2_1_uml_use_case.png")
        elif fname == 'word/media/image11.png':
            with open(r'D:\PHP_NOSQL\hinh_2_2_erd_13_bang.png', 'rb') as f_img:
                zout.writestr(fname, f_img.read())
            print(" -> Replaced word/media/image11.png with hinh_2_2_erd_13_bang.png")
        elif fname == 'word/media/image23.png':
            with open(r'D:\PHP_NOSQL\hinh_3_12_env_config.png', 'rb') as f_img:
                zout.writestr(fname, f_img.read())
            print(" -> Replaced word/media/image23.png with hinh_3_12_env_config.png")
        else:
            zout.writestr(item, zin.read(fname))

os.remove(out_docx)
os.rename(temp_zip, out_docx)

# Also create a copy as BAO_CAO_DO_AN_MON_HOC_LARAVEL_FINAL.docx
shutil.copy2(out_docx, r'D:\PHP_NOSQL\BAO_CAO_DO_AN_MON_HOC_LARAVEL_FINAL.docx')
print("Successfully generated and synchronized BAO_CAO_DO_AN_PHP_SMART_CASUAL_NHOM5.docx and BAO_CAO_DO_AN_MON_HOC_LARAVEL_FINAL.docx!")
