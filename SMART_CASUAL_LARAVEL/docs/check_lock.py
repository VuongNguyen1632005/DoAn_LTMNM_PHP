import os

files = [
    r"D:\PHP_NOSQL\BAO_CAO_DO_AN_MON_HOC_LARAVEL_5_CHUONG.docx",
    r"D:\PHP_NOSQL\SMART_CASUAL_LARAVEL\docs\BAO_CAO_DO_AN_MON_HOC_LARAVEL_5_CHUONG.docx",
]

for f in files:
    try:
        with open(f, "a"):
            print("NOT LOCKED:", f)
    except Exception as e:
        print("LOCKED:", f, e)
