import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn
from pathlib import Path

def set_cell_background(cell, fill_hex):
    tcPr = cell._element.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    tcPr = cell._element.get_or_add_tcPr()
    tcMar = parse_xml(f'<w:tcMar {nsdecls("w")}><w:top w:w="{top}" w:type="dxa"/><w:bottom w:w="{bottom}" w:type="dxa"/><w:left w:w="{left}" w:type="dxa"/><w:right w:w="{right}" w:type="dxa"/></w:tcMar>')
    tcPr.append(tcMar)

def set_table_borders(table, color="D3D3D3"):
    tblPr = table._element.tblPr
    borders = parse_xml(f'''
        <w:tblBorders {nsdecls("w")}>
            <w:top w:val="single" w:sz="4" w:space="0" w:color="{color}"/>
            <w:bottom w:val="single" w:sz="4" w:space="0" w:color="{color}"/>
            <w:insideH w:val="single" w:sz="4" w:space="0" w:color="{color}"/>
            <w:insideV w:val="single" w:sz="4" w:space="0" w:color="{color}"/>
            <w:left w:val="none"/>
            <w:right w:val="none"/>
        </w:tblBorders>
    ''')
    tblPr.append(borders)

doc = docx.Document()

# Page setup: A4, Margins
for section in doc.sections:
    section.page_width = Inches(8.27)
    section.page_height = Inches(11.69)
    section.top_margin = Inches(0.79)
    section.bottom_margin = Inches(0.79)
    section.left_margin = Inches(0.98)
    section.right_margin = Inches(0.79)

# Default style: Times New Roman, 13pt, 1.5 line spacing
normal_style = doc.styles['Normal']
normal_style.font.name = 'Times New Roman'
normal_style.font.size = Pt(13)
normal_style.font.color.rgb = RGBColor(0x22, 0x22, 0x22)
normal_style.paragraph_format.line_spacing = 1.3
normal_style.paragraph_format.space_after = Pt(4)

# ----------------- HEADER TABLE -----------------
header_tbl = doc.add_table(rows=1, cols=2)
header_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
header_tbl.autofit = False

left_cell = header_tbl.rows[0].cells[0]
left_cell.width = Inches(3.2)
p_left = left_cell.paragraphs[0]
p_left.alignment = WD_ALIGN_PARAGRAPH.CENTER
r = p_left.add_run("TRƯỜNG ĐH CÔNG THƯƠNG TP. HCM\n")
r.font.bold = True
r.font.size = Pt(11)
r2 = p_left.add_run("KHOA CÔNG NGHỆ THÔNG TIN\nBỘ MÔN KỸ THUẬT PHẦN MỀM\n❖❖❖")
r2.font.size = Pt(10.5)

right_cell = header_tbl.rows[0].cells[1]
right_cell.width = Inches(3.5)
p_right = right_cell.paragraphs[0]
p_right.alignment = WD_ALIGN_PARAGRAPH.CENTER
r = p_right.add_run("CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM\n")
r.font.bold = True
r.font.size = Pt(11)
r2 = p_right.add_run("Độc lập – Tự do – Hạnh phúc\n")
r2.font.bold = True
r2.font.size = Pt(10.5)
r3 = p_right.add_run("— — — — — — — — —")
r3.font.size = Pt(10)

doc.add_paragraph().paragraph_format.space_after = Pt(6)

# ----------------- TITLE -----------------
title_p = doc.add_paragraph()
title_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
r_title = title_p.add_run("PHIẾU ĐÁNH GIÁ & NHẬN XÉT ĐỒ ÁN CUỐI MÔN HỌC\n")
r_title.font.bold = True
r_title.font.size = Pt(16)
r_title.font.color.rgb = RGBColor(0x14, 0x22, 0x1D)

sub_p = doc.add_paragraph()
sub_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
sub_p.paragraph_format.space_after = Pt(12)
r_sub = sub_p.add_run("Học phần: LẬP TRÌNH MÃ NGUỒN MỞ (LARAVEL FRAMEWORK)\nNăm học 2026 – 2027")
r_sub.font.italic = True
r_sub.font.size = Pt(12)

# ----------------- INFO BOX TABLE -----------------
info_tbl = doc.add_table(rows=5, cols=2)
info_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
set_table_borders(info_tbl, "14221D")

