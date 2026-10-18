"""Draw the Chapter 13 architecture diagram from the project's PHP/CouchDB wiring."""

from pathlib import Path
from PIL import Image, ImageDraw, ImageFont

OUT = Path(__file__).with_name('figure_13_01_php_couchdb_fauxton_architecture.png')
W, H = 2800, 1550
im = Image.new('RGB', (W, H), '#ffffff')
d = ImageDraw.Draw(im)
ink = '#172335'
blue = '#245c88'
orange = '#986026'
muted = '#5f6c7b'

def font(size, bold=False):
    name = 'arialbd.ttf' if bold else 'arial.ttf'
    return ImageFont.truetype('C:/Windows/Fonts/' + name, size)

def label(center, value, size=35, bold=False, color=ink, spacing=12):
    d.multiline_text(center, value, font=font(size, bold), anchor='mm',
                     align='center', spacing=spacing, fill=color)

def card(bounds, fill, border, heading, body):
    d.rounded_rectangle(bounds, radius=26, fill=fill, outline=border, width=4)
    x1, y1, x2, y2 = bounds
    center = ((x1+x2)//2, (y1+y2)//2)
    label((center[0], center[1]-35), heading, 42, True, border)
    label((center[0], center[1]+45), body, 32, False, ink)

def arrow(points, color=blue, width=6, dashed=False):
    for a, b in zip(points, points[1:]):
        if dashed:
            import math
            distance = math.dist(a, b)
            for step in range(0, int(distance), 30):
                end = min(step + 18, distance)
                p = tuple(round(a[i]+(b[i]-a[i])*step/distance) for i in range(2))
                q = tuple(round(a[i]+(b[i]-a[i])*end/distance) for i in range(2))
                d.line((p, q), fill=color, width=width)
        else:
            d.line((a, b), fill=color, width=width, joint='curve')
    x1, y1 = points[-2]
    x2, y2 = points[-1]
    import math
    angle = math.atan2(y2-y1, x2-x1)
    tip = (x2, y2)
    left = (x2-25*math.cos(angle-.5), y2-25*math.sin(angle-.5))
    right = (x2-25*math.cos(angle+.5), y2-25*math.sin(angle+.5))
    d.polygon((tip, left, right), fill=color)

label((W//2, 125), 'KIẾN TRÚC ỨNG DỤNG PHP – COUCHDB', 65, True)
label((W//2, 205), 'Website nghiệp vụ và Fauxton cùng truy cập một database CouchDB', 36, color=muted)

card((95, 395, 570, 715), '#f1f6fa', blue,
     'Người dùng', 'Trình duyệt web\nhoặc JSON API')
card((720, 395, 1320, 715), '#f1f6fa', blue,
     'Ứng dụng PHP', 'Router → Controller\n→ Service')
card((1470, 395, 2070, 715), '#f1f6fa', blue,
     'Tầng dữ liệu PHP', 'Repository\n→ CouchDbClient')
card((2270, 395, 2700, 715), '#eaf3ef', '#27704d',
     'CouchDB', 'Document, Mango\nvà index')

arrow([(570, 555), (720, 555)])
arrow([(1320, 555), (1470, 555)])
arrow([(2070, 555), (2270, 555)])
label((645, 490), 'HTTP', 28, color=muted)
label((1395, 490), 'Gọi nội bộ', 28, color=muted)
label((2170, 490), 'REST / JSON', 28, color=muted)

card((535, 965, 1270, 1245), '#fff7eb', orange,
     'Fauxton', 'Giao diện quản trị CouchDB\nXem document, query, index')
arrow([(1270, 1105), (2485, 1105), (2485, 720)], orange, 6, True)
label((1750, 1040), 'HTTP / REST trực tiếp tới CouchDB', 30, color=orange)

d.rounded_rectangle((135, 1330, 2665, 1455), radius=20,
                    fill='#f6f8fa', outline='#d3dbe4', width=2)
label((1400, 1390),
      'Giỏ hàng khách chưa đăng nhập dùng PHP session; thông tin đăng nhập CouchDB nằm ở phía server.',
      32, color=ink)

im.save(OUT, dpi=(240, 240))
print(OUT)
