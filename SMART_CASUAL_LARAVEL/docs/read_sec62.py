import docx
import sys

sys.stdout.reconfigure(encoding='utf-8')

p = r"C:\Users\VuongNguyen\.gemini\antigravity\brain\6f1d1daf-5c59-4014-bff0-7e3389b6bf51\.user_uploaded\media_1793017301535_b532377e.docx"
doc = docx.Document(p)

capture = False
for para in doc.paragraphs:
    if "6.2" in para.text:
        capture = True
    if "7." in para.text:
        capture = False
    if capture:
        print(para.text)
