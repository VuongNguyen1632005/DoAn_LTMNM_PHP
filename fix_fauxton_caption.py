from docx import Document

path = r"D:\PHP_NOSQL\BAOCAO_NOSQL_HOANCHINH.docx"
old = "Hình 3.14 – Kết quả chạy View order_count_by_status trong Fauxton"
new = "Ảnh chụp Fauxton – Kết quả View order_count_by_status với reduce=true và group=true"

doc = Document(path)
for paragraph in doc.paragraphs:
    if paragraph.text.strip() == old:
        paragraph.text = new
        paragraph.alignment = 1
        break
else:
    raise RuntimeError("Caption not found")
doc.save(path)
print(new)
