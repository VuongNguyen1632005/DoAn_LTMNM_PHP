import docx
import glob
import sys

sys.stdout.reconfigure(encoding='utf-8')

for p in glob.glob(r"C:\Users\VuongNguyen\.gemini\antigravity\brain\6f1d1daf-5c59-4014-bff0-7e3389b6bf51\.user_uploaded\*.docx"):
    doc = docx.Document(p)
    text = " ".join([para.text for para in doc.paragraphs[:10] if para.text.strip()])
    print("FILE:", p)
    print("TEXT:", text[:300])
    print("-" * 50)
