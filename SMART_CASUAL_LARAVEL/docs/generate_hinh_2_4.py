import os
import sys
import subprocess
from pathlib import Path

if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8')

DRAWIO_CONTENT = """<mxfile host="app.diagrams.net" modified="2026-10-26T23:15:00.000Z" agent="Antigravity" version="24.7.5" type="device">
  <diagram id="Order_State_Machine" name="Hinh 2.4 - Sơ đồ chuyển đổi trạng thái đơn hàng">
    <mxGraphModel dx="1600" dy="1100" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1700" pageHeight="1100" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />

        <!-- Tiêu đề sơ đồ -->
        <mxCell id="title_box" value="&lt;b&gt;HỆ THỐNG THƯƠNG MẠI ĐIỆN TỬ SMART CASUAL&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 15px; font-weight: normal; color: #555555;&quot;&gt;HÌNH 2.4: SƠ ĐỒ CHUYỂN ĐỔI TRẠNG THÁI ĐƠN HÀNG (STATE MACHINE DIAGRAM) - ALLOWED_TRANSITIONS&lt;/font&gt;" style="text;html=1;align=center;verticalAlign=middle;resizable=0;points=[];autosize=1;strokeColor=none;fillColor=none;fontSize=18;fontColor=#14221D;fontFamily=Helvetica;" vertex="1" parent="1">
          <mxGeometry x="450" y="25" width="800" height="50" as="geometry" />
        </mxCell>

        <!-- ==================== KHU VỰC LUỒNG CHUẨN ==================== -->
        <mxCell id="zone_standard" value="&lt;b&gt;I. LUỒNG THỰC HIỆN ĐƠN HÀNG CHUẨN (HAPPY PATH: PENDING ➔ CONFIRMED ➔ PACKING ➔ SHIPPING ➔ DELIVERED)&lt;/b&gt;" style="shape=rect;html=1;whiteSpace=wrap;dashed=1;strokeColor=#16A085;strokeWidth=1.8;fillColor=#F2FAF7;verticalAlign=top;align=left;spacingLeft=15;spacingTop=10;rounded=1;fontColor=#0E6251;fontSize=12;" vertex="1" parent="1">
          <mxGeometry x="50" y="90" width="1600" height="420" as="geometry" />
        </mxCell>

        <!-- Start Node -->
        <mxCell id="node_start" value="" style="ellipse;fillColor=#14221D;strokeColor=none;" vertex="1" parent="1">
          <mxGeometry x="90" y="240" width="30" height="30" as="geometry" />
        </mxCell>

        <mxCell id="lbl_start" value="Khách đặt hàng&lt;br&gt;POST /checkout" style="text;html=1;align=center;verticalAlign=middle;fontSize=11;fontColor=#555555;" vertex="1" parent="1">
          <mxGeometry x="60" y="275" width="90" height="30" as="geometry" />
        </mxCell>

        <!-- Arrow: Start -> pending -->
        <mxCell id="arr_start_pending" value="placeOrder()" style="html=1;verticalAlign=bottom;endArrow=block;strokeColor=#16A085;strokeWidth=2;fontColor=#0E6251;fontSize=11;" edge="1" parent="1" source="node_start" target="st_pending">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- 1. Trạng thái PENDING -->
        <mxCell id="st_pending" value="&lt;b&gt;pending&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 11px; font-weight: normal;&quot;&gt;(Chờ xác nhận)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; font-size: 10px; padding-left: 4px;&quot;&gt;• &lt;b&gt;entry /&lt;/b&gt; Trừ tồn kho tạm thời&lt;br&gt;• &lt;b&gt;entry /&lt;/b&gt; Trừ 1 lượt voucher&lt;br&gt;• &lt;b&gt;entry /&lt;/b&gt; Khởi tạo MaDonHang&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=40;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#FEF9E7;strokeColor=#D4AC0D;strokeWidth=2;fontColor=#7D6608;fontSize=13;rounded=1;arcSize=10;" vertex="1" parent="1">
          <mxGeometry x="190" y="200" width="200" height="110" as="geometry" />
        </mxCell>

        <!-- 2. Trạng thái CONFIRMED -->
        <mxCell id="st_confirmed" value="&lt;b&gt;confirmed&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 11px; font-weight: normal;&quot;&gt;(Đã xác nhận)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; font-size: 10px; padding-left: 4px;&quot;&gt;• &lt;b&gt;entry /&lt;/b&gt; Gán MaNV xác nhận&lt;br&gt;• Kiểm tra thông tin SĐT, địa chỉ&lt;br&gt;• Khóa chỉnh sửa dòng hàng&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=40;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#EBF5FB;strokeColor=#2980B9;strokeWidth=2;fontColor=#1B4F72;fontSize=13;rounded=1;arcSize=10;" vertex="1" parent="1">
          <mxGeometry x="490" y="200" width="210" height="110" as="geometry" />
        </mxCell>

        <!-- Arrow: pending -> confirmed -->
        <mxCell id="arr_p_c" value="[NV duyệt]&lt;br&gt;updateStatus('confirmed')" style="html=1;verticalAlign=bottom;endArrow=block;strokeColor=#2980B9;strokeWidth=2;fontColor=#1B4F72;fontSize=11;align=center;" edge="1" parent="1" source="st_pending" target="st_confirmed">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- 3. Trạng thái PACKING -->
        <mxCell id="st_packing" value="&lt;b&gt;packing&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 11px; font-weight: normal;&quot;&gt;(Đang đóng gói)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; font-size: 10px; padding-left: 4px;&quot;&gt;• &lt;b&gt;entry /&lt;/b&gt; In phiếu xuất kho&lt;br&gt;• Nhân viên kho lấy đúng SKU&lt;br&gt;• Đóng kiện niêm phong&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=40;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#EBF5FB;strokeColor=#2980B9;strokeWidth=2;fontColor=#1B4F72;fontSize=13;rounded=1;arcSize=10;" vertex="1" parent="1">
          <mxGeometry x="800" y="200" width="200" height="110" as="geometry" />
        </mxCell>

        <!-- Arrow: confirmed -> packing -->
        <mxCell id="arr_c_pk" value="[Kho xuất hàng]&lt;br&gt;updateStatus('packing')" style="html=1;verticalAlign=bottom;endArrow=block;strokeColor=#2980B9;strokeWidth=2;fontColor=#1B4F72;fontSize=11;align=center;" edge="1" parent="1" source="st_confirmed" target="st_packing">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- 4. Trạng thái SHIPPING -->
        <mxCell id="st_shipping" value="&lt;b&gt;shipping&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 11px; font-weight: normal;&quot;&gt;(Đang giao hàng)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; font-size: 10px; padding-left: 4px;&quot;&gt;• &lt;b&gt;entry /&lt;/b&gt; Bàn giao Shipper / PTVC&lt;br&gt;• &lt;b&gt;RÀNG BUỘC:&lt;/b&gt; &lt;font color=&quot;#C0392B&quot;&gt;&lt;b&gt;KHÔNG THỂ HỦY&lt;/b&gt;&lt;/font&gt;&lt;br&gt;• Theo dõi hành trình bưu kiện&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=40;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#FCF3CF;strokeColor=#B7950B;strokeWidth=2;fontColor=#7D6608;fontSize=13;rounded=1;arcSize=10;" vertex="1" parent="1">
          <mxGeometry x="1100" y="200" width="220" height="110" as="geometry" />
        </mxCell>

        <!-- Arrow: packing -> shipping -->
        <mxCell id="arr_pk_s" value="[Giao vận chuyển]&lt;br&gt;updateStatus('shipping')" style="html=1;verticalAlign=bottom;endArrow=block;strokeColor=#B7950B;strokeWidth=2;fontColor=#7D6608;fontSize=11;align=center;" edge="1" parent="1" source="st_packing" target="st_shipping">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- 5. Trạng thái DELIVERED (Terminal) -->
        <mxCell id="st_delivered" value="&lt;b&gt;delivered&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 11px; font-weight: normal;&quot;&gt;(Đã giao hàng - Hoàn tất)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; font-size: 10px; padding-left: 4px;&quot;&gt;• &lt;b&gt;entry /&lt;/b&gt; Nếu COD: DaThanhToan=1&lt;br&gt;• &lt;b&gt;entry /&lt;/b&gt; Ghi nhận tích điểm KH&lt;br&gt;• Mở quyền viết đánh giá sao&lt;br&gt;• &lt;i&gt;Trạng thái kết thúc thành công&lt;/i&gt;&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=40;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#D4EFDF;strokeColor=#229954;strokeWidth=2.2;fontColor=#145A32;fontSize=13;rounded=1;arcSize=10;" vertex="1" parent="1">
          <mxGeometry x="1410" y="200" width="210" height="120" as="geometry" />
        </mxCell>

        <!-- Arrow: shipping -> delivered -->
        <mxCell id="arr_s_d" value="[Khách nhận hàng]&lt;br&gt;updateStatus('delivered')" style="html=1;verticalAlign=bottom;endArrow=block;strokeColor=#229954;strokeWidth=2.2;fontColor=#145A32;fontSize=11;align=center;" edge="1" parent="1" source="st_shipping" target="st_delivered">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- End Node cho Delivered -->
        <mxCell id="node_end_delivered" value="" style="ellipse;shape=doubleEllipse;fillColor=#145A32;strokeColor=#229954;strokeWidth=2;" vertex="1" parent="1">
          <mxGeometry x="1500" y="370" width="30" height="30" as="geometry" />
        </mxCell>

        <mxCell id="arr_d_end" value="Giao dịch kết thúc" style="html=1;verticalAlign=bottom;endArrow=block;strokeColor=#229954;strokeWidth=1.8;fontColor=#145A32;fontSize=10;" edge="1" parent="1" source="st_delivered" target="node_end_delivered">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>


        <!-- ==================== KHU VỰC LUỒNG HỦY ĐƠN HÀNG ==================== -->
        <mxCell id="zone_cancelled" value="&lt;b&gt;II. LUỒNG HỦY ĐƠN HÀNG &amp;amp; HOÀN NGUYÊN TỒN KHO ATOMIC RESTOCKING (CHẤP NHẬN TỪ PENDING, CONFIRMED, PACKING)&lt;/b&gt;" style="shape=rect;html=1;whiteSpace=wrap;dashed=1;strokeColor=#CB4335;strokeWidth=1.8;fillColor=#FDEDEC;verticalAlign=top;align=left;spacingLeft=15;spacingTop=10;rounded=1;fontColor=#78281F;fontSize=12;" vertex="1" parent="1">
          <mxGeometry x="50" y="540" width="1600" height="420" as="geometry" />
        </mxCell>

        <!-- 6. Trạng thái CANCELLED -->
        <mxCell id="st_cancelled" value="&lt;b&gt;cancelled&lt;/b&gt;&lt;br&gt;&lt;font style=&quot;font-size: 11px; font-weight: normal;&quot;&gt;(Đã hủy đơn hàng)&lt;/font&gt;&lt;hr size=&quot;1&quot;&gt;&lt;div style=&quot;text-align: left; font-size: 11px; padding-left: 6px;&quot;&gt;&lt;b&gt;TÁC VỤ TỰ ĐỘNG KHI HỦY (cancelOrder):&lt;/b&gt;&lt;br&gt;1. &lt;b&gt;Hoàn tồn kho:&lt;/b&gt; &lt;font color=&quot;#1B4F72&quot;&gt;CHI_TIET_SP::increment('SoLuong', $item-&gt;qty)&lt;/font&gt;&lt;br&gt;2. &lt;b&gt;Hoàn voucher:&lt;/b&gt; &lt;font color=&quot;#7D6608&quot;&gt;MA_GIAM_GIA::increment('SoLuong', 1)&lt;/font&gt; (nếu có MaCode)&lt;br&gt;3. &lt;b&gt;Ghi vết lý do:&lt;/b&gt; Cập nhật cột GhiChu: &lt;i&gt;'Lý do hủy: ...'&lt;/i&gt;&lt;br&gt;4. &lt;b&gt;Bọc trong DB::transaction():&lt;/b&gt; Bảo đảm tính Atomic toàn diện&lt;/div&gt;" style="shape=swimlane;fontStyle=0;childLayout=stackLayout;horizontal=1;startSize=40;horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;whiteSpace=wrap;fillColor=#FADBD8;strokeColor=#CB4335;strokeWidth=2.2;fontColor=#78281F;fontSize=13;rounded=1;arcSize=10;" vertex="1" parent="1">
          <mxGeometry x="530" y="660" width="460" height="150" as="geometry" />
        </mxCell>

        <!-- Mũi tên Hủy 1: pending -> cancelled -->
        <mxCell id="arr_cancel_1" value="[Khách hủy / NV hủy]&lt;br&gt;cancelOrder('Khách đổi ý')" style="html=1;verticalAlign=bottom;endArrow=block;strokeColor=#CB4335;strokeWidth=2;fontColor=#922B21;fontSize=11;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="st_pending" target="st_cancelled">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="290" y="710" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- Mũi tên Hủy 2: confirmed -> cancelled -->
        <mxCell id="arr_cancel_2" value="[Hủy trước khi đóng gói]&lt;br&gt;cancelOrder('Hết hàng / Hủy')" style="html=1;verticalAlign=bottom;endArrow=block;strokeColor=#CB4335;strokeWidth=2;fontColor=#922B21;fontSize=11;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="st_confirmed" target="st_cancelled">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="600" y="580" />
              <mxPoint x="660" y="580" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- Mũi tên Hủy 3: packing -> cancelled -->
        <mxCell id="arr_cancel_3" value="[Hủy trước khi giao shipper]&lt;br&gt;cancelOrder('Khách yêu cầu hủy')" style="html=1;verticalAlign=bottom;endArrow=block;strokeColor=#CB4335;strokeWidth=2;fontColor=#922B21;fontSize=11;edgeStyle=orthogonalEdgeStyle;" edge="1" parent="1" source="st_packing" target="st_cancelled">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="900" y="580" />
              <mxPoint x="830" y="580" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- End Node cho Cancelled -->
        <mxCell id="node_end_cancelled" value="" style="ellipse;shape=doubleEllipse;fillColor=#78281F;strokeColor=#CB4335;strokeWidth=2;" vertex="1" parent="1">
          <mxGeometry x="745" y="870" width="30" height="30" as="geometry" />
        </mxCell>

        <mxCell id="arr_c_end" value="Đơn hàng kết thúc (Đã hủy)" style="html=1;verticalAlign=bottom;endArrow=block;strokeColor=#CB4335;strokeWidth=1.8;fontColor=#78281F;fontSize=10;" edge="1" parent="1" source="st_cancelled" target="node_end_cancelled">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- Box cảnh báo: Cấm hủy từ shipping & delivered -->
        <mxCell id="box_blocked" value="&lt;b&gt;QUY TẮC BẢO VỆ CHẶN HỦY (GUARD CONDITION):&lt;/b&gt;&lt;br&gt;• &lt;b&gt;shipping ➔ cancelled:&lt;/b&gt; &lt;font color=&quot;#C0392B&quot;&gt;&lt;b&gt;BỊ CHẶN&lt;/b&gt;&lt;/font&gt; (Hàng đã rời kho, không thể hủy ngang để bảo vệ chi phí vận chuyển)&lt;br&gt;• &lt;b&gt;delivered ➔ cancelled:&lt;/b&gt; &lt;font color=&quot;#C0392B&quot;&gt;&lt;b&gt;BỊ CHẶN&lt;/b&gt;&lt;/font&gt; (Đơn đã thành công, chỉ xử lý đổi trả qua nghiệp vụ riêng)&lt;br&gt;• &lt;i&gt;Mọi hành vi vi phạm đều ném ra ngoại lệ: Exception(&quot;Không thể hủy đơn hàng ở trạng thái...&quot;)&lt;/i&gt;" style="shape=rect;html=1;whiteSpace=wrap;dashed=0;strokeColor=#E74C3C;strokeWidth=1.5;fillColor=#FDEDEC;fontColor=#78281F;fontSize=11;align=left;spacingLeft=12;rounded=1;" vertex="1" parent="1">
          <mxGeometry x="1100" y="680" width="510" height="100" as="geometry" />
        </mxCell>

        <!-- Bảng chú thích (Legend) -->
        <mxCell id="legend_box" value="&lt;b&gt;MA TRẬN HỢP LỆ (ALLOWED_TRANSITIONS) TRONG CHECKOUTSERVICE:&lt;/b&gt;&lt;br&gt;• pending ➔ ['confirmed', 'cancelled'] | confirmed ➔ ['packing', 'cancelled'] | packing ➔ ['shipping', 'cancelled']&lt;br&gt;• shipping ➔ ['delivered'] | delivered ➔ [] (Trạng thái đích) | cancelled ➔ [] (Trạng thái đích)&lt;br&gt;• &lt;b&gt;Phân quyền tác nhân:&lt;/b&gt; Khách hàng chỉ có quyền hủy khi đơn ở trạng thái 'pending'. Quản trị viên/Nhân viên có quyền hủy từ 'pending', 'confirmed' và 'packing'." style="shape=rect;html=1;whiteSpace=wrap;dashed=0;strokeColor=#BDC3C7;strokeWidth=1.2;fillColor=#FFFFFF;verticalAlign=middle;align=left;spacingLeft=15;rounded=1;fontSize=11;fontColor=#333333;" vertex="1" parent="1">
          <mxGeometry x="50" y="980" width="1600" height="75" as="geometry" />
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

    drawio_file = docs_figures / "hinh_2_4_state_machine.drawio"
    drawio_file.write_text(DRAWIO_CONTENT, encoding="utf-8")
    (root_dir / "hinh_2_4_state_machine.drawio").write_text(DRAWIO_CONTENT, encoding="utf-8")
    print(f"Đã tạo tệp Draw.io: {drawio_file}")

    svg_content = """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1680 1120" width="1680" height="1120" style="background:#ffffff; font-family:'Segoe UI', Arial, sans-serif;">
  <defs>
    <style>
      .title { font-size: 20px; font-weight: bold; fill: #14221D; text-anchor: middle; }
      .subtitle { font-size: 14px; fill: #555555; text-anchor: middle; }
      .zone-title { font-size: 12px; font-weight: bold; }
      .st-title { font-size: 14px; font-weight: bold; }
      .st-sub { font-size: 11px; }
      .st-body { font-size: 11px; fill: #333333; }
      .arr-text { font-size: 11px; font-weight: 500; }
      .legend-text { font-size: 11px; fill: #333333; }
    </style>
    <marker id="arrow-green" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
      <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#16A085" />
    </marker>
    <marker id="arrow-blue" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
      <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#2980B9" />
    </marker>
    <marker id="arrow-gold" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
      <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#B7950B" />
    </marker>
    <marker id="arrow-darkgreen" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
      <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#229954" />
    </marker>
    <marker id="arrow-red" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
      <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#CB4335" />
    </marker>
  </defs>

  <!-- TIÊU ĐỀ -->
  <text x="840" y="38" class="title">HỆ THỐNG THƯƠNG MẠI ĐIỆN TỬ SMART CASUAL</text>
  <text x="840" y="62" class="subtitle">HÌNH 2.4: SƠ ĐỒ CHUYỂN ĐỔI TRẠNG THÁI ĐƠN HÀNG (STATE MACHINE DIAGRAM) - ALLOWED_TRANSITIONS</text>

  <!-- ==================== KHU VỰC I: LUỒNG CHUẨN ==================== -->
  <g transform="translate(40, 90)">
    <rect width="1600" height="420" rx="10" fill="#F2FAF7" stroke="#16A085" stroke-width="1.8" stroke-dasharray="6,4"/>
    <text x="20" y="26" class="zone-title" fill="#0E6251">I. LUỒNG THỰC HIỆN ĐƠN HÀNG CHUẨN (HAPPY PATH: PENDING ➔ CONFIRMED ➔ PACKING ➔ SHIPPING ➔ DELIVERED)</text>
  </g>

  <!-- Start Node -->
  <g transform="translate(75, 235)">
    <circle cx="15" cy="15" r="14" fill="#14221D"/>
    <text x="15" y="48" font-size="11" fill="#555555" text-anchor="middle">Khách đặt hàng</text>
    <text x="15" y="62" font-size="10" fill="#777777" text-anchor="middle">POST /checkout</text>
  </g>

  <!-- Mũi tên Start -> Pending -->
  <line x1="105" y1="250" x2="165" y2="250" stroke="#16A085" stroke-width="2" marker-end="url(#arrow-green)"/>
  <text x="135" y="242" font-size="11" font-weight="bold" fill="#0E6251" text-anchor="middle">placeOrder()</text>

  <!-- 1. State: PENDING -->
  <g transform="translate(170, 190)">
    <rect width="210" height="120" rx="8" fill="#FEF9E7" stroke="#D4AC0D" stroke-width="2"/>
    <rect width="210" height="38" rx="8" fill="#F9E79F"/>
    <text x="105" y="24" class="st-title" fill="#7D6608" text-anchor="middle">pending</text>
    <text x="105" y="48" class="st-sub" fill="#B7950B" text-anchor="middle">(Chờ xác nhận)</text>
    <line x1="0" y1="55" x2="210" y2="55" stroke="#D4AC0D" stroke-width="1"/>
    <text x="12" y="74" class="st-body">• <tspan font-weight="bold">entry /</tspan> Trừ tồn kho tạm thời</text>
    <text x="12" y="92" class="st-body">• <tspan font-weight="bold">entry /</tspan> Trừ 1 lượt voucher</text>
    <text x="12" y="110" class="st-body">• <tspan font-weight="bold">entry /</tspan> Khởi tạo MaDonHang</text>
  </g>

  <!-- Mũi tên Pending -> Confirmed -->
  <line x1="380" y1="250" x2="475" y2="250" stroke="#2980B9" stroke-width="2" marker-end="url(#arrow-blue)"/>
  <text x="430" y="235" class="arr-text" fill="#1B4F72" text-anchor="middle">[NV duyệt]</text>
  <text x="430" y="270" font-size="10" fill="#2980B9" text-anchor="middle">updateStatus('confirmed')</text>

  <!-- 2. State: CONFIRMED -->
  <g transform="translate(480, 190)">
    <rect width="220" height="120" rx="8" fill="#EBF5FB" stroke="#2980B9" stroke-width="2"/>
    <rect width="220" height="38" rx="8" fill="#D4E6F1"/>
    <text x="110" y="24" class="st-title" fill="#1B4F72" text-anchor="middle">confirmed</text>
    <text x="110" y="48" class="st-sub" fill="#2980B9" text-anchor="middle">(Đã xác nhận)</text>
    <line x1="0" y1="55" x2="220" y2="55" stroke="#2980B9" stroke-width="1"/>
    <text x="12" y="74" class="st-body">• <tspan font-weight="bold">entry /</tspan> Gán MaNV xác nhận</text>
    <text x="12" y="92" class="st-body">• Kiểm tra SĐT, địa chỉ giao</text>
    <text x="12" y="110" class="st-body">• Khóa thay đổi dòng mặt hàng</text>
  </g>

  <!-- Mũi tên Confirmed -> Packing -->
  <line x1="700" y1="250" x2="785" y2="250" stroke="#2980B9" stroke-width="2" marker-end="url(#arrow-blue)"/>
  <text x="742" y="235" class="arr-text" fill="#1B4F72" text-anchor="middle">[Kho xuất hàng]</text>
  <text x="742" y="270" font-size="10" fill="#2980B9" text-anchor="middle">updateStatus('packing')</text>

  <!-- 3. State: PACKING -->
  <g transform="translate(790, 190)">
    <rect width="210" height="120" rx="8" fill="#EBF5FB" stroke="#2980B9" stroke-width="2"/>
    <rect width="210" height="38" rx="8" fill="#D4E6F1"/>
    <text x="105" y="24" class="st-title" fill="#1B4F72" text-anchor="middle">packing</text>
    <text x="105" y="48" class="st-sub" fill="#2980B9" text-anchor="middle">(Đang đóng gói)</text>
    <line x1="0" y1="55" x2="210" y2="55" stroke="#2980B9" stroke-width="1"/>
    <text x="12" y="74" class="st-body">• <tspan font-weight="bold">entry /</tspan> In phiếu xuất kho</text>
    <text x="12" y="92" class="st-body">• So khớp đúng SKU &amp; Size</text>
    <text x="12" y="110" class="st-body">• Niêm phong kiện hàng</text>
  </g>

  <!-- Mũi tên Packing -> Shipping -->
  <line x1="1000" y1="250" x2="1085" y2="250" stroke="#B7950B" stroke-width="2" marker-end="url(#arrow-gold)"/>
  <text x="1042" y="235" class="arr-text" fill="#7D6608" text-anchor="middle">[Giao bưu tá]</text>
  <text x="1042" y="270" font-size="10" fill="#B7950B" text-anchor="middle">updateStatus('shipping')</text>

  <!-- 4. State: SHIPPING -->
  <g transform="translate(1090, 190)">
    <rect width="230" height="120" rx="8" fill="#FCF3CF" stroke="#B7950B" stroke-width="2"/>
    <rect width="230" height="38" rx="8" fill="#F9E79F"/>
    <text x="115" y="24" class="st-title" fill="#7D6608" text-anchor="middle">shipping</text>
    <text x="115" y="48" class="st-sub" fill="#B7950B" text-anchor="middle">(Đang giao hàng)</text>
    <line x1="0" y1="55" x2="230" y2="55" stroke="#B7950B" stroke-width="1"/>
    <text x="12" y="74" class="st-body">• <tspan font-weight="bold">entry /</tspan> Bàn giao Shipper / PTVC</text>
    <text x="12" y="92" class="st-body" fill="#C0392B" font-weight="bold">• RÀNG BUỘC: KHÔNG THỂ HỦY</text>
    <text x="12" y="110" class="st-body">• Theo dõi lộ trình vận đơn</text>
  </g>

  <!-- Mũi tên Shipping -> Delivered -->
  <line x1="1320" y1="250" x2="1395" y2="250" stroke="#229954" stroke-width="2.2" marker-end="url(#arrow-darkgreen)"/>
  <text x="1358" y="235" class="arr-text" fill="#145A32" text-anchor="middle">[Khách nhận hàng]</text>
  <text x="1358" y="270" font-size="10" fill="#229954" text-anchor="middle">updateStatus('delivered')</text>

  <!-- 5. State: DELIVERED (Terminal) -->
  <g transform="translate(1400, 190)">
    <rect width="220" height="130" rx="8" fill="#D4EFDF" stroke="#229954" stroke-width="2.2"/>
    <rect width="220" height="38" rx="8" fill="#A9DFBF"/>
    <text x="110" y="24" class="st-title" fill="#145A32" text-anchor="middle">delivered</text>
    <text x="110" y="48" class="st-sub" fill="#196F3D" text-anchor="middle">(Đã giao hàng - Hoàn tất)</text>
    <line x1="0" y1="55" x2="220" y2="55" stroke="#229954" stroke-width="1"/>
    <text x="12" y="74" class="st-body">• <tspan font-weight="bold">entry /</tspan> Nếu COD: DaThanhToan=1</text>
    <text x="12" y="92" class="st-body">• <tspan font-weight="bold">entry /</tspan> Tích điểm thành viên</text>
    <text x="12" y="110" class="st-body">• Cho phép viết Đánh giá sao</text>
    <text x="12" y="125" class="st-body" font-style="italic" fill="#229954">✓ Trạng thái đích thành công</text>
  </g>

  <!-- End Node Delivered -->
  <g transform="translate(1495, 360)">
    <circle cx="15" cy="15" r="14" fill="#145A32" stroke="#229954" stroke-width="3"/>
    <circle cx="15" cy="15" r="8" fill="#ffffff"/>
    <text x="15" y="45" font-size="10" fill="#145A32" text-anchor="middle">Giao dịch thành công</text>
  </g>
  <line x1="1510" y1="320" x2="1510" y2="355" stroke="#229954" stroke-width="1.8" marker-end="url(#arrow-darkgreen)"/>


  <!-- ==================== KHU VỰC II: LUỒNG HỦY ==================== -->
  <g transform="translate(40, 540)">
    <rect width="1600" height="420" rx="10" fill="#FDEDEC" stroke="#CB4335" stroke-width="1.8" stroke-dasharray="6,4"/>
    <text x="20" y="26" class="zone-title" fill="#78281F">II. LUỒNG HỦY ĐƠN HÀNG &amp; HOÀN NGUYÊN TỒN KHO ATOMIC RESTOCKING (CHẤP NHẬN TỪ PENDING, CONFIRMED, PACKING)</text>
  </g>

  <!-- 6. State: CANCELLED (Terminal) -->
  <g transform="translate(520, 650)">
    <rect width="480" height="165" rx="8" fill="#FADBD8" stroke="#CB4335" stroke-width="2.2"/>
    <rect width="480" height="40" rx="8" fill="#F5B7B1"/>
    <text x="240" y="26" class="st-title" fill="#78281F" text-anchor="middle">cancelled (Đã hủy đơn hàng)</text>
    <line x1="0" y1="50" x2="480" y2="50" stroke="#CB4335" stroke-width="1.2"/>
    <text x="15" y="72" class="st-body" font-weight="bold" fill="#78281F">TÁC VỤ TỰ ĐỘNG KHI HỦY (cancelOrder trong CheckoutService):</text>
    <text x="25" y="94" class="st-body">1. <tspan font-weight="bold">Hoàn tồn kho SKU:</tspan> CHI_TIET_SP::where('MaCTSP', $item-&gt;MaCTSP)-&gt;increment('SoLuong', $item-&gt;SoLuong);</text>
    <text x="25" y="116" class="st-body">2. <tspan font-weight="bold">Hoàn lượt voucher:</tspan> MaGiamGia::where('MaCode', $order-&gt;MaCode)-&gt;increment('SoLuong', 1); (nếu có)</text>
    <text x="25" y="138" class="st-body">3. <tspan font-weight="bold">Ghi nhận vết lý do:</tspan> Cập nhật cột GhiChu: 'Lý do hủy: ...'</text>
    <text x="25" y="156" class="st-body">4. <tspan font-weight="bold">Bọc trong DB::transaction():</tspan> Cam kết bảo toàn tính nhất quán tuyệt đối</text>
  </g>

  <!-- Mũi tên Hủy 1: Pending -> Cancelled -->
  <path d="M 275 310 L 275 730 L 515 730" fill="none" stroke="#CB4335" stroke-width="2" marker-end="url(#arrow-red)"/>
  <text x="290" y="580" class="arr-text" fill="#922B21">[Khách hủy / NV hủy]</text>
  <text x="290" y="598" font-size="10" fill="#CB4335">cancelOrder('Khách đổi ý')</text>

  <!-- Mũi tên Hủy 2: Confirmed -> Cancelled -->
  <path d="M 590 310 L 590 645" fill="none" stroke="#CB4335" stroke-width="2" marker-end="url(#arrow-red)"/>
  <text x="605" y="470" class="arr-text" fill="#922B21">[Hủy trước đóng gói]</text>
  <text x="605" y="488" font-size="10" fill="#CB4335">cancelOrder('Hết hàng')</text>

  <!-- Mũi tên Hủy 3: Packing -> Cancelled -->
  <path d="M 895 310 L 895 645" fill="none" stroke="#CB4335" stroke-width="2" marker-end="url(#arrow-red)"/>
  <text x="910" y="470" class="arr-text" fill="#922B21">[Hủy trước khi giao shipper]</text>
  <text x="910" y="488" font-size="10" fill="#CB4335">cancelOrder('Khách gọi hủy')</text>

  <!-- End Node Cancelled -->
  <g transform="translate(745, 850)">
    <circle cx="15" cy="15" r="14" fill="#78281F" stroke="#CB4335" stroke-width="3"/>
    <circle cx="15" cy="15" r="8" fill="#ffffff"/>
    <text x="15" y="45" font-size="10" fill="#78281F" text-anchor="middle">Đơn hàng kết thúc (Đã hủy)</text>
  </g>
  <line x1="760" y1="815" x2="760" y2="845" stroke="#CB4335" stroke-width="1.8" marker-end="url(#arrow-red)"/>

  <!-- BOX CẢNH BÁO CHẶN HỦY -->
  <g transform="translate(1080, 670)">
    <rect width="530" height="120" rx="8" fill="#FDEDEC" stroke="#E74C3C" stroke-width="1.8"/>
    <text x="15" y="24" font-size="12" font-weight="bold" fill="#78281F">QUY TẮC BẢO VỆ CHẶN HỦY (GUARD CONDITION):</text>
    <text x="15" y="48" font-size="11" fill="#333333">• <tspan font-weight="bold">shipping ➔ cancelled:</tspan> <tspan font-weight="bold" fill="#C0392B">BỊ CHẶN TUYỆT ĐỐI</tspan></text>
    <text x="25" y="66" font-size="10.5" fill="#555555">(Hàng đã bàn giao bưu tá xuất bến, không thể hủy ngang để bảo vệ cước vận chuyển)</text>
    <text x="15" y="88" font-size="11" fill="#333333">• <tspan font-weight="bold">delivered ➔ cancelled:</tspan> <tspan font-weight="bold" fill="#C0392B">BỊ CHẶN TUYỆT ĐỐI</tspan></text>
    <text x="25" y="106" font-size="10.5" fill="#555555">(Đơn hàng đã hoàn tất giao dịch; khiếu nại xử lý qua quy trình Đổi trả riêng)</text>
  </g>

  <!-- BẢNG CHÚ THÍCH (LEGEND) -->
  <g transform="translate(40, 980)">
    <rect width="1600" height="85" rx="6" fill="#FFFFFF" stroke="#BDC3C7" stroke-width="1.2"/>
    <text x="15" y="22" font-size="12" font-weight="bold" fill="#2C3E50">MA TRẬN HỢP LỆ (ALLOWED_TRANSITIONS) TRONG CHECKOUTSERVICE:</text>
    <text x="15" y="42" class="legend-text">• <tspan font-weight="bold">pending</tspan> ➔ ['confirmed', 'cancelled'] | <tspan font-weight="bold">confirmed</tspan> ➔ ['packing', 'cancelled'] | <tspan font-weight="bold">packing</tspan> ➔ ['shipping', 'cancelled']</text>
    <text x="15" y="60" class="legend-text">• <tspan font-weight="bold">shipping</tspan> ➔ ['delivered'] | <tspan font-weight="bold">delivered</tspan> ➔ [] (Trạng thái đích thành công) | <tspan font-weight="bold">cancelled</tspan> ➔ [] (Trạng thái đích hủy)</text>
    <text x="15" y="76" class="legend-text">• <tspan font-weight="bold">Phân quyền tác nhân:</tspan> Khách hàng thành viên chỉ được hủy khi đơn ở trạng thái 'pending'. Quản trị viên và Nhân viên có quyền hủy từ 'pending', 'confirmed' và 'packing'.</text>
  </g>

</svg>
"""

    svg_file = docs_figures / "hinh_2_4_state_machine.svg"
    svg_file.write_text(svg_content, encoding="utf-8")
    (root_dir / "hinh_2_4_state_machine.svg").write_text(svg_content, encoding="utf-8")
    print(f"Đã tạo tệp Vector SVG: {svg_file}")

    edge_paths = [
        r"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe",
        r"C:\Program Files\Microsoft\Edge\Application\msedge.exe",
    ]
    edge_exe = next((p for p in edge_paths if os.path.exists(p)), None)
    if not edge_exe:
        print("Không tìm thấy Microsoft Edge.")
        return

    png_file = docs_figures / "hinh_2_4_state_machine.png"
    html_wrapper = docs_figures / "temp_render_2_4.html"
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
        "--window-size=1760,1200",
        f"--screenshot={str(png_file)}",
        str(html_wrapper)
    ]
    subprocess.run(cmd, check=True)
    if png_file.exists():
        (root_dir / "hinh_2_4_state_machine.png").write_bytes(png_file.read_bytes())
        print(f"Đã xuất ảnh PNG HD: {png_file} ({png_file.stat().st_size} bytes)")
    if html_wrapper.exists():
        html_wrapper.unlink()

if __name__ == "__main__":
    generate_svg_and_png()