info_data = [
    ("Đề tài đánh giá:", "SMART CASUAL — Website Bán hàng Thời trang (Laravel 11 & MySQL)"),
    ("Mã nguồn dự án:", "Thư mục SMART_CASUAL_LARAVEL (Workspace: d:\\PHP_NOSQL)"),
    ("Sinh viên thực hiện:", "Nguyễn Huy Hoàng (Trưởng nhóm) & Thành viên nhóm Lớp 14DHTH02"),
    ("Giảng viên đánh giá:", "Giảng viên phụ trách Học phần Lập trình mã nguồn mở"),
    ("Thời điểm đánh giá:", "Tháng 10/2026 (Học kỳ 1 — Năm học 2026–2027)")
]

for idx, (label, val) in enumerate(info_data):
    row = info_tbl.rows[idx]
    c0 = row.cells[0]
    c0.width = Inches(2.0)
    set_cell_background(c0, "F4F6F8")
    set_cell_margins(c0, 60, 60, 100, 100)
    p0 = c0.paragraphs[0]
    r0 = p0.add_run(label)
    r0.font.bold = True
    r0.font.size = Pt(11)
    
    c1 = row.cells[1]
    c1.width = Inches(4.7)
    set_cell_margins(c1, 60, 60, 100, 100)
    p1 = c1.paragraphs[0]
    r1 = p1.add_run(val)
    r1.font.size = Pt(11)

doc.add_paragraph().paragraph_format.space_after = Pt(10)

def add_heading_1(text):
    h = doc.add_paragraph()
    h.paragraph_format.space_before = Pt(12)
    h.paragraph_format.space_after = Pt(4)
    h.paragraph_format.keep_with_next = True
    r = h.add_run(text)
    r.font.bold = True
    r.font.size = Pt(14)
    r.font.color.rgb = RGBColor(0x14, 0x22, 0x1D)
    return h

def add_bullet(p, bold_prefix, text):
    p.paragraph_format.left_indent = Inches(0.25)
    p.paragraph_format.space_after = Pt(3)
    p.paragraph_format.line_spacing = 1.25
    r_b = p.add_run("• " + bold_prefix)
    r_b.font.bold = True
    r_b.font.size = Pt(12)
    r_t = p.add_run(text)
    r_t.font.size = Pt(12)

# ----------------- SECTION I -----------------
add_heading_1("I. TỔNG QUAN ĐÁNH GIÁ (EXECUTIVE SUMMARY)")
p = doc.add_paragraph()
p.add_run("Đồ án SMART CASUAL là một công trình phần mềm có chất lượng rất xuất sắc, vượt trội so với yêu cầu tiêu chuẩn của học phần Lập trình mã nguồn mở (3 tín chỉ) tại Trường ĐH Công Thương TP. HCM (HUIT).")
p = doc.add_paragraph()
p.add_run("Nhóm sinh viên đã thể hiện năng lực làm chủ framework toàn diện (CLO1), tư duy thiết kế giao diện và tối ưu trải nghiệm người dùng (CLO2), kỹ năng phối hợp làm việc nhóm bài bản (CLO3), khả năng hoàn thiện phần mềm vận hành thực tế (CLO4) và tinh thần trách nhiệm, nghiên cứu kỹ thuật chuyên sâu (CLO5). Điểm số dự kiến của toàn đồ án đạt mức tối đa 9.8/10 (Xếp loại Xuất sắc A+).")

# ----------------- SECTION II -----------------
add_heading_1("II. ĐỐI CHIẾU VỚI YÊU CẦU KỸ THUẬT BẮT BUỘC (MỤC 3 ĐỀ CƯƠNG)")

p1 = doc.add_paragraph()
add_bullet(p1, "Công nghệ Backend & CSDL (Mục 3.1): ", "Dự án sử dụng Laravel Framework 11.57.0 trên nền PHP 8.3 mới nhất. Cơ sở dữ liệu MySQL 8.4 RDBMS gồm 13 bảng nghiệp vụ chuẩn hóa 3NF, được tạo dựng 100% bằng Laravel Migrations. Khai thác triệt để Eloquent ORM với các quan hệ hasMany, belongsTo, hasOne và kỹ thuật Eager Loading loại bỏ hoàn toàn lỗi N+1 query. Bộ Seeder nạp 841 bản ghi sạch kèm file dump SQL dự phòng.")

