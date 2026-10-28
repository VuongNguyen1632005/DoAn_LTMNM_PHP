import subprocess
import os

edge_exe = r"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe"
svg_path = os.path.abspath(r"D:\PHP_NOSQL\SMART_CASUAL_LARAVEL\docs\figures\hinh_2_1_uml_use_case.svg")
png_path = os.path.abspath(r"D:\PHP_NOSQL\SMART_CASUAL_LARAVEL\docs\figures\hinh_2_1_uml_use_case.png")
tmp_profile = os.path.join(os.environ.get("TEMP", "C:\\Temp"), "edge_shot_profile")

cmd = [
    edge_exe,
    "--headless",
    "--disable-gpu",
    "--no-sandbox",
    f"--user-data-dir={tmp_profile}",
    f"--screenshot={png_path}",
    "--window-size=1540,1050",
    f"file:///{svg_path.replace(os.sep, '/')}"
]

print("Running command:", " ".join(cmd))
res = subprocess.run(cmd, capture_output=True, text=True)
print("Return code:", res.returncode)
print("PNG exists:", os.path.exists(png_path))
if os.path.exists(png_path):
    print("PNG size:", os.path.getsize(png_path))
