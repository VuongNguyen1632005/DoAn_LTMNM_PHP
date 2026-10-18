from pathlib import Path
import math
from PIL import Image, ImageDraw, ImageFont

out = Path(__file__).parent
scale = 240
im = Image.new('RGB', (3000, 2400), 'white')
d = ImageDraw.Draw(im)
ink, blue, orange = '#172335', '#245c88', '#895020'

def xy(p):
    return (round(p[0] * scale), round((10 - p[1]) * scale))

def text(x, y, s, size=13, bold=False, color=ink, ha='center'):
    f = ImageFont.truetype(r'C:\Windows\Fonts\arialbd.ttf' if bold else r'C:\Windows\Fonts\arial.ttf', round(size * 3.1))
    anchor = 'lm' if ha == 'left' else 'mm'
    d.multiline_text(xy((x, y)), s, font=f, fill=color, anchor=anchor, align='left' if ha == 'left' else 'center', spacing=14)

def box(x, y, label, w=1.92, h=.66, color=blue, fill='#f1f6fa'):
    d.rounded_rectangle([xy((x-w/2, y+h/2)), xy((x+w/2, y-h/2))], radius=20, fill=fill, outline=color, width=3)
    text(x, y, label, 12.5, color=color)

def line(points, color=blue, style='-'):
    ps = [xy(p) for p in points]
    if style == '-':
        d.line(ps, fill=color, width=3)
    else:
        for a, b in zip(ps, ps[1:]):
            length = math.dist(a, b)
            if not length:
                continue
            for start in range(0, math.ceil(length), 22):
                end = min(start + 13, length)
                p = (a[0] + (b[0]-a[0])*start/length, a[1] + (b[1]-a[1])*start/length)
                q = (a[0] + (b[0]-a[0])*end/length, a[1] + (b[1]-a[1])*end/length)
                d.line([p, q], fill=color, width=3)

def arrow(a, b, color=blue, style='-'):
    line([a, b], color, style)
    p, q = xy(a), xy(b)
    angle = math.atan2(q[1]-p[1], q[0]-p[0])
    points = [q]
    for offset in [-.45, .45]:
        points.append((q[0]-19*math.cos(angle+offset), q[1]-19*math.sin(angle+offset)))
    d.polygon(points, fill=color)

text(6.25, 9.65, 'LUỒNG MUA HÀNG, ĐƠN HÀNG VÀ GIAO HÀNG', 18, True)
text(.3, 9.02, '1. Mua hàng', 14, True, ha='left')
xs = [1.5, 4.5, 7.5, 10.5]
for x, label in zip(xs, ['Sản phẩm', 'Giỏ hàng', 'Checkout', 'Order mới']):
    box(x, 8.47, label, w=2.1)
for a, b in zip(xs, xs[1:]):
    arrow((a+1.1, 8.47), (b-1.1, 8.47))
text(6.25, 7.83, 'Checkout kiểm tra lại giá, tồn kho, voucher và phí giao hàng.\nOrder mới: pending; COD: unpaid.', 12)

text(.3, 7.13, '2. Xử lý đơn hàng — Order status', 14, True, ha='left')
ox = [1.3, 3.65, 6, 8.35, 10.7]
for x, label in zip(ox, ['pending', 'confirmed', 'packing', 'shipping', 'delivered']):
    box(x, 6.55, label)
for a, b in zip(ox, ox[1:]):
    arrow((a+1.0, 6.55), (b-1.0, 6.55))
box(3.65, 5.43, 'cancelled', color=orange, fill='#fff7ed')
for x in ox[:3]:
    line([(x, 6.22), (x, 5.99), (3.65, 5.99)], orange)
arrow((3.65, 5.99), (3.65, 5.76), orange)
text(3.65, 4.87, 'Chỉ hủy trước shipping và khi chưa thanh toán.', 11, color=orange)
text(8.35, 5.47, 'Vào shipping:\nkhởi tạo delivery_tracking', 11)
arrow((8.35, 6.22), (8.35, 5.98))
arrow((10.7, 3.76), (10.7, 6.22), style='--')
text(11.86, 5.18, 'Đồng bộ\ndelivered', 10.5, color=blue)

text(.3, 4.37, '3. Giao hàng — Delivery status', 14, True, ha='left')
for x, label in zip(ox, ['created', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered']):
    box(x, 3.43, label)
for a, b in zip(ox, ox[1:]):
    arrow((a+1.0, 3.43), (b-1.0, 3.43))
box(6, 2.04, 'failed_delivery', color=orange, fill='#fff7ed', w=2.35)
for x in ox[:4]:
    line([(x, 3.1), (x, 2.7)], orange, '--')
line([(ox[0], 2.7), (ox[3], 2.7)], orange, '--')
arrow((6, 2.7), (6, 2.37), orange, '--')
line([(7.175, 2.04), (8.7, 2.04), (8.7, 2.9)], blue)
arrow((8.7, 2.9), (8.7, 3.1))
line([(4.825, 2.04), (4.5, 2.04), (4.5, 2.48), (5.6, 2.48), (5.6, 2.9)], blue)
arrow((5.6, 2.9), (5.6, 3.1))
text(9.65, 2.15, 'Thử giao lại', 11, color=blue)
text(3.45, 2.13, 'Thử giao lại', 11, color=blue)
text(6.25, 1.24, 'Failed delivery có thể phát sinh từ 4 bước trước delivered.\nRetry chỉ về in_transit hoặc out_for_delivery; order vẫn ở shipping.', 11.5)
line([(.25, .72), (12.2, .72)], '#cbd2db')
text(6.25, .43, 'Payment status được quản lý riêng; delivered không đồng nghĩa paid.\nSơ đồ thể hiện luồng staff; chủ đơn có luồng xác nhận đã nhận theo quyền hiện có.', 10.5)
path = out / 'figure_05_01_order_delivery_workflow.png'
im.save(path, dpi=(240, 240))
print(path)