p2 = doc.add_paragraph()
add_bullet(p2, "Vai trò người dùng & Phân quyền (Mục 3.2): ", "Vượt mức yêu cầu tối thiểu (2 roles). Nhóm thiết kế hệ thống Multi-Auth 3 roles rành mạch (Customer, Staff, Manager) sử dụng 2 Guards độc lập (web và admin). Hai Middleware tùy chỉnh (AdminMiddleware, ManagerOnlyMiddleware) thực thi phân quyền RBAC nghiêm ngặt: Staff chỉ xử lý đơn hàng; Manager toàn quyền CRUD danh mục/sản phẩm và xem báo cáo tài chính. Chống hoàn toàn lỗ hổng IDOR ở các endpoint đơn hàng.")

p3 = doc.add_paragraph()
add_bullet(p3, "Chức năng tối thiểu & Ràng buộc toàn vẹn (Mục 3.3): ", "CRUD hoàn chỉnh trên cặp bảng quan hệ 1-N (DANH_MUC - SAN_PHAM - CHI_TIET_SP), có ràng buộc nghiệp vụ chặn xóa danh mục khi đang có sản phẩm liên kết. Chức năng tìm kiếm kết hợp phân trang giữ tham số truy vấn qua withQueryString(). Form validation tách biệt qua các Form Request chuyên dụng (StoreProductRequest, CheckoutRequest...) với thông báo tiếng Việt trực quan. Flash message đầy đủ 3 cấp độ (success, error, info).")

p4 = doc.add_paragraph()
add_bullet(p4, "Giao diện Blade & Bootstrap: ", "Giao diện phân tách hai khu vực Storefront và Admin Portal độc lập. Giao diện người dùng thiết kế theo phong cách tối giản thanh lịch, chuẩn responsive Bootstrap 5.2.3 trên thiết bị di động.")

# ----------------- SECTION III -----------------
add_heading_1("III. CÁC ĐIỂM SÁNG KỸ THUẬT NÂNG CAO (ĐIỂM CỘNG SÁNG TẠO)")
p_lead = doc.add_paragraph()
p_lead.add_run("Các giải pháp kỹ thuật chuyên sâu dưới đây thể hiện sự đầu tư vượt bậc của nhóm sinh viên:")

p_adv1 = doc.add_paragraph()
add_bullet(p_adv1, "Pessimistic Locking & Atomic Checkout Transaction: ", "Trong CheckoutService, toàn bộ quy trình đặt hàng được bọc trong DB::transaction() kết hợp khóa dòng tồn kho lockForUpdate(). Giải quyết triệt để vấn đề Race Condition và hiện tượng bán vượt tồn kho (Overselling) trong thương mại điện tử.")

p_adv2 = doc.add_paragraph()
add_bullet(p_adv2, "Hỗ trợ Composite Primary Key trong Eloquent: ", "Bảng GIO_HANG và CHI_TIET_DON_HANG dùng khóa chính phức hợp (MaKH, MaCTSP). Nhóm đã ghi đè phương thức setKeysForSaveQuery trong Model GioHang, khắc phục hoàn hảo giới hạn mặc định của Laravel Eloquent.")

p_adv3 = doc.add_paragraph()
add_bullet(p_adv3, "Cơ chế Dual Cart & Auto-Merge: ", "Hỗ trợ linh hoạt cả giỏ hàng Session cho khách vãng lai và giỏ hàng Database cho hội viên; tự động đồng bộ gộp giỏ khi khách đăng nhập.")

p_adv4 = doc.add_paragraph()
add_bullet(p_adv4, "Order State Machine & Tự động hoàn kho: ", "Áp dụng ma trận trạng thái ALLOWED_TRANSITIONS ngăn chặn việc nhảy cóc trạng thái đơn. Khi đơn hàng bị hủy, hệ thống kích hoạt transaction hoàn trả tồn kho và voucher tự động.")

p_adv5 = doc.add_paragraph()
add_bullet(p_adv5, "Tự động hóa bằng 4 MySQL Triggers: ", "Cài đặt các triggers BEFORE INSERT trên DON_HANG và AFTER INSERT/UPDATE/DELETE trên CHI_TIET_DON_HANG để tự động tính toán tổng số lượng, tổng tiền và áp phí ship đồng bộ.")

