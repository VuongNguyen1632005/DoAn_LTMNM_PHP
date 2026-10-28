# Source package
Đóng gói riêng repo PHP + CouchDB: mã PHP, Compose/Dockerfile, `.env.example`, Composer, seeder, query/index/design docs, scripts, tests, facts và README. Gói có 443 file dự án cùng `MANIFEST.sha256` (444 entries ZIP). Loại `.env`, vendor, node_modules, `.git`, dữ liệu runtime trong `var/`, tài liệu báo cáo/draft và ZIP lồng nhau. ZIP: `dist/source_code.zip`; SHA-256: `CC559BDF19B2FB4F74E9E54CE021C9BE2908DBB518481DD860034EBA48E54892`.

Kiểm tra secret: PASS. Clean-room runtime smoke: PASS — giải nén độc lập, build Compose, khởi động CouchDB/PHP, seed thành công 147 tài liệu nghiệp vụ + 3 design documents, verifier xác nhận đủ 150 tài liệu; container, volume và thư mục tạm đã được dọn. Gói chỉ gồm repo này; các thay đổi Laravel ở workspace cha không nằm trong ZIP.
