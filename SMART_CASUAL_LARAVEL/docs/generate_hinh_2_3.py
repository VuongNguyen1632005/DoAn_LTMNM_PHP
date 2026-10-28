import os
import sys
import subprocess
from pathlib import Path

# Đảm bảo in tiếng Việt không lỗi encoding trên console
if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8')

DRAWIO_CONTENT = """<mxfile host="app.diagrams.net" modified="2026-10-26T23:05:00.000Z" agent="Antigravity" version="24.7.5" type="device">
  <diagram id="Checkout_Service_Triggers_Seq" name="Hinh 2.3 - CheckoutService & Triggers Sequence">
    <mxGraphModel dx="1800" dy="1200" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1800" pageHeight="1400" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />

        <!-- Tiêu đề sơ đồ -->
        <mxCell id="title_box" value="&lt;b&gt;HỆ THỐNG THƯƠNG MẠI ĐIỆN TỬ SMART CASUAL&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 15px; font-weight: normal; color: #555555;&quot;&gt;HÌNH 2.3: BIỂU ĐỒ TUẦN TỰ (SEQUENCE DIAGRAM) PHỐI HỢP GIỮA SERVICE LAYER VÀ DATABASE TRIGGERS&lt;/font&gt;" style="text;html=1;align=center;verticalAlign=middle;resizable=0;points=[];autosize=1;strokeColor=none;fillColor=none;fontSize=18;fontColor=#14221D;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="500" y="20" width="800" height="50" as="geometry" />
        </mxCell>

        <!-- ==================== PARTICIPANTS (LIFELINES) ==================== -->

        <!-- 1. Khách hàng (Client) -->
        <mxCell id="p_client" value="&lt;b&gt;Khách hàng&lt;/b&gt;&lt;br&gt;(Trình duyệt / Client)" style="shape=umlLifeline;perimeter=lifelinePerimeter;whiteSpace=wrap;html=1;container=1;collapsible=0;recursiveResize=0;outlineConnect=0;size=45;fillColor=#FFF3E0;strokeColor=#E65100;strokeWidth=1.8;fontColor=#BF360C;fontSize=12;" vertex="1" parent="1">
          <mxGeometry x="80" y="90" width="150" height="1180" as="geometry" />
        </mxCell>

        <!-- 2. CheckoutController -->
        <mxCell id="p_controller" value="&lt;b&gt;CheckoutController&lt;/b&gt;&lt;br&gt;(Tầng Controller)" style="shape=umlLifeline;perimeter=lifelinePerimeter;whiteSpace=wrap;html=1;container=1;collapsible=0;recursiveResize=0;outlineConnect=0;size=45;fillColor=#E8F8F5;strokeColor=#16A085;strokeWidth=1.8;fontColor=#0E6251;fontSize=12;" vertex="1" parent="1">
          <mxGeometry x="370" y="90" width="160" height="1180" as="geometry" />
        </mxCell>

        <!-- 3. CheckoutService -->
        <mxCell id="p_service" value="&lt;b&gt;CheckoutService&lt;/b&gt;&lt;br&gt;(Tầng Dịch Vụ - App Core)" style="shape=umlLifeline;perimeter=lifelinePerimeter;whiteSpace=wrap;html=1;container=1;collapsible=0;recursiveResize=0;outlineConnect=0;size=45;fillColor=#EBF5FB;strokeColor=#2980B9;strokeWidth=2;fontColor=#1B4F72;fontSize=12;" vertex="1" parent="1">
          <mxGeometry x="680" y="90" width="180" height="1180" as="geometry" />
        </mxCell>

        <!-- 4. MySQL Engine & Triggers -->
        <mxCell id="p_triggers" value="&lt;b&gt;MySQL 8.4 Engine&lt;/b&gt;&lt;br&gt;(4 Triggers CSDL)" style="shape=umlLifeline;perimeter=lifelinePerimeter;whiteSpace=wrap;html=1;container=1;collapsible=0;recursiveResize=0;outlineConnect=0;size=45;fillColor=#FDEDEC;strokeColor=#CB4335;strokeWidth=2;fontColor=#78281F;fontSize=12;" vertex="1" parent="1">
          <mxGeometry x="1030" y="90" width="170" height="1180" as="geometry" />
        </mxCell>

        <!-- 5. Bảng CSDL (InnoDB Tables) -->
        <mxCell id="p_tables" value="&lt;b&gt;InnoDB Tables&lt;/b&gt;&lt;br&gt;(DON_HANG, CHI_TIET...)" style="shape=umlLifeline;perimeter=lifelinePerimeter;whiteSpace=wrap;html=1;container=1;collapsible=0;recursiveResize=0;outlineConnect=0;size=45;fillColor=#FEF9E7;strokeColor=#D4AC0D;strokeWidth=1.8;fontColor=#7D6608;fontSize=12;" vertex="1" parent="1">
          <mxGeometry x="1350" y="90" width="170" height="1180" as="geometry" />
        </mxCell>

        <!-- ==================== CÁC BƯỚC TUẦN TỰ (MESSAGES) ==================== -->

        <!-- Bước 1: Client gửi đơn -->
        <mxCell id="msg_1" value="1. POST /checkout (form data, idempotency_key)" style="html=1;verticalAlign=bottom;endArrow=block;edgeStyle=orthogonalEdgeStyle;strokeColor=#E65100;strokeWidth=1.8;fontColor=#BF360C;fontSize=11;" edge="1" parent="1" source="p_client" target="p_controller">
          <mxGeometry relative="1" as="geometry">
            <mxPoint as="offset" />
            <Array as="points">
              <mxPoint x="250" y="170" />
              <mxPoint x="250" y="170" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- Bước 2: Controller validate -->
        <mxCell id="msg_2" value="2. Form Request Validation (CSRF, phone, address)" style="html=1;verticalAlign=bottom;endArrow=block;edgeStyle=orthogonalEdgeStyle;strokeColor=#16A085;strokeWidth=1.4;fontColor=#0E6251;fontSize=10;" edge="1" parent="1" source="p_controller" target="p_controller">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="480" y="195" />
              <mxPoint x="500" y="195" />
              <mxPoint x="500" y="215" />
              <mxPoint x="455" y="215" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- Bước 3: Controller gọi Service -->
        <mxCell id="msg_3" value="3. placeOrder($validatedData, $idempotencyKey)" style="html=1;verticalAlign=bottom;endArrow=block;edgeStyle=orthogonalEdgeStyle;strokeColor=#16A085;strokeWidth=1.8;fontColor=#0E6251;fontSize=11;" edge="1" parent="1" source="p_controller" target="p_service">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="600" y="240" />
              <mxPoint x="600" y="240" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- Bước 4: Kiểm tra Idempotency -->
        <mxCell id="msg_4" value="4. Check idempotency_key trong CHECKOUT_IDEMPOTENCY" style="html=1;verticalAlign=bottom;endArrow=block;edgeStyle=orthogonalEdgeStyle;strokeColor=#2980B9;strokeWidth=1.5;fontColor=#1B4F72;fontSize=10;" edge="1" parent="1" source="p_service" target="p_tables">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="1100" y="275" />
              <mxPoint x="1100" y="275" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- Bước 5: Bắt đầu DB::transaction() -->
        <mxCell id="box_trans" value="&lt;b&gt;DB::transaction() BEGIN&lt;/b&gt;" style="shape=rect;html=1;whiteSpace=wrap;dashed=1;strokeColor=#2980B9;strokeWidth=1.5;fillColor=#EBF5FB;fontColor=#1B4F72;fontSize=11;align=center;" vertex="1" parent="1">
          <mxGeometry x="715" y="305" width="110" height="28" as="geometry" />
        </mxCell>

        <!-- Bước 6: lockForUpdate tồn kho -->
        <mxCell id="msg_6" value="5. lockForUpdate() biến thể CHI_TIET_SP (kiểm tra SoLuong >= item.qty)" style="html=1;verticalAlign=bottom;endArrow=block;edgeStyle=orthogonalEdgeStyle;strokeColor=#2980B9;strokeWidth=1.8;fontColor=#1B4F72;fontSize=11;" edge="1" parent="1" source="p_service" target="p_tables">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="1100" y="360" />
              <mxPoint x="1100" y="360" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- Note chống Race Condition -->
        <mxCell id="note_lock" value="&lt;b&gt;Bảo vệ tồn kho:&lt;/b&gt;&lt;br&gt;Khóa bi quan ngăn 2 khách cùng mua chiếc áo/size cuối cùng" style="shape=note;whiteSpace=wrap;html=1;size=14;verticalAlign=top;align=left;spacingLeft=5;fillColor=#FFFDE7;strokeColor=#FBC02D;strokeWidth=1.2;fontSize=10;fontColor=#685B00;" vertex="1" parent="1">
          <mxGeometry x="1540" y="335" width="200" height="55" as="geometry" />
        </mxCell>

        <!-- Bước 7: Khóa voucher -->
        <mxCell id="msg_7" value="6. lockForUpdate() MA_GIAM_GIA &amp; tính discount, decrement SoLuong" style="html=1;verticalAlign=bottom;endArrow=block;edgeStyle=orthogonalEdgeStyle;strokeColor=#2980B9;strokeWidth=1.5;fontColor=#1B4F72;fontSize=10;" edge="1" parent="1" source="p_service" target="p_tables">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="1100" y="420" />
              <mxPoint x="1100" y="420" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- Bước 8: DonHang::create -->
        <mxCell id="msg_8" value="7. INSERT INTO DON_HANG (MaDonHang, MaKH, MaPTVC, TongTien, ...)" style="html=1;verticalAlign=bottom;endArrow=block;edgeStyle=orthogonalEdgeStyle;strokeColor=#CB4335;strokeWidth=2;fontColor=#78281F;fontSize=11;" edge="1" parent="1" source="p_service" target="p_triggers">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="940" y="475" />
              <mxPoint x="940" y="475" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- TRIGGER 1: BEFORE INSERT -->
        <mxCell id="trg_1" value="&lt;b&gt;TRIGGER 1: BEFORE INSERT ON DON_HANG&lt;/b&gt;&lt;br&gt;&lt;i&gt;trg_tudong_lay_phiship_insert&lt;/i&gt;&lt;br&gt;• Tra cứu GiaVanChuyen từ PHUONG_THUC_VAN_CHUYEN nếu rỗng&lt;br&gt;• Gán ThanhTien = max(0, TongTien + PhiVanChuyen - GiamGia)" style="shape=rect;html=1;whiteSpace=wrap;dashed=0;strokeColor=#CB4335;strokeWidth=1.8;fillColor=#FDEDEC;fontColor=#78281F;fontSize=10;align=left;spacingLeft=8;rounded=1;" vertex="1" parent="1">
          <mxGeometry x="1060" y="505" width="370" height="70" as="geometry" />
        </mxCell>

        <!-- Lưu vào bảng DON_HANG -->
        <mxCell id="msg_trg1_write" value="Ghi bản ghi đơn hàng đã chuẩn hóa phí ship" style="html=1;verticalAlign=bottom;endArrow=block;edgeStyle=orthogonalEdgeStyle;strokeColor=#CB4335;strokeWidth=1.5;fontColor=#78281F;fontSize=10;" edge="1" parent="1" source="p_triggers" target="p_tables">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="1270" y="595" />
              <mxPoint x="1270" y="595" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- KHUNG VÒNG LẶP (LOOP) CHO ORDER ITEMS -->
        <mxCell id="frame_loop" value="&lt;b&gt;LOOP [Mỗi sản phẩm trong giỏ hàng]&lt;/b&gt;" style="shape=rect;html=1;whiteSpace=wrap;dashed=1;strokeColor=#2980B9;strokeWidth=1.6;fillColor=#F4F9FD;verticalAlign=top;align=left;spacingLeft=10;spacingTop=6;rounded=1;fontColor=#1B4F72;fontSize=11;" vertex="1" parent="1">
          <mxGeometry x="660" y="625" width="880" height="255" as="geometry" />
        </mxCell>

        <!-- Bước 9a: Trừ kho -->
        <mxCell id="msg_9a" value="8a. CHI_TIET_SP->decrement('SoLuong', $qty)" style="html=1;verticalAlign=bottom;endArrow=block;edgeStyle=orthogonalEdgeStyle;strokeColor=#2980B9;strokeWidth=1.6;fontColor=#1B4F72;fontSize=10;" edge="1" parent="1" source="p_service" target="p_tables">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="1100" y="660" />
              <mxPoint x="1100" y="660" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- Bước 9b: ChiTietDonHang::create -->
        <mxCell id="msg_9b" value="8b. INSERT INTO CHI_TIET_DON_HANG (MaDonHang, MaCTSP, SoLuong, DonGia)" style="html=1;verticalAlign=bottom;endArrow=block;edgeStyle=orthogonalEdgeStyle;strokeColor=#CB4335;strokeWidth=1.8;fontColor=#78281F;fontSize=10;" edge="1" parent="1" source="p_service" target="p_triggers">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="940" y="700" />
              <mxPoint x="940" y="700" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- TRIGGER 2: AFTER INSERT -->
        <mxCell id="trg_2" value="&lt;b&gt;TRIGGER 2: AFTER INSERT ON CHI_TIET_DON_HANG&lt;/b&gt;&lt;br&gt;&lt;i&gt;trg_capnhat_tong_donhang_after_insert&lt;/i&gt;&lt;br&gt;• SELECT SUM(SoLuong) &amp; SELECT SUM(SoLuong * DonGia)&lt;br&gt;• &lt;b&gt;UPDATE DON_HANG:&lt;/b&gt; TongSoLuong, TongTien, ThanhTien bất biến" style="shape=rect;html=1;whiteSpace=wrap;dashed=0;strokeColor=#CB4335;strokeWidth=1.8;fillColor=#FDEDEC;fontColor=#78281F;fontSize=10;align=left;spacingLeft=8;rounded=1;" vertex="1" parent="1">
          <mxGeometry x="1060" y="730" width="370" height="70" as="geometry" />
        </mxCell>

        <!-- Đồng bộ lại DON_HANG -->
        <mxCell id="msg_trg2_update" value="Tự động UPDATE bảng DON_HANG (Tạo Snapshot tài chính)" style="html=1;verticalAlign=bottom;endArrow=block;edgeStyle=orthogonalEdgeStyle;strokeColor=#CB4335;strokeWidth=1.5;fontColor=#78281F;fontSize=10;" edge="1" parent="1" source="p_triggers" target="p_tables">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="1270" y="820" />
              <mxPoint x="1270" y="820" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- Note về Snapshot -->
        <mxCell id="note_snapshot" value="&lt;b&gt;Đặc sản kỹ thuật:&lt;/b&gt;&lt;br&gt;Trigger tự động tính tổng tiền ngay ở nhân CSDL, đảm bảo toàn vẹn ngay cả khi thao tác ngoài Laravel." style="shape=note;whiteSpace=wrap;html=1;size=14;verticalAlign=top;align=left;spacingLeft=5;fillColor=#FFFDE7;strokeColor=#FBC02D;strokeWidth=1.2;fontSize=10;fontColor=#685B00;" vertex="1" parent="1">
          <mxGeometry x="1550" y="735" width="200" height="65" as="geometry" />
        </mxCell>

        <!-- Bước 10: Xóa giỏ hàng -->
        <mxCell id="msg_10" value="9. CartService::clear() (Xóa giỏ hàng session / CSDL GIO_HANG)" style="html=1;verticalAlign=bottom;endArrow=block;edgeStyle=orthogonalEdgeStyle;strokeColor=#2980B9;strokeWidth=1.5;fontColor=#1B4F72;fontSize=10;" edge="1" parent="1" source="p_service" target="p_tables">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="1100" y="905" />
              <mxPoint x="1100" y="905" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- Bước 11: DB::transaction COMMIT -->
        <mxCell id="box_commit" value="&lt;b&gt;DB::transaction() COMMIT&lt;/b&gt;" style="shape=rect;html=1;whiteSpace=wrap;dashed=1;strokeColor=#16A085;strokeWidth=1.5;fillColor=#E8F8F5;fontColor=#0E6251;fontSize=11;align=center;" vertex="1" parent="1">
          <mxGeometry x="715" y="940" width="110" height="28" as="geometry" />
        </mxCell>

        <!-- Bước 12: Đánh dấu idempotency completed -->
        <mxCell id="msg_12" value="10. UPDATE CHECKOUT_IDEMPOTENCY status = 'completed', order_id = $order->id" style="html=1;verticalAlign=bottom;endArrow=block;edgeStyle=orthogonalEdgeStyle;strokeColor=#2980B9;strokeWidth=1.4;fontColor=#1B4F72;fontSize=10;" edge="1" parent="1" source="p_service" target="p_tables">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="1100" y="990" />
              <mxPoint x="1100" y="990" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- Bước 13: Service trả về Order -->
        <mxCell id="msg_13" value="11. Return $order (Đối tượng DonHang hoàn tất)" style="html=1;verticalAlign=bottom;endArrow=open;dashed=1;endSize=8;edgeStyle=orthogonalEdgeStyle;strokeColor=#16A085;strokeWidth=1.8;fontColor=#0E6251;fontSize=11;" edge="1" parent="1" source="p_service" target="p_controller">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="600" y="1035" />
              <mxPoint x="600" y="1035" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- Bước 14: Redirect thành công -->
        <mxCell id="msg_14" value="12. 302 Redirect /checkout/success/{orderId} (Kèm flash message)" style="html=1;verticalAlign=bottom;endArrow=open;dashed=1;endSize=8;edgeStyle=orthogonalEdgeStyle;strokeColor=#E65100;strokeWidth=1.8;fontColor=#BF360C;fontSize=11;" edge="1" parent="1" source="p_controller" target="p_client">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="250" y="1080" />
              <mxPoint x="250" y="1080" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- ==================== LUỒNG NGOẠI LỆ / ROLLBACK ==================== -->
        <mxCell id="frame_alt" value="&lt;b&gt;ALT [Ngoại lệ: Hết tồn kho HOẶC Voucher hết lượt sử dụng]&lt;/b&gt;" style="shape=rect;html=1;whiteSpace=wrap;dashed=1;strokeColor=#C0392B;strokeWidth=1.5;fillColor=#FDEDEC;verticalAlign=top;align=left;spacingLeft=10;spacingTop=6;rounded=1;fontColor=#922B21;fontSize=10;" vertex="1" parent="1">
          <mxGeometry x="360" y="1120" width="1180" height="90" as="geometry" />
        </mxCell>

        <mxCell id="msg_rollback" value="throw Exception -> DB::transaction() TỰ ĐỘNG ROLLBACK TOÀN BỘ (Dữ liệu trả về trạng thái nguyên vẹn)" style="html=1;verticalAlign=bottom;endArrow=block;edgeStyle=orthogonalEdgeStyle;strokeColor=#C0392B;strokeWidth=1.6;fontColor=#922B21;fontSize=10;" edge="1" parent="1" source="p_service" target="p_tables">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="1100" y="1160" />
              <mxPoint x="1100" y="1160" />
            </Array>
          </mxGeometry>
        </mxCell>

        <mxCell id="msg_error_res" value="Redirect back() kèm error flash (Hiển thị thông báo đỏ cho khách)" style="html=1;verticalAlign=bottom;endArrow=open;dashed=1;endSize=8;edgeStyle=orthogonalEdgeStyle;strokeColor=#C0392B;strokeWidth=1.5;fontColor=#922B21;fontSize=10;" edge="1" parent="1" source="p_controller" target="p_client">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="250" y="1190" />
              <mxPoint x="250" y="1190" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- Bảng chú thích (Legend) -->
        <mxCell id="legend_box" value="&lt;b&gt;Ý NGHĨA PHÂN TẦNG VÀ PHỐI HỢP TRONG SƠ ĐỒ:&lt;/b&gt;&lt;br&gt;• &lt;b&gt;Tầng Service (CheckoutService):&lt;/b&gt; Chịu trách nhiệm quy tắc nghiệp vụ cao (Business Rules), điều phối Transaction, khóa dòng bi quan lockForUpdate() và chống Race Condition.&lt;br&gt;• &lt;b&gt;Tầng Database Triggers (MySQL Engine):&lt;/b&gt; Chịu trách nhiệm toàn vẹn tài chính ở mức nhân CSDL (Database Engine Level), tự động tính cước vận chuyển (BEFORE INSERT) và tái tính toán TongSoLuong, TongTien, ThanhTien (AFTER INSERT/UPDATE/DELETE).&lt;br&gt;• &lt;b&gt;Tính năng chống đặt trùng (Idempotency):&lt;/b&gt; Khóa chặn việc gửi nhiều yêu cầu liên tiếp nhờ bảng kỹ thuật CHECKOUT_IDEMPOTENCY." style="shape=rect;html=1;whiteSpace=wrap;dashed=0;strokeColor=#BDC3C7;strokeWidth=1.2;fillColor=#FFFFFF;verticalAlign=middle;align=left;spacingLeft=15;rounded=1;fontSize=11;fontColor=#333333;" vertex="1" parent="1">
          <mxGeometry x="80" y="1285" width="1440" height="75" as="geometry" />
        </mxCell>

      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
"""

