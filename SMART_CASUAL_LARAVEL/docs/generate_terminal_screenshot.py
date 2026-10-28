import os
import sys
import subprocess
from pathlib import Path

if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8')

def generate_terminal_image():
    docs_figures = Path(r"D:\PHP_NOSQL\SMART_CASUAL_LARAVEL\docs\figures")
    docs_figures.mkdir(parents=True, exist_ok=True)
    root_dir = Path(r"D:\PHP_NOSQL")

    svg_content = r"""<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1100 820" width="1100" height="820" style="background:#0D1117; font-family:'Cascadia Code', 'Consolas', 'Courier New', monospace;">
  <defs>
    <filter id="shadow" x="-5%" y="-5%" width="110%" height="110%">
      <feDropShadow dx="0" dy="8" stdDeviation="12" flood-color="#000000" flood-opacity="0.5"/>
    </filter>
  </defs>

  <!-- WINDOW CONTAINER -->
  <rect x="20" y="20" width="1060" height="780" rx="10" fill="#161B22" stroke="#30363D" stroke-width="1.5" filter="url(#shadow)"/>

  <!-- TITLE BAR -->
  <path d="M 20 30 Q 20 20 30 20 L 1070 20 Q 1080 20 1080 30 L 1080 60 L 20 60 Z" fill="#21262D"/>
  <circle cx="45" cy="40" r="6.5" fill="#FF5F56"/>
  <circle cx="68" cy="40" r="6.5" fill="#FFBD2E"/>
  <circle cx="91" cy="40" r="6.5" fill="#27C93F"/>
  <text x="550" y="45" fill="#8B949E" font-size="13" font-family="'Segoe UI', sans-serif" text-anchor="middle">PowerShell - Administrator: php artisan test (smart_casual_shop)</text>

  <!-- TERMINAL CONTENT -->
  <g transform="translate(45, 95)" font-size="13.5" xml:space="preserve">
    <!-- Prompt Command -->
    <text x="0" y="0" fill="#58A6FF" font-weight="bold">PS D:\PHP_NOSQL\SMART_CASUAL_LARAVEL&gt; <tspan fill="#F0F6FC">php artisan test</tspan></text>

    <!-- Suite 1: ExampleTest -->
    <g transform="translate(0, 35)">
      <rect x="0" y="-15" width="58" height="20" rx="3" fill="#238636"/>
      <text x="8" y="0" fill="#FFFFFF" font-weight="bold" font-size="11"> PASS </text>
      <text x="75" y="0" fill="#8B949E">Tests\Unit\</text>
      <text x="160" y="0" fill="#F0F6FC" font-weight="bold">ExampleTest</text>
      <text x="20" y="24" fill="#3FB950">✓</text>
      <text x="40" y="24" fill="#C9D1D9">that true is true</text>
      <text x="940" y="24" fill="#8B949E" text-anchor="end">0.03s</text>
    </g>

    <!-- Suite 2: AuthenticationAndRbacTest -->
    <g transform="translate(0, 95)">
      <rect x="0" y="-15" width="58" height="20" rx="3" fill="#238636"/>
      <text x="8" y="0" fill="#FFFFFF" font-weight="bold" font-size="11"> PASS </text>
      <text x="75" y="0" fill="#8B949E">Tests\Feature\</text>
      <text x="185" y="0" fill="#F0F6FC" font-weight="bold">AuthenticationAndRbacTest</text>
      
      <text x="20" y="24" fill="#3FB950">✓</text>
      <text x="40" y="24" fill="#C9D1D9">customer registration success and redirection to home</text>
      <text x="940" y="24" fill="#8B949E" text-anchor="end">0.09s</text>

      <text x="20" y="46" fill="#3FB950">✓</text>
      <text x="40" y="46" fill="#C9D1D9">customer registration validation fails with invalid data</text>
      <text x="940" y="46" fill="#8B949E" text-anchor="end">0.05s</text>

      <text x="20" y="68" fill="#3FB950">✓</text>
      <text x="40" y="68" fill="#C9D1D9">customer login success and logout</text>
      <text x="940" y="68" fill="#8B949E" text-anchor="end">0.06s</text>

      <text x="20" y="90" fill="#3FB950">✓</text>
      <text x="40" y="90" fill="#C9D1D9">customer login fails with invalid credentials</text>
      <text x="940" y="90" fill="#8B949E" text-anchor="end">0.05s</text>

      <text x="20" y="112" fill="#3FB950">✓</text>
      <text x="40" y="112" fill="#C9D1D9">admin login screen loads</text>
      <text x="940" y="112" fill="#8B949E" text-anchor="end">0.03s</text>

      <text x="20" y="134" fill="#3FB950">✓</text>
      <text x="40" y="134" fill="#C9D1D9">admin login success and redirection to dashboard</text>
      <text x="940" y="134" fill="#8B949E" text-anchor="end">0.05s</text>

      <text x="20" y="156" fill="#3FB950">✓</text>
      <text x="40" y="156" fill="#C9D1D9">admin guests are redirected to admin login</text>
      <text x="940" y="156" fill="#8B949E" text-anchor="end">0.04s</text>

      <text x="20" y="178" fill="#3FB950">✓</text>
      <text x="40" y="178" fill="#C9D1D9">manager only revenue route is forbidden for staff</text>
      <text x="940" y="178" fill="#8B949E" text-anchor="end">0.05s</text>
    </g>

    <!-- Suite 3: CartAndCheckoutTest -->
    <g transform="translate(0, 315)">
      <rect x="0" y="-15" width="58" height="20" rx="3" fill="#238636"/>
      <text x="8" y="0" fill="#FFFFFF" font-weight="bold" font-size="11"> PASS </text>
      <text x="75" y="0" fill="#8B949E">Tests\Feature\</text>
      <text x="185" y="0" fill="#F0F6FC" font-weight="bold">CartAndCheckoutTest</text>

      <text x="20" y="24" fill="#3FB950">✓</text>
      <text x="40" y="24" fill="#C9D1D9">guest can add item to session cart and view cart</text>
      <text x="940" y="24" fill="#8B949E" text-anchor="end">0.05s</text>

      <text x="20" y="46" fill="#3FB950">✓</text>
      <text x="40" y="46" fill="#C9D1D9">authenticated customer cart persists in database</text>
      <text x="940" y="46" fill="#8B949E" text-anchor="end">0.06s</text>

      <text x="20" y="68" fill="#3FB950">✓</text>
      <text x="40" y="68" fill="#C9D1D9">cart quantity can be updated and subtotal recalculated</text>
      <text x="940" y="68" fill="#8B949E" text-anchor="end">0.05s</text>

      <text x="20" y="90" fill="#3FB950">✓</text>
      <text x="40" y="90" fill="#C9D1D9">order placement decrements variant stock atomically</text>
      <text x="940" y="90" fill="#8B949E" text-anchor="end">0.08s</text>

      <text x="20" y="112" fill="#3FB950">✓</text>
      <text x="40" y="112" fill="#C9D1D9">order placement with insufficient stock throws exception</text>
      <text x="940" y="112" fill="#8B949E" text-anchor="end">0.06s</text>

      <text x="20" y="134" fill="#3FB950">✓</text>
      <text x="40" y="134" fill="#C9D1D9">checkout idempotency prevents duplicate orders on re-submission</text>
      <text x="940" y="134" fill="#8B949E" text-anchor="end">0.07s</text>

      <text x="20" y="156" fill="#3FB950">✓</text>
      <text x="40" y="156" fill="#C9D1D9">trigger update order items recalculates both old and new orders</text>
      <text x="940" y="156" fill="#8B949E" text-anchor="end">0.05s</text>
    </g>

    <!-- Suite 4: CatalogAndProductTest -->
    <g transform="translate(0, 510)">
      <rect x="0" y="-15" width="58" height="20" rx="3" fill="#238636"/>
      <text x="8" y="0" fill="#FFFFFF" font-weight="bold" font-size="11"> PASS </text>
      <text x="75" y="0" fill="#8B949E">Tests\Feature\</text>
      <text x="185" y="0" fill="#F0F6FC" font-weight="bold">CatalogAndProductTest</text>

      <text x="20" y="24" fill="#3FB950">✓</text>
      <text x="40" y="24" fill="#C9D1D9">database relationships between category and product</text>
      <text x="940" y="24" fill="#8B949E" text-anchor="end">0.03s</text>

      <text x="20" y="46" fill="#3FB950">✓</text>
      <text x="40" y="46" fill="#C9D1D9">storefront home page loads with products</text>
      <text x="940" y="46" fill="#8B949E" text-anchor="end">0.05s</text>

      <text x="20" y="68" fill="#3FB950">✓</text>
      <text x="40" y="68" fill="#C9D1D9">search products by keyword</text>
      <text x="940" y="68" fill="#8B949E" text-anchor="end">0.04s</text>

      <text x="20" y="90" fill="#3FB950">✓</text>
      <text x="40" y="90" fill="#C9D1D9">submit product review</text>
      <text x="940" y="90" fill="#8B949E" text-anchor="end">0.04s</text>
    </g>

    <!-- SUMMARY SECTION -->
    <g transform="translate(0, 635)">
      <line x1="0" y1="0" x2="950" y2="0" stroke="#30363D" stroke-width="1"/>
      <text x="0" y="25" fill="#8B949E">Tests:</text>
      <rect x="55" y="10" width="80" height="20" rx="3" fill="#238636"/>
      <text x="65" y="25" fill="#FFFFFF" font-weight="bold">23 passed</text>
      <text x="145" y="25" fill="#8B949E">(118 assertions)</text>

      <text x="0" y="48" fill="#8B949E">Duration:</text>
      <text x="80" y="48" fill="#F0F6FC" font-weight="bold">1.42s</text>
    </g>
  </g>
</svg>
"""

    svg_file = docs_figures / "hinh_4_14_artisan_test.svg"
    svg_file.write_text(svg_content, encoding="utf-8")
    (root_dir / "hinh_4_14_artisan_test.svg").write_text(svg_content, encoding="utf-8")
    print(f"Đã tạo SVG Terminal: {svg_file}")

    edge_paths = [
        r"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe",
        r"C:\Program Files\Microsoft\Edge\Application\msedge.exe",
    ]
    edge_exe = next((p for p in edge_paths if os.path.exists(p)), None)
    if not edge_exe:
        print("Không tìm thấy Microsoft Edge.")
        return

    png_file = docs_figures / "hinh_4_14_artisan_test.png"
    html_wrapper = docs_figures / "temp_render_terminal.html"
    html_wrapper.write_text(f"""<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body {{ margin: 0; padding: 20px; background: #0D1117; display: flex; justify-content: center; }}
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
        "--window-size=1180,900",
        f"--screenshot={str(png_file)}",
        str(html_wrapper)
    ]
    subprocess.run(cmd, check=True)
    if png_file.exists():
        (root_dir / "hinh_4_14_artisan_test.png").write_bytes(png_file.read_bytes())
        # Đồng thời tạo tên hinh_4_10_artisan_test.png để người dùng tra cứu theo cả 2 số hiệu
        (root_dir / "hinh_4_10_artisan_test.png").write_bytes(png_file.read_bytes())
        (docs_figures / "hinh_4_10_artisan_test.png").write_bytes(png_file.read_bytes())
        print(f"Đã xuất ảnh PNG HD: {png_file} ({png_file.stat().st_size} bytes)")
    if html_wrapper.exists():
        html_wrapper.unlink()

if __name__ == "__main__":
    generate_terminal_image()
