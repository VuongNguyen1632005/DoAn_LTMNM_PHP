"""
Tệp: generate_uml_use_case.py
Chức năng: Tạo sơ đồ UML Use Case tổng quan hệ thống Smart Casual chuẩn mực
dưới cả 2 định dạng: Vector SVG chất lượng cao và ảnh bitmap PNG độ phân giải lớn.
"""

from pathlib import Path
import sys

sys.stdout.reconfigure(encoding='utf-8')

def create_svg():
    svg_content = """<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1520 1020" width="100%" height="100%">
  <defs>
    <!-- Gradients -->
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#FFFFFF"/>
      <stop offset="100%" stop-color="#F4F6F8"/>
    </linearGradient>
    
    <linearGradient id="sysBoxGrad" x1="0%" y1="0%" x2="0%" y2="100%">
      <stop offset="0%" stop-color="#FFFFFF"/>
      <stop offset="100%" stop-color="#FAFCFB"/>
    </linearGradient>

    <linearGradient id="ucGradPublic" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#EBF5FB"/>
      <stop offset="100%" stop-color="#D4E6F1"/>
    </linearGradient>

    <linearGradient id="ucGradCustomer" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#E8F8F5"/>
      <stop offset="100%" stop-color="#D1F2EB"/>
    </linearGradient>

    <linearGradient id="ucGradStaff" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#FEF9E7"/>
      <stop offset="100%" stop-color="#FCF3CF"/>
    </linearGradient>

    <linearGradient id="ucGradManager" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#FDEDEC"/>
      <stop offset="100%" stop-color="#FADBD8"/>
    </linearGradient>

    <!-- Markers -->
    <marker id="generalization" viewBox="0 0 12 12" refX="10" refY="6" markerWidth="10" markerHeight="10" orient="auto">
      <path d="M 0 0 L 10 6 L 0 12 z" fill="#FFFFFF" stroke="#14221D" stroke-width="1.8"/>
    </marker>
    
    <!-- Filter for soft shadow -->
    <filter id="boxShadow" x="-2%" y="-2%" width="104%" height="104%">
      <feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="#000000" flood-opacity="0.06"/>
    </filter>
  </defs>

  <style>
    .title { font-family: 'Segoe UI', Arial, sans-serif; font-size: 20px; font-weight: bold; fill: #14221D; }
    .subtitle { font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; fill: #555555; }
    .boundary-title { font-family: 'Segoe UI', Arial, sans-serif; font-size: 16px; font-weight: bold; fill: #14221D; }
    .pkg-title { font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; font-weight: bold; fill: #2C3E50; }
    .actor-name { font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; font-weight: bold; fill: #14221D; text-anchor: middle; }
    .actor-role { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; fill: #555555; text-anchor: middle; font-style: italic; }
    .uc-text { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; font-weight: 600; fill: #1C2833; text-anchor: middle; }
    .uc-code { font-family: 'Consolas', monospace; font-size: 9.5px; fill: #7F8C8D; text-anchor: middle; }
    .assoc-line { stroke: #566573; stroke-width: 1.4; fill: none; }
    .inherit-line { stroke: #14221D; stroke-width: 2.0; stroke-dasharray: 4,4; fill: none; }
    .pkg-rect { fill: none; stroke: #D5DBDB; stroke-width: 1.2; stroke-dasharray: 6,4; rx: 8; }
  </style>

  <!-- Background -->
  <rect width="100%" height="100%" fill="url(#bgGrad)"/>

  <!-- Main System Header -->
  <text x="760" y="36" text-anchor="middle" class="title">HỆ THỐNG WEBSITE THỜI TRANG NAM "SMART CASUAL"</text>
  <text x="760" y="58" text-anchor="middle" class="subtitle">SƠ ĐỒ UML USE CASE TỔNG QUAN — 4 TÁC NHÂN &amp; 17 CA SỬ DỤNG PHÂN QUYỀN</text>

  <!-- System Boundary Box -->
  <rect x="230" y="75" width="1060" height="920" rx="14" fill="url(#sysBoxGrad)" stroke="#14221D" stroke-width="2.2" filter="url(#boxShadow)"/>
  <rect x="230" y="75" width="1060" height="38" rx="14" fill="#14221D"/>
  <text x="760" y="100" text-anchor="middle" font-family="'Segoe UI', Arial, sans-serif" font-size="14px" font-weight="bold" fill="#FFFFFF">Ranh giới Hệ thống (Smart Casual E-Commerce Boundary)</text>

  <!-- Subsystem Packages (Dashed Groupings) -->
  <!-- 1. Storefront Public Package -->
  <rect x="245" y="125" width="515" height="410" class="pkg-rect"/>
  <text x="260" y="145" class="pkg-title">Phân hệ Cửa hàng Công khai (Storefront Public)</text>

  <!-- 2. Storefront Customer Package -->
  <rect x="245" y="550" width="515" height="430" class="pkg-rect"/>
  <text x="260" y="570" class="pkg-title">Phân hệ Khách hàng Đã xác thực (Customer Authenticated - guard: web)</text>

  <!-- 3. Admin Staff Package -->
  <rect x="780" y="125" width="495" height="490" class="pkg-rect"/>
  <text x="795" y="145" class="pkg-title">Phân hệ Quản trị Nhân viên (Staff Portal - guard: admin)</text>

  <!-- 4. Admin Manager Package -->
  <rect x="780" y="630" width="495" height="350" class="pkg-rect"/>
  <text x="795" y="650" class="pkg-title">Phân hệ Quản trị Cấp cao (Manager Only - ManagerOnlyMiddleware)</text>

  <!-- ==================== USE CASES (17 OVALS) ==================== -->

  <!-- UC01: Xem danh mục & Catalog -->
  <g transform="translate(500, 180)">
    <ellipse cx="0" cy="0" rx="135" ry="24" fill="url(#ucGradPublic)" stroke="#2980B9" stroke-width="1.6"/>
    <text x="0" y="-3" class="uc-text">Xem danh mục &amp; Catalog</text>
    <text x="0" y="11" class="uc-code">UC-01 | /products</text>
  </g>

  <!-- UC02: Xem chi tiết sản phẩm & biến thể -->
  <g transform="translate(500, 245)">
    <ellipse cx="0" cy="0" rx="145" ry="24" fill="url(#ucGradPublic)" stroke="#2980B9" stroke-width="1.6"/>
    <text x="0" y="-3" class="uc-text">Xem chi tiết sản phẩm &amp; Chọn size</text>
    <text x="0" y="11" class="uc-code">UC-02 | /products/{id}</text>
  </g>

  <!-- UC03: Tìm kiếm & Lọc sản phẩm -->
  <g transform="translate(500, 310)">
    <ellipse cx="0" cy="0" rx="140" ry="24" fill="url(#ucGradPublic)" stroke="#2980B9" stroke-width="1.6"/>
    <text x="0" y="-3" class="uc-text">Tìm kiếm &amp; Lọc (16 sp/trang)</text>
    <text x="0" y="11" class="uc-code">UC-03 | withQueryString()</text>
  </g>

  <!-- UC04: Quản lý giỏ hàng Session -->
  <g transform="translate(500, 375)">
    <ellipse cx="0" cy="0" rx="135" ry="24" fill="url(#ucGradPublic)" stroke="#2980B9" stroke-width="1.6"/>
    <text x="0" y="-3" class="uc-text">Quản lý giỏ hàng Session</text>
    <text x="0" y="11" class="uc-code">UC-04 | /cart</text>
  </g>

  <!-- UC05: Đặt hàng COD / Chuyển khoản -->
  <g transform="translate(500, 440)">
    <ellipse cx="0" cy="0" rx="145" ry="25" fill="url(#ucGradPublic)" stroke="#2980B9" stroke-width="1.8"/>
    <text x="0" y="-3" class="uc-text">Đặt hàng thanh toán COD / Bank</text>
    <text x="0" y="12" class="uc-code">UC-05 | CheckoutService</text>
  </g>

  <!-- UC06: Gửi nhận xét & Đánh giá sao -->
  <g transform="translate(500, 505)">
    <ellipse cx="0" cy="0" rx="135" ry="24" fill="url(#ucGradPublic)" stroke="#2980B9" stroke-width="1.6"/>
    <text x="0" y="-3" class="uc-text">Gửi đánh giá &amp; Chấm điểm sao</text>
    <text x="0" y="11" class="uc-code">UC-06 | /products/{id}/reviews</text>
  </g>

  <!-- UC07: Đăng ký & Đăng nhập thành viên -->
  <g transform="translate(500, 615)">
    <ellipse cx="0" cy="0" rx="140" ry="24" fill="url(#ucGradCustomer)" stroke="#16A085" stroke-width="1.6"/>
    <text x="0" y="-3" class="uc-text">Đăng ký &amp; Đăng nhập thành viên</text>
    <text x="0" y="11" class="uc-code">UC-07 | /login, /register</text>
  </g>

  <!-- UC08: Xem lịch sử đơn hàng cá nhân -->
  <g transform="translate(500, 680)">
    <ellipse cx="0" cy="0" rx="140" ry="24" fill="url(#ucGradCustomer)" stroke="#16A085" stroke-width="1.6"/>
    <text x="0" y="-3" class="uc-text">Xem lịch sử &amp; Chi tiết đơn cá nhân</text>
    <text x="0" y="11" class="uc-code">UC-08 | /account/orders (Anti-IDOR)</text>
  </g>

  <!-- UC09: Tự hủy đơn hàng (pending, confirmed, packing) -->
  <g transform="translate(500, 745)">
    <ellipse cx="0" cy="0" rx="145" ry="25" fill="url(#ucGradCustomer)" stroke="#16A085" stroke-width="1.8"/>
    <text x="0" y="-3" class="uc-text">Tự hủy đơn hàng &amp; Hoàn kho</text>
    <text x="0" y="12" class="uc-code">UC-09 | /account/orders/{id}/cancel</text>
  </g>

  <!-- UC10: Tự động đồng bộ giỏ hàng Session vào CSDL -->
  <g transform="translate(500, 810)">
    <ellipse cx="0" cy="0" rx="145" ry="24" fill="url(#ucGradCustomer)" stroke="#16A085" stroke-width="1.6"/>
    <text x="0" y="-3" class="uc-text">Đồng bộ giỏ hàng vào CSDL</text>
    <text x="0" y="11" class="uc-code">UC-10 | CartService::syncSessionToDb()</text>
  </g>

  <!-- UC11: Đăng nhập Cổng quản trị Admin -->
  <g transform="translate(1030, 185)">
    <ellipse cx="0" cy="0" rx="135" ry="24" fill="url(#ucGradStaff)" stroke="#D4AC0D" stroke-width="1.6"/>
    <text x="0" y="-3" class="uc-text">Đăng nhập Admin Portal</text>
    <text x="0" y="11" class="uc-code">UC-11 | /admin/login</text>
  </g>

  <!-- UC12: Quản lý Sản phẩm & Biến thể SKU -->
  <g transform="translate(1030, 255)">
    <ellipse cx="0" cy="0" rx="145" ry="25" fill="url(#ucGradStaff)" stroke="#D4AC0D" stroke-width="1.6"/>
    <text x="0" y="-3" class="uc-text">Quản lý Sản phẩm &amp; Biến thể SKU</text>
    <text x="0" y="12" class="uc-code">UC-12 | CRUD, Storage, TrangThai</text>
  </g>

  <!-- UC13: Quản lý Danh mục hàng hóa -->
  <g transform="translate(1030, 325)">
    <ellipse cx="0" cy="0" rx="135" ry="24" fill="url(#ucGradStaff)" stroke="#D4AC0D" stroke-width="1.6"/>
    <text x="0" y="-3" class="uc-text">Quản lý Danh mục hàng hóa</text>
    <text x="0" y="11" class="uc-code">UC-13 | /admin/categories</text>
  </g>

  <!-- UC14: Xử lý Đơn hàng & Đổi trạng thái -->
  <g transform="translate(1030, 395)">
    <ellipse cx="0" cy="0" rx="145" ry="25" fill="url(#ucGradStaff)" stroke="#D4AC0D" stroke-width="1.8"/>
    <text x="0" y="-3" class="uc-text">Xử lý đơn hàng &amp; Đổi trạng thái</text>
    <text x="0" y="12" class="uc-code">UC-14 | ALLOWED_TRANSITIONS</text>
  </g>

  <!-- UC15: Kiểm duyệt Đánh giá khách hàng -->
  <g transform="translate(1030, 465)">
    <ellipse cx="0" cy="0" rx="140" ry="24" fill="url(#ucGradStaff)" stroke="#D4AC0D" stroke-width="1.6"/>
    <text x="0" y="-3" class="uc-text">Kiểm duyệt đánh giá khách hàng</text>
    <text x="0" y="11" class="uc-code">UC-15 | /admin/reviews</text>
  </g>

  <!-- UC16: Báo cáo Thống kê Doanh thu & AOV -->
  <g transform="translate(1030, 700)">
    <ellipse cx="0" cy="0" rx="145" ry="26" fill="url(#ucGradManager)" stroke="#CB4335" stroke-width="2.0"/>
    <text x="0" y="-3" class="uc-text">Báo cáo Doanh thu &amp; AOV</text>
    <text x="0" y="12" class="uc-code">UC-16 | /admin/revenue (QuanLy)</text>
  </g>

  <!-- UC17: Quản lý Mã giảm giá & Nhân sự -->
  <g transform="translate(1030, 775)">
    <ellipse cx="0" cy="0" rx="145" ry="25" fill="url(#ucGradManager)" stroke="#CB4335" stroke-width="1.8"/>
    <text x="0" y="-3" class="uc-text">Quản lý Mã giảm giá &amp; Nhân sự</text>
    <text x="0" y="12" class="uc-code">UC-17 | MA_GIAM_GIA, NHAN_VIEN</text>
  </g>


  <!-- ==================== ACTORS (STICK FIGURES) ==================== -->

  <!-- 1. ACTOR: Khách vãng lai (Guest) -->
  <g transform="translate(100, 260)">
    <!-- Head -->
    <circle cx="0" cy="-35" r="16" fill="#FFFFFF" stroke="#14221D" stroke-width="2.4"/>
    <!-- Body -->
    <line x1="0" y1="-19" x2="0" y2="22" stroke="#14221D" stroke-width="2.4"/>
    <!-- Arms -->
    <line x1="-24" y1="-6" x2="24" y2="-6" stroke="#14221D" stroke-width="2.4"/>
    <!-- Left Leg -->
    <line x1="0" y1="22" x2="-18" y2="60" stroke="#14221D" stroke-width="2.4"/>
    <!-- Right Leg -->
    <line x1="0" y1="22" x2="18" y2="60" stroke="#14221D" stroke-width="2.4"/>
    <!-- Text -->
    <text x="0" y="85" class="actor-name">Khách vãng lai</text>
    <text x="0" y="100" class="actor-role">(Guest)</text>
  </g>

  <!-- 2. ACTOR: Khách thành viên (Customer) -->
  <g transform="translate(100, 700)">
    <!-- Head -->
    <circle cx="0" cy="-35" r="16" fill="#FFFFFF" stroke="#16A085" stroke-width="2.4"/>
    <!-- Body -->
    <line x1="0" y1="-19" x2="0" y2="22" stroke="#16A085" stroke-width="2.4"/>
    <!-- Arms -->
    <line x1="-24" y1="-6" x2="24" y2="-6" stroke="#16A085" stroke-width="2.4"/>
    <!-- Left Leg -->
    <line x1="0" y1="22" x2="-18" y2="60" stroke="#16A085" stroke-width="2.4"/>
    <!-- Right Leg -->
    <line x1="0" y1="22" x2="18" y2="60" stroke="#16A085" stroke-width="2.4"/>
    <!-- Text -->
    <text x="0" y="85" class="actor-name" fill="#16A085">Khách thành viên</text>
    <text x="0" y="100" class="actor-role">(Customer / Member)</text>
  </g>

  <!-- Inheritance: Customer -> Guest -->
  <line x1="100" y1="640" x2="100" y2="385" class="inherit-line" marker-end="url(#generalization)"/>
  <rect x="55" y="495" width="90" height="20" rx="4" fill="#FFFFFF" stroke="#BDC3C7" stroke-width="1"/>
  <text x="100" y="509" text-anchor="middle" font-family="'Segoe UI', Arial, sans-serif" font-size="10px" font-weight="bold" fill="#14221D">kế thừa quyền</text>

  <!-- 3. ACTOR: Nhân viên bán hàng (Staff) -->
  <g transform="translate(1420, 270)">
    <!-- Head -->
    <circle cx="0" cy="-35" r="16" fill="#FFFFFF" stroke="#D4AC0D" stroke-width="2.4"/>
    <!-- Body -->
    <line x1="0" y1="-19" x2="0" y2="22" stroke="#D4AC0D" stroke-width="2.4"/>
    <!-- Arms -->
    <line x1="-24" y1="-6" x2="24" y2="-6" stroke="#D4AC0D" stroke-width="2.4"/>
    <!-- Left Leg -->
    <line x1="0" y1="22" x2="-18" y2="60" stroke="#D4AC0D" stroke-width="2.4"/>
    <!-- Right Leg -->
    <line x1="0" y1="22" x2="18" y2="60" stroke="#D4AC0D" stroke-width="2.4"/>
    <!-- Text -->
    <text x="0" y="85" class="actor-name" fill="#B7950B">Nhân viên bán hàng</text>
    <text x="0" y="100" class="actor-role">(Staff)</text>
  </g>

  <!-- 4. ACTOR: Quản lý cửa hàng (Manager) -->
  <g transform="translate(1420, 720)">
    <!-- Head -->
    <circle cx="0" cy="-35" r="16" fill="#FFFFFF" stroke="#CB4335" stroke-width="2.6"/>
    <!-- Body -->
    <line x1="0" y1="-19" x2="0" y2="22" stroke="#CB4335" stroke-width="2.6"/>
    <!-- Arms -->
    <line x1="-24" y1="-6" x2="24" y2="-6" stroke="#CB4335" stroke-width="2.6"/>
    <!-- Left Leg -->
    <line x1="0" y1="22" x2="-18" y2="60" stroke="#CB4335" stroke-width="2.6"/>
    <!-- Right Leg -->
    <line x1="0" y1="22" x2="18" y2="60" stroke="#CB4335" stroke-width="2.6"/>
    <!-- Text -->
    <text x="0" y="85" class="actor-name" fill="#CB4335">Quản lý cửa hàng</text>
    <text x="0" y="100" class="actor-role">(Manager)</text>
  </g>

  <!-- Inheritance: Manager -> Staff -->
  <line x1="1420" y1="660" x2="1420" y2="395" class="inherit-line" marker-end="url(#generalization)"/>
  <rect x="1375" y="510" width="90" height="20" rx="4" fill="#FFFFFF" stroke="#BDC3C7" stroke-width="1"/>
  <text x="1420" y="524" text-anchor="middle" font-family="'Segoe UI', Arial, sans-serif" font-size="10px" font-weight="bold" fill="#CB4335">kế thừa quyền</text>


  <!-- ==================== ASSOCIATIONS (LINES) ==================== -->
  
  <!-- Guest associations -->
  <path d="M 130 260 L 365 180" class="assoc-line"/>
  <path d="M 130 260 L 355 245" class="assoc-line"/>
  <path d="M 130 260 L 360 310" class="assoc-line"/>
  <path d="M 130 260 L 365 375" class="assoc-line"/>
  <path d="M 130 260 L 355 440" class="assoc-line"/>
  <path d="M 130 260 L 365 505" class="assoc-line"/>

  <!-- Customer associations -->
  <path d="M 130 700 L 360 615" class="assoc-line" stroke="#16A085"/>
  <path d="M 130 700 L 360 680" class="assoc-line" stroke="#16A085"/>
  <path d="M 130 700 L 355 745" class="assoc-line" stroke="#16A085"/>
  <path d="M 130 700 L 355 810" class="assoc-line" stroke="#16A085"/>

  <!-- Staff associations -->
  <path d="M 1390 270 L 1165 185" class="assoc-line" stroke="#B7950B"/>
  <path d="M 1390 270 L 1175 255" class="assoc-line" stroke="#B7950B"/>
  <path d="M 1390 270 L 1165 325" class="assoc-line" stroke="#B7950B"/>
  <path d="M 1390 270 L 1175 395" class="assoc-line" stroke="#B7950B"/>
  <path d="M 1390 270 L 1170 465" class="assoc-line" stroke="#B7950B"/>

  <!-- Manager associations -->
  <path d="M 1390 720 L 1175 700" class="assoc-line" stroke="#CB4335" stroke-width="1.8"/>
  <path d="M 1390 720 L 1175 775" class="assoc-line" stroke="#CB4335" stroke-width="1.8"/>

  <!-- Legend / Footnote -->
  <rect x="230" y="960" width="1060" height="35" rx="6" fill="#F2F4F4"/>
  <text x="760" y="982" text-anchor="middle" font-family="'Segoe UI', Arial, sans-serif" font-size="11.5px" fill="#5D6D7E">
    Ghi chú: Đường nét đứt có mũi tên rỗng (──▷) biểu diễn quan hệ Kế thừa (Generalization). Đường liền (──) biểu diễn quan hệ Tham gia (Association).
  </text>
</svg>
"""
    svg_path = Path(r"D:\PHP_NOSQL\SMART_CASUAL_LARAVEL\docs\figures\hinh_2_1_uml_use_case.svg")
    svg_path.write_text(svg_content, encoding="utf-8")
    print(f"Đã tạo tệp Vector SVG: {svg_path}")
    return svg_path

if __name__ == "__main__":
    create_svg()
