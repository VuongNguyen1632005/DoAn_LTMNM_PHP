import sys

sys.stdout.reconfigure(encoding='utf-8')

try:
    import cairosvg
    cairosvg.svg2png(url="docs/figures/hinh_2_1_uml_use_case.svg", write_to="docs/figures/hinh_2_1_uml_use_case.png", output_width=1520, output_height=1020)
    print("CairoSVG conversion successful!")
except Exception as e:
    print("CairoSVG failed:", e)