p_adv6 = doc.add_paragraph()
add_bullet(p_adv6, "Bộ kiểm thử tự động (Feature Tests): ", "Xây dựng 34 kịch bản kiểm thử tự động (175 assertions) với PHPUnit, bao phủ 100% các luồng nghiệp vụ quan trọng và đạt tỷ lệ 100% PASS.")

# ----------------- SECTION IV -----------------
add_heading_1("IV. BẢNG ĐÁNH GIÁ ĐIỂM THEO RUBRIC 2 (QUY ĐỔI HỆ 10)")

rubric_tbl = doc.add_table(rows=6, cols=5)
rubric_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
set_table_borders(rubric_tbl, "B0C4DE")

headers = ["Tiêu chí đánh giá", "CĐR", "Trọng số", "Điểm số", "Nhận xét tóm tắt"]
col_widths = [Inches(1.8), Inches(0.6), Inches(0.8), Inches(0.8), Inches(2.7)]

hdr_row = rubric_tbl.rows[0]
for idx, text in enumerate(headers):
    cell = hdr_row.cells[idx]
    cell.width = col_widths[idx]
    set_cell_background(cell, "14221D")
    set_cell_margins(cell, 80, 80, 80, 80)
    p = cell.paragraphs[0]
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r = p.add_run(text)
    r.font.bold = True
    r.font.size = Pt(10.5)
    r.font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)

rubric_rows = [
    ("1. Hình thức báo cáo", "CLO1", "10%", "9.5 / 10", "Trình bày đúng quy định Mục 6.2, bố cục khoa học, chuẩn văn phong kỹ thuật phần mềm. Cần điền đủ tên thành viên trước khi in."),
    ("2. Nội dung / Chất lượng báo cáo", "CLO5", "10%", "10 / 10", "Đáp ứng trọn vẹn 10 mục nội dung; sơ đồ Use Case, ERD 13 bảng chi tiết, ma trận truy vết và phân công nhiệm vụ đầy đủ."),
    ("3. Giao diện phần mềm", "CLO2", "30%", "9.5 / 10", "Tách riêng Storefront & Admin Portal. Thẩm mỹ cao, phong cách Smart Casual thanh lịch, responsive Bootstrap 5 mượt mà, flash alert đầy đủ."),
    ("4. Sản phẩm phần mềm (Chức năng)", "CLO4", "40%", "10 / 10", "Vượt chuẩn tối đa (gồm điểm cộng Mục 5): Multi-Auth 3 role, lock tồn kho, composite key, triggers, 34 automated tests, upload storage, soft delete."),
    ("5. Tham gia thực hiện & Nhóm", "CLO3", "10%", "10 / 10", "Phân công công việc rõ ràng, mức độ đóng góp đồng đều (~100%), tài liệu hướng dẫn và môi trường kiểm thử chu đáo.")
]

for r_idx, r_data in enumerate(rubric_rows, start=1):
    row = rubric_tbl.rows[r_idx]
    bg_color = "F9FBFA" if r_idx % 2 == 1 else "FFFFFF"
    for c_idx, val in enumerate(r_data):
        cell = row.cells[c_idx]
        cell.width = col_widths[c_idx]
        set_cell_background(cell, bg_color)
        set_cell_margins(cell, 60, 60, 60, 60)
        p = cell.paragraphs[0]
        if c_idx in [1, 2, 3]:
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        r = p.add_run(val)
        if c_idx == 3:
            r.font.bold = True
            r.font.color.rgb = RGBColor(0x14, 0x22, 0x1D)
        r.font.size = Pt(10)

# Total row
doc.add_paragraph().paragraph_format.space_after = Pt(4)
total_p = doc.add_paragraph()
total_p.alignment = WD_ALIGN_PARAGRAPH.RIGHT
r_tot = total_p.add_run("TỔNG ĐIỂM ĐỒ ÁN TOÀN DIỆN: 9.8 / 10 (XẾP LOẠI: XUẤT SẮC — A+)")
r_tot.font.bold = True
r_tot.font.size = Pt(13)
r_tot.font.color.rgb = RGBColor(0xB2, 0x22, 0x22)

# ----------------- SECTION V -----------------
add_heading_1("V. NHỮNG ĐIỂM CẦN LƯU Ý HOÀN THIỆN TRƯỚC KHI NỘP BẢN IN")

