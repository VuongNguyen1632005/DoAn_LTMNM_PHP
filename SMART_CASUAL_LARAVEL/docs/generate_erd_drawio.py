"""
Tệp: generate_erd_drawio.py
Chức năng: Tạo tệp Draw.io XML, Vector SVG và ảnh PNG cho Hình 2.2 Sơ đồ ERD 13 bảng nghiệp vụ Smart Casual
"""

import os
import subprocess
from pathlib import Path
import sys

sys.stdout.reconfigure(encoding='utf-8')

def build_erd_drawio_xml():
    # Cấu trúc XML chuẩn Draw.io
    xml = """<mxfile host="app.diagrams.net" modified="2026-10-26T22:45:00.000Z" agent="Antigravity" version="24.7.5" type="device">
  <diagram id="SmartCasual_ERD" name="Sơ đồ ERD 13 Bảng Nghiệp Vụ">
    <mxGraphModel dx="2000" dy="1400" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="2200" pageHeight="1500" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />

        <!-- Tiêu đề sơ đồ -->
        <mxCell id="erd_title" value="&lt;b&gt;HỆ THỐNG THƯƠNG MẠI ĐIỆN TỬ SMART CASUAL&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 14px; font-weight: normal; color: #555555;&quot;&gt;SƠ ĐỒ THỰC THỂ LIÊN KẾT (ERD) CHI TIẾT 13 BẢNG NGHIỆP VỤ &amp;amp; RÀNG BUỘC TOÀN VẸN 3NF&lt;/font&gt;" style="text;html=1;align=center;verticalAlign=middle;resizable=0;points=[];autosize=1;strokeColor=none;fillColor=none;fontSize=18;fontColor=#14221D;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="700" y="25" width="800" height="50" as="geometry" />
        </mxCell>

        <!-- ==================== KHU VỰC 3 PHÂN HỆ ==================== -->
        <!-- 1. Phân hệ Người dùng -->
        <mxCell id="zone_users" value="&lt;b&gt;I. PHÂN HỆ TÀI KHOẢN &amp;amp; NGƯỜI DÙNG&lt;/b&gt;" style="shape=rect;html=1;whiteSpace=wrap;dashed=1;strokeColor=#16A085;strokeWidth=1.8;fillColor=#F2FAF7;verticalAlign=top;align=left;spacingLeft=15;spacingTop=10;rounded=1;fontColor=#0E6251;fontSize=13;" vertex="1" parent="1">
          <mxGeometry x="50" y="90" width="460" height="740" as="geometry" />
        </mxCell>

        <!-- 2. Phân hệ Hàng hóa -->
        <mxCell id="zone_catalog" value="&lt;b&gt;II. PHÂN HỆ SẢN PHẨM &amp;amp; BIẾN THỂ KÍCH CỠ&lt;/b&gt;" style="shape=rect;html=1;whiteSpace=wrap;dashed=1;strokeColor=#2980B9;strokeWidth=1.8;fillColor=#F4F9FD;verticalAlign=top;align=left;spacingLeft=15;spacingTop=10;rounded=1;fontColor=#1B4F72;fontSize=13;" vertex="1" parent="1">
          <mxGeometry x="540" y="90" width="970" height="600" as="geometry" />
        </mxCell>

        <!-- 3. Phân hệ Giao dịch & Đơn hàng -->
        <mxCell id="zone_orders" value="&lt;b&gt;III. PHÂN HỆ GIỎ HÀNG, ĐƠN HÀNG &amp;amp; ĐÁNH GIÁ (GIAO DỊCH)&lt;/b&gt;" style="shape=rect;html=1;whiteSpace=wrap;dashed=1;strokeColor=#D4AC0D;strokeWidth=1.8;fillColor=#FEFDF4;verticalAlign=top;align=left;spacingLeft=15;spacingTop=10;rounded=1;fontColor=#7D6608;fontSize=13;" vertex="1" parent="1">
          <mxGeometry x="50" y="710" width="1460" height="690" as="geometry" />
        </mxCell>


        <!-- ==================== BẢNG THỰC THỂ ==================== -->

        <!-- 1. NGUOI_DUNG -->
        <mxCell id="tbl_nguoi_dung" value="&lt;b&gt;NGUOI_DUNG&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 10px; font-weight: normal; color: #555555;&quot;&gt;(Tài khoản định danh cơ sở)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; padding-left: 6px;&quot;&gt;&lt;b&gt;PK&lt;/b&gt; MaNguoiDung : varchar(20)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TenDangNhap : varchar(100) &lt;i&gt;[UK]&lt;/i&gt;&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;MatKhau : varchar(255)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;Email : varchar(100) &lt;i&gt;[UK]&lt;/i&gt;&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;SoDienThoai : varchar(20)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;VaiTro : varchar(50)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TrangThai : boolean&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;created_at / updated_at&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=35;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#E8F8F5;strokeColor=#16A085;strokeWidth=1.8;fontColor=#14221D;fontSize=12;rounded=1;arcSize=4;" vertex="1" parent="1">
          <mxGeometry x="140" y="140" width="280" height="190" as="geometry" />
        </mxCell>

        <!-- 2. KHACH_HANG -->
        <mxCell id="tbl_khach_hang" value="&lt;b&gt;KHACH_HANG&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 10px; font-weight: normal; color: #555555;&quot;&gt;(Thông tin Khách hàng - guard: web)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; padding-left: 6px;&quot;&gt;&lt;b&gt;PK, FK&lt;/b&gt; MaKH : varchar(20)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TenDangNhap : varchar(100)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;HoTen : varchar(100)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;SoDienThoai : varchar(20)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;Email : varchar(100)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;DiaChi : varchar(255)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;DiemTichLuy : int default 0&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TrangThai : boolean&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=35;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#E8F8F5;strokeColor=#16A085;strokeWidth=1.8;fontColor=#14221D;fontSize=12;rounded=1;arcSize=4;" vertex="1" parent="1">
          <mxGeometry x="70" y="410" width="250" height="200" as="geometry" />
        </mxCell>

        <!-- 3. NHAN_VIEN -->
        <mxCell id="tbl_nhan_vien" value="&lt;b&gt;NHAN_VIEN&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 10px; font-weight: normal; color: #555555;&quot;&gt;(Nhân sự - guard: admin)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; padding-left: 6px;&quot;&gt;&lt;b&gt;PK, FK&lt;/b&gt; MaNV : varchar(20)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TenDangNhap : varchar(100)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;HoTen : varchar(100)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;ChucVu : varchar(50) &lt;i&gt;['staff'/'manager']&lt;/i&gt;&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;Luong : decimal(18,2)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TrangThai : boolean&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=35;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#E8F8F5;strokeColor=#16A085;strokeWidth=1.8;fontColor=#14221D;fontSize=12;rounded=1;arcSize=4;" vertex="1" parent="1">
          <mxGeometry x="340" y="410" width="240" height="170" as="geometry" />
        </mxCell>

        <!-- 4. DANH_MUC -->
        <mxCell id="tbl_danh_muc" value="&lt;b&gt;DANH_MUC&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 10px; font-weight: normal; color: #555555;&quot;&gt;(Phân loại thời trang)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; padding-left: 6px;&quot;&gt;&lt;b&gt;PK&lt;/b&gt; MaDanhMuc : varchar(10)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TenDanhMuc : varchar(100)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TrangThai : boolean&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;created_at / updated_at&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=35;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#EBF5FB;strokeColor=#2980B9;strokeWidth=1.8;fontColor=#14221D;fontSize=12;rounded=1;arcSize=4;" vertex="1" parent="1">
          <mxGeometry x="610" y="140" width="220" height="130" as="geometry" />
        </mxCell>

        <!-- 5. KICH_THUOC -->
        <mxCell id="tbl_kich_thuoc" value="&lt;b&gt;KICH_THUOC&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 10px; font-weight: normal; color: #555555;&quot;&gt;(Bảng chuẩn S, M, L, XL)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; padding-left: 6px;&quot;&gt;&lt;b&gt;PK&lt;/b&gt; MaKichThuoc : varchar(10)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TenKichThuoc : varchar(50)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TrangThai : boolean&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=35;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#EBF5FB;strokeColor=#2980B9;strokeWidth=1.8;fontColor=#14221D;fontSize=12;rounded=1;arcSize=4;" vertex="1" parent="1">
          <mxGeometry x="1250" y="140" width="210" height="110" as="geometry" />
        </mxCell>

        <!-- 6. SAN_PHAM -->
        <mxCell id="tbl_san_pham" value="&lt;b&gt;SAN_PHAM&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 10px; font-weight: normal; color: #555555;&quot;&gt;(Thông tin sản phẩm chính)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; padding-left: 6px;&quot;&gt;&lt;b&gt;PK&lt;/b&gt; MaSP : varchar(20)&lt;br&gt;&lt;b&gt;FK&lt;/b&gt; MaDanhMuc : varchar(10)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TenSanPham : varchar(200)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;GiaGoc : decimal(18,2)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;GiaKhuyenMai : decimal(18,2)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;ChatLieu : varchar(100)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;MoTa : text&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TrangThai : boolean &lt;i&gt;[Ẩn SP]&lt;/i&gt;&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=35;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#EBF5FB;strokeColor=#2980B9;strokeWidth=2;fontColor=#14221D;fontSize=12;rounded=1;arcSize=4;" vertex="1" parent="1">
          <mxGeometry x="600" y="360" width="240" height="200" as="geometry" />
        </mxCell>

        <!-- 7. CHI_TIET_SP -->
        <mxCell id="tbl_chi_tiet_sp" value="&lt;b&gt;CHI_TIET_SP&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 10px; font-weight: normal; color: #555555;&quot;&gt;(Biến thể SKU &amp;amp; Tồn kho)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; padding-left: 6px;&quot;&gt;&lt;b&gt;PK&lt;/b&gt; MaCTSP : varchar(30)&lt;br&gt;&lt;b&gt;FK&lt;/b&gt; MaSP : varchar(20)&lt;br&gt;&lt;b&gt;FK&lt;/b&gt; MaKichThuoc : varchar(10)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;GiaBan : decimal(18,2)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;SoLuong : int &lt;i&gt;[Tồn kho lockForUpdate]&lt;/i&gt;&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TrangThai : boolean&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=35;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#EBF5FB;strokeColor=#2980B9;strokeWidth=2;fontColor=#14221D;fontSize=12;rounded=1;arcSize=4;" vertex="1" parent="1">
          <mxGeometry x="930" y="360" width="250" height="180" as="geometry" />
        </mxCell>

        <!-- 8. HINH_ANH_SP -->
        <mxCell id="tbl_hinh_anh_sp" value="&lt;b&gt;HINH_ANH_SP&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 10px; font-weight: normal; color: #555555;&quot;&gt;(Ảnh sản phẩm - Laravel Storage)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; padding-left: 6px;&quot;&gt;&lt;b&gt;PK&lt;/b&gt; MaHinhAnh : varchar(30)&lt;br&gt;&lt;b&gt;FK&lt;/b&gt; MaSP : varchar(20)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;DuongDan : varchar(255)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;LaAnhChinh : boolean&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TrangThai : boolean&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=35;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#EBF5FB;strokeColor=#2980B9;strokeWidth=1.8;fontColor=#14221D;fontSize=12;rounded=1;arcSize=4;" vertex="1" parent="1">
          <mxGeometry x="1230" y="375" width="240" height="150" as="geometry" />
        </mxCell>

        <!-- 9. GIO_HANG -->
        <mxCell id="tbl_gio_hang" value="&lt;b&gt;GIO_HANG&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 10px; font-weight: normal; color: #555555;&quot;&gt;(Khóa chính phức hợp 2 cột)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; padding-left: 6px;&quot;&gt;&lt;b&gt;PK, FK&lt;/b&gt; MaKH : varchar(20)&lt;br&gt;&lt;b&gt;PK, FK&lt;/b&gt; MaCTSP : varchar(30)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;SoLuong : int default 1&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;created_at / updated_at&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=35;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#FEF9E7;strokeColor=#D4AC0D;strokeWidth=1.8;fontColor=#14221D;fontSize=12;rounded=1;arcSize=4;" vertex="1" parent="1">
          <mxGeometry x="80" y="780" width="230" height="130" as="geometry" />
        </mxCell>

        <!-- 10. MA_GIAM_GIA -->
        <mxCell id="tbl_ma_giam_gia" value="&lt;b&gt;MA_GIAM_GIA&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 10px; font-weight: normal; color: #555555;&quot;&gt;(Vouchers khuyến mãi)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; padding-left: 6px;&quot;&gt;&lt;b&gt;PK&lt;/b&gt; MaCode : varchar(50)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;LoaiGiamGia : varchar(20)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;GiaTri : decimal(18,2)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;DonHangToiThieu : decimal(18,2)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;SoLuong : int &lt;i&gt;[Hoàn lượt khi hủy]&lt;/i&gt;&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TrangThai : boolean&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=35;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#FEF9E7;strokeColor=#D4AC0D;strokeWidth=1.8;fontColor=#14221D;fontSize=12;rounded=1;arcSize=4;" vertex="1" parent="1">
          <mxGeometry x="360" y="780" width="250" height="160" as="geometry" />
        </mxCell>

        <!-- 11. PHUONG_THUC_VAN_CHUYEN -->
        <mxCell id="tbl_ptvc" value="&lt;b&gt;PHUONG_THUC_VAN_CHUYEN&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 10px; font-weight: normal; color: #555555;&quot;&gt;(Hình thức giao hàng)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; padding-left: 6px;&quot;&gt;&lt;b&gt;PK&lt;/b&gt; MaPTVC : varchar(20)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TenPTVC : varchar(100)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;GiaVanChuyen : decimal(18,2)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TrangThai : boolean&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=35;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#FEF9E7;strokeColor=#D4AC0D;strokeWidth=1.8;fontColor=#14221D;fontSize=12;rounded=1;arcSize=4;" vertex="1" parent="1">
          <mxGeometry x="360" y="980" width="250" height="130" as="geometry" />
        </mxCell>

        <!-- 12. DON_HANG -->
        <mxCell id="tbl_don_hang" value="&lt;b&gt;DON_HANG&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 10px; font-weight: normal; color: #555555;&quot;&gt;(Phi chuẩn hóa có chủ đích - Snapshot tài chính)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; padding-left: 6px;&quot;&gt;&lt;b&gt;PK&lt;/b&gt; MaDonHang : varchar(30)&lt;br&gt;&lt;b&gt;FK&lt;/b&gt; MaKH : varchar(20) &lt;i&gt;[nullable - khách vãng lai]&lt;/i&gt;&lt;br&gt;&lt;b&gt;FK&lt;/b&gt; MaNV : varchar(20) &lt;i&gt;[nullable]&lt;/i&gt;&lt;br&gt;&lt;b&gt;FK&lt;/b&gt; MaPTVC : varchar(20)&lt;br&gt;&lt;b&gt;FK&lt;/b&gt; MaCode : varchar(50) &lt;i&gt;[nullable]&lt;/i&gt;&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TenNguoiNhan : varchar(100)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;SoDienThoai : varchar(20)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;DiaChiGiao : varchar(255)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;NgayDat : datetime&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&lt;b&gt;TongSoLuong&lt;/b&gt; : int &lt;i&gt;[Trigger CSDL]&lt;/i&gt;&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&lt;b&gt;TongTien&lt;/b&gt; : decimal(18,2) &lt;i&gt;[Trigger CSDL]&lt;/i&gt;&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;GiamGia : decimal(18,2)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;PhiVanChuyen : decimal(18,2)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&lt;b&gt;ThanhTien&lt;/b&gt; : decimal(18,2) &lt;i&gt;[Trigger CSDL]&lt;/i&gt;&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TrangThaiDonHang : varchar(30) &lt;i&gt;[ALLOWED_TRANSITIONS]&lt;/i&gt;&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;PhuongThucThanhToan : varchar(30) &lt;i&gt;[COD / Bank]&lt;/i&gt;&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;DaThanhToan : boolean&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=35;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#FDEDEC;strokeColor=#CB4335;strokeWidth=2.2;fontColor=#14221D;fontSize=12;rounded=1;arcSize=4;" vertex="1" parent="1">
          <mxGeometry x="690" y="760" width="350" height="360" as="geometry" />
        </mxCell>

        <!-- 13. CHI_TIET_DON_HANG -->
        <mxCell id="tbl_ctdh" value="&lt;b&gt;CHI_TIET_DON_HANG&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 10px; font-weight: normal; color: #555555;&quot;&gt;(Dòng sản phẩm xuất kho - Khóa kép)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; padding-left: 6px;&quot;&gt;&lt;b&gt;PK, FK&lt;/b&gt; MaDonHang : varchar(30)&lt;br&gt;&lt;b&gt;PK, FK&lt;/b&gt; MaCTSP : varchar(30)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;SoLuong : int default 1&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;DonGia : decimal(18,2)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&lt;b&gt;ThanhTien&lt;/b&gt; : decimal(18,2) &lt;i&gt;[SoLuong * DonGia]&lt;/i&gt;&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TrangThai : boolean&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;created_at / updated_at&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=35;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#FEF9E7;strokeColor=#D4AC0D;strokeWidth=2;fontColor=#14221D;fontSize=12;rounded=1;arcSize=4;" vertex="1" parent="1">
          <mxGeometry x="1130" y="760" width="310" height="180" as="geometry" />
        </mxCell>

        <!-- 14. DANH_GIA -->
        <mxCell id="tbl_danh_gia" value="&lt;b&gt;DANH_GIA&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 10px; font-weight: normal; color: #555555;&quot;&gt;(Phi chuẩn hóa TenKhachHang cho khách vãng lai)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; padding-left: 6px;&quot;&gt;&lt;b&gt;PK&lt;/b&gt; MaDanhGia : varchar(30)&lt;br&gt;&lt;b&gt;FK&lt;/b&gt; MaSP : varchar(20)&lt;br&gt;&lt;b&gt;FK&lt;/b&gt; MaKH : varchar(20) &lt;i&gt;[nullable]&lt;/i&gt;&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&lt;b&gt;TenKhachHang&lt;/b&gt; : varchar(100)&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;SoSao : tinyint &lt;i&gt;[1..5 sao]&lt;/i&gt;&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;NoiDung : text&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;TrangThai : boolean &lt;i&gt;[Kiểm duyệt]&lt;/i&gt;&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=35;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#FEF9E7;strokeColor=#D4AC0D;strokeWidth=1.8;fontColor=#14221D;fontSize=12;rounded=1;arcSize=4;" vertex="1" parent="1">
          <mxGeometry x="1130" y="1000" width="310" height="190" as="geometry" />
        </mxCell>


        <!-- ==================== CÁC MỐI QUAN HỆ (RELATIONSHIPS) ==================== -->

        <!-- NGUOI_DUNG - KHACH_HANG (1:1) -->
        <mxCell id="rel_nd_kh" value="1 : 1 (Chuyên biệt hóa)" style="endArrow=ERone;startArrow=ERone;html=1;strokeWidth=1.6;strokeColor=#16A085;fontColor=#0E6251;fontSize=10;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="tbl_nguoi_dung" target="tbl_khach_hang">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="200" y="370" />
              <mxPoint x="200" y="370" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- NGUOI_DUNG - NHAN_VIEN (1:1) -->
        <mxCell id="rel_nd_nv" value="1 : 1 (Chuyên biệt hóa)" style="endArrow=ERone;startArrow=ERone;html=1;strokeWidth=1.6;strokeColor=#16A085;fontColor=#0E6251;fontSize=10;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="tbl_nguoi_dung" target="tbl_nhan_vien">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="380" y="370" />
              <mxPoint x="430" y="370" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- DANH_MUC - SAN_PHAM (1:N) -->
        <mxCell id="rel_dm_sp" value="1 : N" style="endArrow=ERmany;startArrow=ERone;html=1;strokeWidth=1.8;strokeColor=#2980B9;fontColor=#1B4F72;fontSize=11;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="tbl_danh_muc" target="tbl_san_pham">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- SAN_PHAM - CHI_TIET_SP (1:N) -->
        <mxCell id="rel_sp_ctsp" value="1 : N" style="endArrow=ERmany;startArrow=ERone;html=1;strokeWidth=2;strokeColor=#2980B9;fontColor=#1B4F72;fontSize=11;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="tbl_san_pham" target="tbl_chi_tiet_sp">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- KICH_THUOC - CHI_TIET_SP (1:N) -->
        <mxCell id="rel_kt_ctsp" value="1 : N" style="endArrow=ERmany;startArrow=ERone;html=1;strokeWidth=1.8;strokeColor=#2980B9;fontColor=#1B4F72;fontSize=11;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="tbl_kich_thuoc" target="tbl_chi_tiet_sp">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="1350" y="300" />
              <mxPoint x="1080" y="300" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- SAN_PHAM - HINH_ANH_SP (1:N) -->
        <mxCell id="rel_sp_ha" value="1 : N" style="endArrow=ERmany;startArrow=ERone;html=1;strokeWidth=1.6;strokeColor=#2980B9;fontColor=#1B4F72;fontSize=11;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="tbl_san_pham" target="tbl_hinh_anh_sp">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="720" y="590" />
              <mxPoint x="1350" y="590" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- KHACH_HANG - GIO_HANG (1:N) -->
        <mxCell id="rel_kh_gh" value="1 : N" style="endArrow=ERmany;startArrow=ERone;html=1;strokeWidth=1.8;strokeColor=#16A085;fontColor=#0E6251;fontSize=11;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="tbl_khach_hang" target="tbl_gio_hang">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- CHI_TIET_SP - GIO_HANG (1:N) -->
        <mxCell id="rel_ctsp_gh" value="1 : N" style="endArrow=ERmany;startArrow=ERone;html=1;strokeWidth=1.6;strokeColor=#2980B9;fontColor=#1B4F72;fontSize=11;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="tbl_chi_tiet_sp" target="tbl_gio_hang">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="1000" y="650" />
              <mxPoint x="200" y="650" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- KHACH_HANG - DON_HANG (0..1:N) -->
        <mxCell id="rel_kh_dh" value="0..1 : N (Khách vãng lai NULL)" style="endArrow=ERmany;startArrow=ERzeroToOne;html=1;strokeWidth=1.8;strokeColor=#16A085;fontColor=#0E6251;fontSize=10;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="tbl_khach_hang" target="tbl_don_hang">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="230" y="670" />
              <mxPoint x="750" y="670" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- NHAN_VIEN - DON_HANG (0..1:N) -->
        <mxCell id="rel_nv_dh" value="0..1 : N (Duyệt đơn)" style="endArrow=ERmany;startArrow=ERzeroToOne;html=1;strokeWidth=1.6;strokeColor=#16A085;fontColor=#0E6251;fontSize=10;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="tbl_nhan_vien" target="tbl_don_hang">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="480" y="685" />
              <mxPoint x="830" y="685" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- MA_GIAM_GIA - DON_HANG (0..1:N) -->
        <mxCell id="rel_mgg_dh" value="0..1 : N (Voucher)" style="endArrow=ERmany;startArrow=ERzeroToOne;html=1;strokeWidth=1.6;strokeColor=#D4AC0D;fontColor=#7D6608;fontSize=10;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="tbl_ma_giam_gia" target="tbl_don_hang">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- PTVC - DON_HANG (1:N) -->
        <mxCell id="rel_ptvc_dh" value="1 : N (Phí ship)" style="endArrow=ERmany;startArrow=ERone;html=1;strokeWidth=1.6;strokeColor=#D4AC0D;fontColor=#7D6608;fontSize=10;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="tbl_ptvc" target="tbl_don_hang">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- DON_HANG - CHI_TIET_DON_HANG (1:N) -->
        <mxCell id="rel_dh_ctdh" value="1 : N (CASCADE)" style="endArrow=ERmany;startArrow=ERmandOne;html=1;strokeWidth=2.2;strokeColor=#CB4335;fontColor=#78281F;fontSize=11;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="tbl_don_hang" target="tbl_ctdh">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- CHI_TIET_SP - CHI_TIET_DON_HANG (1:N) -->
        <mxCell id="rel_ctsp_ctdh" value="1 : N (Xuất kho)" style="endArrow=ERmany;startArrow=ERone;html=1;strokeWidth=1.8;strokeColor=#2980B9;fontColor=#1B4F72;fontSize=10;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="tbl_chi_tiet_sp" target="tbl_ctdh">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="1100" y="600" />
              <mxPoint x="1280" y="600" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- SAN_PHAM - DANH_GIA (1:N) -->
        <mxCell id="rel_sp_dg" value="1 : N" style="endArrow=ERmany;startArrow=ERone;html=1;strokeWidth=1.6;strokeColor=#2980B9;fontColor=#1B4F72;fontSize=10;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="tbl_san_pham" target="tbl_danh_gia">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="650" y="630" />
              <mxPoint x="1070" y="630" />
              <mxPoint x="1070" y="1120" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- KHACH_HANG - DANH_GIA (0..1:N) -->
        <mxCell id="rel_kh_dg" value="0..1 : N (Khách vãng lai NULL)" style="endArrow=ERmany;startArrow=ERzeroToOne;html=1;strokeWidth=1.4;strokeColor=#16A085;fontColor=#0E6251;fontSize=10;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="tbl_khach_hang" target="tbl_danh_gia">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="100" y="660" />
              <mxPoint x="50" y="660" />
              <mxPoint x="50" y="1280" />
              <mxPoint x="1240" y="1280" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- Bảng chú thích (Legend) -->
        <mxCell id="legend_box" value="&lt;b&gt;QUY ƯỚC KÝ HIỆU &amp;amp; QUAN HỆ TRONG ERD:&lt;/b&gt;&lt;br&gt;• &lt;b&gt;PK&lt;/b&gt;: Primary Key (Khóa chính) | &lt;b&gt;FK&lt;/b&gt;: Foreign Key (Khóa ngoại) | &lt;b&gt;UK&lt;/b&gt;: Unique Key (Duy nhất)&lt;br&gt;• &lt;b&gt;Quan hệ 1 : N (Crow's Foot)&lt;/b&gt;: Đầu 1 vạch (1:1), Đầu 3 nhánh (1:N) | &lt;b&gt;0..1 : N&lt;/b&gt;: Đầu tròn rỗng cho phép nhận giá trị NULL (Khách vãng lai, Chưa gán NV)&lt;br&gt;• &lt;b&gt;Bảng phi chuẩn hóa có chủ đích&lt;/b&gt;: &lt;i&gt;DON_HANG&lt;/i&gt; (lưu snapshot TongSoLuong, TongTien, ThanhTien qua Trigger) và &lt;i&gt;DANH_GIA&lt;/i&gt; (lưu TenKhachHang)." style="shape=rect;html=1;whiteSpace=wrap;dashed=0;strokeColor=#BDC3C7;strokeWidth=1.2;fillColor=#FFFFFF;verticalAlign=middle;align=left;spacingLeft=15;rounded=1;fontSize=11;fontColor=#333333;" vertex="1" parent="1">
          <mxGeometry x="50" y="1415" width="1460" height="65" as="geometry" />
        </mxCell>

      </root>
    </mxGraphModel>
  </diagram>
</mxfile>"""
    out1 = Path(r"D:\PHP_NOSQL\hinh_2_2_erd_13_bang.drawio")
    out1.write_text(xml, encoding="utf-8")
    
    out2 = Path(r"D:\PHP_NOSQL\SMART_CASUAL_LARAVEL\docs\figures\hinh_2_2_erd_13_bang.drawio")
    out2.write_text(xml, encoding="utf-8")
    print(f"Đã tạo tệp Draw.io ERD thành công: {out1} và {out2}")

if __name__ == "__main__":
    build_erd_drawio_xml()
