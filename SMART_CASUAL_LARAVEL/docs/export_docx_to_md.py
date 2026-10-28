import docx
from pathlib import Path
import sys

sys.stdout.reconfigure(encoding='utf-8')

docx_path = Path(r"D:\PHP_NOSQL\SMART_CASUAL_LARAVEL\docs\BAO_CAO_DO_AN_MON_HOC_LARAVEL_5_CHUONG.docx")
doc = docx.Document(docx_path)

md_lines = []

for child in doc.element.body:
    tag = child.tag.split('}')[-1]
    if tag == 'p':
        p = docx.text.paragraph.Paragraph(child, doc)
        text = p.text.strip()
        if not text:
            continue
        
        if text.startswith("CHƯƠNG ") or text in ["LỜI CẢM ƠN", "NHẬN XÉT CỦA GIẢNG VIÊN HƯỚNG DẪN", "MỤC LỤC", "DANH MỤC TỪ VIẾT TẮT", "DANH MỤC HÌNH ẢNH", "DANH MỤC BẢNG BIỂU", "KẾT LUẬN", "TÀI LIỆU THAM KHẢO"]:
            md_lines.append(f"\n# {text}\n")
        elif any(text.startswith(f"{c}.") for c in range(1, 6)) and len(text.split('.')[0]) == 1 and text[1] == '.':
            md_lines.append(f"\n## {text}\n")
        elif text.startswith("Hình ") and ":" in text:
            md_lines.append(f"\n*{text}*\n")
        elif text.startswith("Bảng ") and ":" in text:
            md_lines.append(f"\n**{text}**\n")
        elif "Tests:" in text and "passed" in text or "PASS  Tests" in text:
            md_lines.append(f"\n```text\n{text}\n```\n")
        elif text.startswith("[HÌNH ") or text.startswith("[BẢNG "):
            md_lines.append(f"\n> **{text}**\n")
        else:
            md_lines.append(f"\n{text}\n")
            
    elif tag == 'tbl':
        tbl = docx.table.Table(child, doc)
        rows_data = []
        for row in tbl.rows:
            r_data = []
            for cell in row.cells:
                cell_text = " ".join([p.text.strip() for p in cell.paragraphs if p.text.strip()])
                cell_text = cell_text.replace("\n", "<br>")
                r_data.append(cell_text)
            rows_data.append(r_data)
        
        if rows_data:
            num_cols = len(rows_data[0])
            header = "| " + " | ".join(rows_data[0]) + " |"
            sep = "| " + " | ".join(["---"] * num_cols) + " |"
            md_lines.append(f"\n{header}\n{sep}")
            for row in rows_data[1:]:
                row_str = "| " + " | ".join(row) + " |"
                md_lines.append(row_str)
            md_lines.append("\n")

full_md = "\n".join(md_lines)

out1 = Path(r"D:\PHP_NOSQL\BAO_CAO_DO_AN_MON_HOC_LARAVEL_5_CHUONG.md")
out1.write_text(full_md, encoding="utf-8")

out2 = Path(r"D:\PHP_NOSQL\SMART_CASUAL_LARAVEL\docs\BAO_CAO_DO_AN_MON_HOC_LARAVEL.md")
out2.write_text(full_md, encoding="utf-8")

artifact_out = Path(r"C:\Users\VuongNguyen\.gemini\antigravity\brain\6f1d1daf-5c59-4014-bff0-7e3389b6bf51\BAO_CAO_DO_AN_MON_HOC_LARAVEL.md")
artifact_out.write_text(full_md, encoding="utf-8")

print(f"Exported successfully to {out1}, {out2}, and {artifact_out}")