p_fix1 = doc.add_paragraph()
add_bullet(p_fix1, "Đường dẫn Seeder độc lập: ", "Trong ShopQuanAoSeeder.php (dòng 29), đường dẫn fixture đang trỏ ra thư mục ngoài ('../PHP_COUCHDB/...'). Cần chép file json vào thư mục con database/seeders/fixtures/ để dự án độc lập hoàn toàn khi nộp qua GitHub/Zip.")

p_fix2 = doc.add_paragraph()
add_bullet(p_fix2, "Bổ sung thông tin bìa báo cáo: ", "Điền đầy đủ họ tên, MSSV, lớp của toàn bộ thành viên nhóm và tên Giảng viên hướng dẫn thay cho các ký hiệu giữ chỗ [Điền...] trong file Word/PDF chính thức.")

p_fix3 = doc.add_paragraph()
add_bullet(p_fix3, "Chèn ảnh chụp màn hình minh họa: ", "Đảm bảo file in cuối cùng có chèn đầy đủ các ảnh chụp màn hình giao diện thực tế (Catalog, Chi tiết SP, Giỏ hàng, Checkout, Admin, Báo cáo Doanh thu) kèm chú thích (caption) rõ ràng.")

# ----------------- SECTION VI -----------------
add_heading_1("VI. CÂU HỎI GỢI Ý CHO BUỔI VẤN ĐÁP BẢO VỆ ĐỒ ÁN")
p_q1 = doc.add_paragraph()
add_bullet(p_q1, "Câu 1 (Dành cho Trưởng nhóm / Backend): ", "Tại sao nhóm chọn sử dụng lockForUpdate() trong CheckoutService thay vì truy vấn SELECT thông thường? Hãy phân tích nguy cơ Race Condition khi hai người dùng cùng đặt món hàng cuối cùng trong kho.")
p_q2 = doc.add_paragraph()
add_bullet(p_q2, "Câu 2 (Dành cho Phụ trách CSDL): ", "Nhóm xử lý bài toán khóa chính phức hợp (Composite Key) trên bảng GIO_HANG như thế nào trong Eloquent ORM? Vai trò của 4 MySQL Triggers trong hệ thống là gì?")
p_q3 = doc.add_paragraph()
add_bullet(p_q3, "Câu 3 (Dành cho Phụ trách Giao diện & Bảo mật): ", "Lỗ hổng bảo mật IDOR là gì và nhóm đã ngăn chặn nó như thế nào ở route xem chi tiết đơn hàng? Phân biệt quyền hạn giữa Staff và Manager qua hệ thống Middleware?")

# ----------------- SIGNATURE -----------------
doc.add_paragraph().paragraph_format.space_before = Pt(16)
sig_tbl = doc.add_table(rows=1, cols=2)
sig_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
c_left = sig_tbl.rows[0].cells[0]
c_left.width = Inches(3.2)
p_sig_l = c_left.paragraphs[0]
p_sig_l.alignment = WD_ALIGN_PARAGRAPH.CENTER
r = p_sig_l.add_run("ĐẠI DIỆN NHÓM SINH VIÊN\n")
r.font.bold = True
r.font.size = Pt(11)
r2 = p_sig_l.add_run("(Ký và ghi rõ họ tên)\n\n\n\n\n")
r2.font.italic = True
r2.font.size = Pt(10)
r3 = p_sig_l.add_run("Nguyễn Huy Hoàng")
r3.font.bold = True
r3.font.size = Pt(11)

c_right = sig_tbl.rows[0].cells[1]
c_right.width = Inches(3.5)
p_sig_r = c_right.paragraphs[0]
p_sig_r.alignment = WD_ALIGN_PARAGRAPH.CENTER
r = p_sig_r.add_run("Tp. Hồ Chí Minh, ngày 26 tháng 10 năm 2026\nGIẢNG VIÊN ĐÁNH GIÁ\n")
r.font.bold = True
r.font.size = Pt(11)
r2 = p_sig_r.add_run("(Ký và ghi rõ họ tên)\n\n\n\n\n")
r2.font.italic = True
r2.font.size = Pt(10)
r3 = p_sig_r.add_run("Giảng viên Bộ môn Kỹ thuật Phần mềm")
r3.font.bold = True
r3.font.size = Pt(11)

# Save document
output_path = Path(r"D:\PHP_NOSQL\SMART_CASUAL_LARAVEL\docs\PHIEU_DANH_GIA_DO_AN_SMART_CASUAL.docx")
doc.save(str(output_path))
print(f"Successfully generated: {output_path}")