def generate_svg_and_png():
    docs_figures = Path(r"D:\PHP_NOSQL\SMART_CASUAL_LARAVEL\docs\figures")
    docs_figures.mkdir(parents=True, exist_ok=True)
    root_dir = Path(r"D:\PHP_NOSQL")

    drawio_file = docs_figures / "hinh_2_3_service_triggers.drawio"
    drawio_file.write_text(DRAWIO_CONTENT, encoding="utf-8")
    (root_dir / "hinh_2_3_service_triggers.drawio").write_text(DRAWIO_CONTENT, encoding="utf-8")
    print(f"Đã tạo tệp Draw.io: {drawio_file}")

    # Tạo SVG Sequence Diagram vector chất lượng cao
    svg_content = """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1780 1380" width="1780" height="1380" style="background:#ffffff; font-family:'Segoe UI', Arial, sans-serif;">
  <defs>
    <style>
      .title {{ font-size: 20px; font-weight: bold; fill: #14221D; text-anchor: middle; }}
      .subtitle {{ font-size: 14px; fill: #555555; text-anchor: middle; }}
      .p-head {{ font-weight: bold; font-size: 13px; text-anchor: middle; }}
      .p-sub {{ font-size: 11px; text-anchor: middle; }}
      .line {{ stroke-dasharray: 6,4; stroke-width: 1.5; }}
      .msg-text {{ font-size: 12px; font-weight: 500; }}
      .msg-num {{ font-weight: bold; }}
      .box-tag {{ font-size: 11px; font-weight: bold; }}
      .note-text {{ font-size: 11px; fill: #5D4037; }}
    </style>
    <marker id="arrow-orange" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
      <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#E65100" />
    </marker>
    <marker id="arrow-green" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
      <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#16A085" />
    </marker>
    <marker id="arrow-blue" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
      <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#2980B9" />
    </marker>
    <marker id="arrow-red" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
      <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#CB4335" />
    </marker>
  </defs>

  <!-- TIÊU ĐỀ -->
  <text x="890" y="38" class="title">HỆ THỐNG THƯƠNG MẠI ĐIỆN TỬ SMART CASUAL</text>
  <text x="890" y="62" class="subtitle">HÌNH 2.3: BIỂU ĐỒ TUẦN TỰ (SEQUENCE DIAGRAM) PHỐI HỢP GIỮA SERVICE LAYER VÀ DATABASE TRIGGERS</text>

  <!-- ==================== LIFELINES ==================== -->
  <!-- 1. Client -->
  <g transform="translate(80, 90)">
    <rect width="160" height="50" rx="8" fill="#FFF3E0" stroke="#E65100" stroke-width="2"/>
    <text x="80" y="22" class="p-head" fill="#BF360C">Khách hàng</text>
    <text x="80" y="38" class="p-sub" fill="#E65100">(Trình duyệt / Client)</text>
    <line x1="80" y1="50" x2="80" y2="1160" stroke="#E65100" class="line"/>
  </g>

  <!-- 2. Controller -->
  <g transform="translate(370, 90)">
    <rect width="180" height="50" rx="8" fill="#E8F8F5" stroke="#16A085" stroke-width="2"/>
    <text x="90" y="22" class="p-head" fill="#0E6251">CheckoutController</text>
    <text x="90" y="38" class="p-sub" fill="#16A085">(Tầng Controller / HTTP)</text>
    <line x1="90" y1="50" x2="90" y2="1160" stroke="#16A085" class="line"/>
  </g>

  <!-- 3. CheckoutService -->
  <g transform="translate(690, 90)">
    <rect width="200" height="50" rx="8" fill="#EBF5FB" stroke="#2980B9" stroke-width="2.2"/>
    <text x="100" y="22" class="p-head" fill="#1B4F72">CheckoutService</text>
    <text x="100" y="38" class="p-sub" fill="#2980B9">(Tầng Dịch Vụ - App Core)</text>
    <line x1="100" y1="50" x2="100" y2="1160" stroke="#2980B9" class="line"/>
  </g>

  <!-- 4. MySQL Engine & Triggers -->
  <g transform="translate(1040, 90)">
    <rect width="190" height="50" rx="8" fill="#FDEDEC" stroke="#CB4335" stroke-width="2.2"/>
    <text x="95" y="22" class="p-head" fill="#78281F">MySQL 8.4 Engine</text>
    <text x="95" y="38" class="p-sub" fill="#CB4335">(4 Triggers CSDL)</text>
    <line x1="95" y1="50" x2="95" y2="1160" stroke="#CB4335" class="line"/>
  </g>

  <!-- 5. InnoDB Tables -->
  <g transform="translate(1380, 90)">
    <rect width="180" height="50" rx="8" fill="#FEF9E7" stroke="#D4AC0D" stroke-width="2"/>
    <text x="90" y="22" class="p-head" fill="#7D6608">InnoDB Tables</text>
    <text x="90" y="38" class="p-sub" fill="#B7950B">(DON_HANG, CHI_TIET...)</text>
    <line x1="90" y1="50" x2="90" y2="1160" stroke="#D4AC0D" class="line"/>
  </g>

  <!-- ==================== MESSAGES & INTERACTIONS ==================== -->

  <!-- Bước 1: POST /checkout -->
  <g transform="translate(0, 180)">
    <line x1="160" y1="0" x2="455" y2="0" stroke="#E65100" stroke-width="1.8" marker-end="url(#arrow-orange)"/>
    <text x="175" y="-8" class="msg-text" fill="#BF360C"><tspan class="msg-num">1.</tspan> POST /checkout (form data, idempotency_key)</text>
  </g>

  <!-- Bước 2: Validation nội bộ -->
  <g transform="translate(460, 215)">
    <path d="M 0 0 L 35 0 L 35 22 L 5 22" fill="none" stroke="#16A085" stroke-width="1.5" marker-end="url(#arrow-green)"/>
    <text x="45" y="16" class="msg-text" fill="#0E6251" font-size="11"><tspan class="msg-num">2.</tspan> Form Request Validation (CSRF, phone, address)</text>
  </g>

  <!-- Bước 3: Gọi placeOrder() -->
  <g transform="translate(0, 260)">
    <line x1="460" y1="0" x2="785" y2="0" stroke="#16A085" stroke-width="2" marker-end="url(#arrow-green)"/>
    <text x="480" y="-8" class="msg-text" fill="#0E6251"><tspan class="msg-num">3.</tspan> placeOrder($validatedData, $idempotencyKey)</text>
  </g>

  <!-- Bước 4: Check Idempotency -->
  <g transform="translate(0, 300)">
    <line x1="790" y1="0" x2="1465" y2="0" stroke="#2980B9" stroke-width="1.6" marker-end="url(#arrow-blue)"/>
    <text x="810" y="-8" class="msg-text" fill="#1B4F72"><tspan class="msg-num">4.</tspan> Check idempotency_key trong CHECKOUT_IDEMPOTENCY</text>
  </g>

  <!-- Tag DB::transaction BEGIN -->
  <g transform="translate(725, 325)">
    <rect width="130" height="26" rx="4" fill="#EBF5FB" stroke="#2980B9" stroke-width="1.5" stroke-dasharray="4,2"/>
    <text x="65" y="17" class="box-tag" fill="#1B4F72" text-anchor="middle">DB::transaction() BEGIN</text>
  </g>

  <!-- Bước 5: lockForUpdate tồn kho -->
  <g transform="translate(0, 385)">
    <line x1="790" y1="0" x2="1465" y2="0" stroke="#2980B9" stroke-width="2" marker-end="url(#arrow-blue)"/>
    <text x="810" y="-8" class="msg-text" fill="#1B4F72"><tspan class="msg-num">5.</tspan> lockForUpdate() tồn kho CHI_TIET_SP (kiểm tra SoLuong >= item.qty)</text>
  </g>

  <!-- Note chống Race Condition -->
  <g transform="translate(1485, 360)">
    <rect width="250" height="50" rx="4" fill="#FFFDE7" stroke="#FBC02D" stroke-width="1.2"/>
    <text x="10" y="18" class="note-text" font-weight="bold">Bảo vệ tồn kho (Chống Race Condition):</text>
    <text x="10" y="34" class="note-text">Khóa bi quan ngăn 2 khách mua cùng lúc</text>
  </g>

  <!-- Bước 6: Khóa voucher -->
  <g transform="translate(0, 440)">
    <line x1="790" y1="0" x2="1465" y2="0" stroke="#2980B9" stroke-width="1.6" marker-end="url(#arrow-blue)"/>
    <text x="810" y="-8" class="msg-text" fill="#1B4F72"><tspan class="msg-num">6.</tspan> lockForUpdate() MA_GIAM_GIA &amp; tính discount, decrement SoLuong</text>
  </g>

  <!-- Bước 7: INSERT DON_HANG -->
  <g transform="translate(0, 495)">
    <line x1="790" y1="0" x2="1130" y2="0" stroke="#CB4335" stroke-width="2.2" marker-end="url(#arrow-red)"/>
    <text x="810" y="-8" class="msg-text" fill="#78281F"><tspan class="msg-num">7.</tspan> INSERT INTO DON_HANG (MaDonHang, MaKH, MaPTVC, TongTien, ...)</text>
  </g>

  <!-- TRIGGER 1: BEFORE INSERT BOX -->
  <g transform="translate(1050, 520)">
    <rect width="400" height="68" rx="6" fill="#FDEDEC" stroke="#CB4335" stroke-width="1.8"/>
    <text x="15" y="20" class="msg-text" font-weight="bold" fill="#78281F">TRIGGER 1: BEFORE INSERT ON DON_HANG</text>
    <text x="15" y="36" class="msg-text" font-style="italic" fill="#922B21">trg_tudong_lay_phiship_insert</text>
    <text x="15" y="52" class="msg-text" font-size="11" fill="#78281F">• Tra cứu GiaVanChuyen từ PTVC &amp; tính ThanhTien tạm thời</text>
  </g>

  <!-- Ghi DON_HANG xuống bảng -->
  <g transform="translate(0, 610)">
    <line x1="1135" y1="0" x2="1465" y2="0" stroke="#CB4335" stroke-width="1.8" marker-end="url(#arrow-red)"/>
    <text x="1150" y="-8" class="msg-text" fill="#78281F" font-size="11">Ghi bản ghi DON_HANG đã chuẩn hóa cước phí ship</text>
  </g>

  <!-- KHUNG LOOP ORDER ITEMS -->
  <g transform="translate(670, 635)">
    <rect width="890" height="240" rx="8" fill="#F4F9FD" stroke="#2980B9" stroke-width="1.8" stroke-dasharray="6,3"/>
    <rect x="0" y="0" width="280" height="24" rx="4" fill="#2980B9"/>
    <text x="12" y="16" fill="#ffffff" font-size="11" font-weight="bold">LOOP [Mỗi sản phẩm trong giỏ hàng]</text>
  </g>

  <!-- Bước 8a: Trừ kho -->
  <g transform="translate(0, 675)">
    <line x1="790" y1="0" x2="1465" y2="0" stroke="#2980B9" stroke-width="1.8" marker-end="url(#arrow-blue)"/>
    <text x="810" y="-8" class="msg-text" fill="#1B4F72"><tspan class="msg-num">8a.</tspan> CHI_TIET_SP->decrement('SoLuong', $qty)</text>
  </g>

  <!-- Bước 8b: INSERT CHI_TIET_DON_HANG -->
  <g transform="translate(0, 715)">
    <line x1="790" y1="0" x2="1130" y2="0" stroke="#CB4335" stroke-width="2" marker-end="url(#arrow-red)"/>
    <text x="810" y="-8" class="msg-text" fill="#78281F"><tspan class="msg-num">8b.</tspan> INSERT INTO CHI_TIET_DON_HANG (MaDonHang, MaCTSP, SoLuong, DonGia)</text>
  </g>

  <!-- TRIGGER 2: AFTER INSERT BOX -->
  <g transform="translate(1050, 740)">
    <rect width="400" height="72" rx="6" fill="#FDEDEC" stroke="#CB4335" stroke-width="1.8"/>
    <text x="15" y="20" class="msg-text" font-weight="bold" fill="#78281F">TRIGGER 2: AFTER INSERT ON CHI_TIET_DON_HANG</text>
    <text x="15" y="36" class="msg-text" font-style="italic" fill="#922B21">trg_capnhat_tong_donhang_after_insert</text>
    <text x="15" y="52" class="msg-text" font-size="11" fill="#78281F">• SELECT SUM(SoLuong) &amp; SUM(SoLuong * DonGia)</text>
    <text x="15" y="66" class="msg-text" font-size="11" font-weight="bold" fill="#78281F">• UPDATE DON_HANG: TongSoLuong, TongTien, ThanhTien</text>
  </g>

  <!-- Đồng bộ lại DON_HANG -->
  <g transform="translate(0, 835)">
    <line x1="1135" y1="0" x2="1465" y2="0" stroke="#CB4335" stroke-width="2" marker-end="url(#arrow-red)"/>
    <text x="1150" y="-8" class="msg-text" fill="#78281F" font-size="11">Tự động UPDATE DON_HANG (Tạo Snapshot tài chính bất biến)</text>
  </g>

  <!-- Note Snapshot -->
  <g transform="translate(1485, 740)">
    <rect width="250" height="72" rx="4" fill="#FFFDE7" stroke="#FBC02D" stroke-width="1.2"/>
    <text x="10" y="18" class="note-text" font-weight="bold">Snapshot tài chính bất biến:</text>
    <text x="10" y="34" class="note-text">Trigger tính toán trực tiếp ở nhân</text>
    <text x="10" y="48" class="note-text">CSDL, không phụ thuộc code PHP,</text>
    <text x="10" y="62" class="note-text">tránh sai lệch khi giá SP đổi sau này.</text>
  </g>

  <!-- Bước 9: Xóa giỏ hàng -->
  <g transform="translate(0, 905)">
    <line x1="790" y1="0" x2="1465" y2="0" stroke="#2980B9" stroke-width="1.6" marker-end="url(#arrow-blue)"/>
    <text x="810" y="-8" class="msg-text" fill="#1B4F72"><tspan class="msg-num">9.</tspan> CartService::clear() (Xóa giỏ hàng session / CSDL GIO_HANG)</text>
  </g>

  <!-- Tag DB::transaction COMMIT -->
  <g transform="translate(725, 930)">
    <rect width="130" height="26" rx="4" fill="#E8F8F5" stroke="#16A085" stroke-width="1.5" stroke-dasharray="4,2"/>
    <text x="65" y="17" class="box-tag" fill="#0E6251" text-anchor="middle">DB::transaction() COMMIT</text>
  </g>

  <!-- Bước 10: Update Idempotency completed -->
  <g transform="translate(0, 985)">
    <line x1="790" y1="0" x2="1465" y2="0" stroke="#2980B9" stroke-width="1.5" marker-end="url(#arrow-blue)"/>
    <text x="810" y="-8" class="msg-text" fill="#1B4F72"><tspan class="msg-num">10.</tspan> UPDATE CHECKOUT_IDEMPOTENCY status = 'completed', order_id = $order->id</text>
  </g>

  <!-- Bước 11: Return $order -->
  <g transform="translate(0, 1030)">
    <line x1="785" y1="0" x2="465" y2="0" stroke="#16A085" stroke-width="2" stroke-dasharray="5,3" marker-end="url(#arrow-green)"/>
    <text x="500" y="-8" class="msg-text" fill="#0E6251"><tspan class="msg-num">11.</tspan> Return $order (Đối tượng DonHang đã hoàn tất)</text>
  </g>

  <!-- Bước 12: Redirect client -->
  <g transform="translate(0, 1075)">
    <line x1="455" y1="0" x2="165" y2="0" stroke="#E65100" stroke-width="2" stroke-dasharray="5,3" marker-end="url(#arrow-orange)"/>
    <text x="180" y="-8" class="msg-text" fill="#BF360C"><tspan class="msg-num">12.</tspan> 302 Redirect /checkout/success/{orderId} (Kèm flash message)</text>
  </g>

  <!-- ==================== LUỒNG NGOẠI LỆ (ALT BOX) ==================== -->
  <g transform="translate(80, 1115)">
    <rect width="1480" height="85" rx="6" fill="#FDEDEC" stroke="#C0392B" stroke-width="1.5" stroke-dasharray="5,3"/>
    <text x="15" y="20" font-size="11" font-weight="bold" fill="#922B21">ALT [Ngoại lệ: Hết số lượng tồn kho HOẶC Mã giảm giá hết lượt sử dụng]</text>
    
    <line x1="710" y1="42" x2="1390" y2="42" stroke="#C0392B" stroke-width="1.5" marker-end="url(#arrow-red)"/>
    <text x="730" y="36" font-size="11" fill="#922B21">throw Exception -> DB::transaction() TỰ ĐỘNG ROLLBACK TOÀN BỘ (Dữ liệu hoàn nguyên 100%)</text>
    
    <line x1="380" y1="65" x2="85" y2="65" stroke="#C0392B" stroke-width="1.5" stroke-dasharray="4,2" marker-end="url(#arrow-red)"/>
    <text x="100" y="59" font-size="11" fill="#922B21">Redirect back() withErrors (Hiển thị flash thông báo lỗi cho người dùng)</text>
  </g>

  <!-- BẢNG CHÚ THÍCH (LEGEND) -->
  <g transform="translate(80, 1225)">
    <rect width="1480" height="95" rx="6" fill="#FFFFFF" stroke="#BDC3C7" stroke-width="1.2"/>
    <text x="15" y="22" font-size="12" font-weight="bold" fill="#2C3E50">Ý NGHĨA PHÂN TẦNG VÀ PHỐI HỢP TRONG SƠ ĐỒ:</text>
    <text x="15" y="42" font-size="11" fill="#333333">• <tspan font-weight="bold">Tầng Service (CheckoutService):</tspan> Điều phối toàn bộ quy tắc nghiệp vụ (Business Rules), bọc phiên giao dịch DB::transaction(), áp dụng khóa bi quan lockForUpdate() chống Race Condition.</text>
    <text x="15" y="62" font-size="11" fill="#333333">• <tspan font-weight="bold">Tầng Database Triggers (MySQL Engine):</tspan> Đảm bảo tính toàn vẹn tài chính ở cấp CSDL, tự tính cước vận chuyển (BEFORE INSERT) và tự động tính tổng tiền TongSoLuong, TongTien, ThanhTien (AFTER INSERT/UPDATE/DELETE).</text>
    <text x="15" y="82" font-size="11" fill="#333333">• <tspan font-weight="bold">Cơ chế Idempotency:</tspan> Ngăn chặn hoàn toàn lỗi tạo trùng đơn hàng khi người dùng bấm nút Đặt hàng nhiều lần liên tiếp thông qua bảng kỹ thuật CHECKOUT_IDEMPOTENCY.</text>
  </g>

</svg>
"""

    svg_file = docs_figures / "hinh_2_3_service_triggers.svg"
    svg_file.write_text(svg_content, encoding="utf-8")
    (root_dir / "hinh_2_3_service_triggers.svg").write_text(svg_content, encoding="utf-8")
    print(f"Đã tạo tệp Vector SVG: {svg_file}")

    # Render PNG HD bằng headless Edge
    edge_paths = [
        r"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe",
        r"C:\Program Files\Microsoft\Edge\Application\msedge.exe",
    ]
    edge_exe = next((p for p in edge_paths if os.path.exists(p)), None)
    if not edge_exe:
        print("Không tìm thấy Microsoft Edge.")
        return

    png_file = docs_figures / "hinh_2_3_service_triggers.png"
    html_wrapper = docs_figures / "temp_render_2_3.html"
    html_wrapper.write_text(f"""<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body {{ margin: 0; padding: 20px; background: #ffffff; display: flex; justify-content: center; }}
    svg {{ box-shadow: 0 4px 16px rgba(0,0,0,0.06); }}
  </style>
</head>
<body>
{svg_content}
</body>
</html>
""", encoding="utf-8")

    cmd = [
        edge_exe,
        "--headless",
        "--disable-gpu",
        "--hide-scrollbars",
        "--window-size=1860,1440",
        f"--screenshot={str(png_file)}",
        str(html_wrapper)
    ]
    subprocess.run(cmd, check=True)
    if png_file.exists():
        (root_dir / "hinh_2_3_service_triggers.png").write_bytes(png_file.read_bytes())
        print(f"Đã xuất ảnh PNG HD: {png_file} ({png_file.stat().st_size} bytes)")
    if html_wrapper.exists():
        html_wrapper.unlink()

if __name__ == "__main__":
    generate_svg_and_png()
