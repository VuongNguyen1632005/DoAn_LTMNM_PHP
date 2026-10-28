import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn
from pathlib import Path

def test_formatter():
    doc = docx.Document()
    sec = doc.sections[0]
    # Lề chuẩn: Trái 3.0cm, Phải 2.0cm, Trên 2.0cm, Dưới 2.0cm
    sec.left_margin = Inches(3.0 / 2.54)
    sec.right_margin = Inches(2.0 / 2.54)
    sec.top_margin = Inches(2.0 / 2.54)
    sec.bottom_margin = Inches(2.0 / 2.54)
    
    # Add paragraph
    p = doc.add_paragraph("Kiểm thử định dạng đồ án HUIT.")
    p.style.font.name = "Times New Roman"
    p.style.font.size = Pt(13)
    p.paragraph_format.line_spacing = 1.5
    p.paragraph_format.space_after = Pt(6)
    p.paragraph_format.first_line_indent = Inches(1.27 / 2.54)
    
    out_test = Path("D:/PHP_NOSQL/SMART_CASUAL_LARAVEL/docs/test_format.docx")
    doc.save(str(out_test))
    print("Test passed! Saved to:", out_test)

if __name__ == "__main__":
    test_formatter()
