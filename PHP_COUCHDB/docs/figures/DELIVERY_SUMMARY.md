# Biên bản bàn giao

92 ảnh GUI thật, rộng 2880 px. Trạng thái: {"OK":87,"MISMATCH":3,"FAILED":2,"MANUAL":14}. Không có ảnh terminal được tạo.

Fingerprint trước/sau: **KHỚP**. doc_count=150; update_seq giữ nguyên.

SHA-256 chuẩn hóa: `0de42e51319fee5be8ae190bb1f44b7bf2bf964c8457f127f6ec46f800583af5`. Thuật toán và update_seq đầy đủ trong logs/fingerprint_before.json và logs/fingerprint_after.json.

Mọi database do đợt chụp tạo (`*_scratch`, `retail_ui_capture_demo`) đã xóa. File backup/CSV tạm đã dọn; xác minh ở logs/final_audit.json. Container PHP đã trả về database ban đầu `shopquan_ao_sql_migration_test`; không commit override, storageState hay thông tin xác thực.

Cần nhóm quyết định: cho phép tạo rồi xóa hai user tạm trong `_users` để chạy T6 hay giữ mục này chưa kiểm chứng; chọn cách trình bày Query 01/06 theo nhiều ảnh trang; chấp nhận minh chứng compaction qua API hoặc bổ sung môi trường có menu GUI tương ứng. C2 orders_by_customer có 50 kết quả API nhưng ảnh GUI FAILED.
