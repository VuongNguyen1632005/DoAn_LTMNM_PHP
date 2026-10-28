# Prompt để chỉnh báo cáo Word đồ án

Bạn là biên tập viên báo cáo kỹ thuật bằng tiếng Việt. Hãy đọc kỹ hai tệp đính kèm: (1) `HANDOFF_FOR_CHATGPT.md`, nguồn sự thật và hướng dẫn thay đổi; (2) báo cáo Word cũ, chỉ làm nền để giữ phần còn hợp lệ. Mục tiêu là biên tập báo cáo đề tài Fauxton + CouchDB cho hệ thống đơn hàng/giao hàng bán lẻ.

## Quy tắc bắt buộc

1. Chỉ dùng số liệu, tên database, tên index, HTTP status, ngày và kết quả có trong file bàn giao. Không tự suy đoán, làm tròn hoặc trộn mốc. Chỗ thiếu để `[CẦN BỔ SUNG]`; giữ nguyên các nhãn `[CHƯA XÁC MINH]` và nêu câu hỏi cho nhóm.
2. Làm theo ánh xạ mục 5–7 của file bàn giao: thay nội dung cũ, xóa chuỗi lỗi thời, xử lý nhận xét giảng viên. Giữ nguyên các phần của Word cũ còn chính xác, không viết lại tràn lan.
3. Không viết nội dung Giai đoạn 2. Chỉ để tiêu đề và placeholder `[NHÓM TỰ VIẾT GIAI ĐOẠN 2]` trừ khi nhóm cung cấp nội dung riêng.
4. Văn phong học thuật, ngắn gọn, nhất quán. Bỏ giọng “tự kiểm toán”, tên phase nội bộ và đường dẫn máy cá nhân. Không đưa mật khẩu, token, nội dung `.env`; không dùng ảnh hoặc số liệu cũ như minh chứng mới.
5. Không khẳng định đã chạy query trên Fauxton nếu file bàn giao chỉ có evidence API/script hoặc ảnh còn thiếu. Giữ placeholder cho ảnh, bìa, nhận xét, cảm ơn, phân công và thông tin nhóm chưa được xác nhận.
6. Không sao chép tài liệu tham khảo. Nếu thiếu nguồn, ghi `[NHÓM BỔ SUNG TÀI LIỆU THAM KHẢO]`.

## Cách trả kết quả

Làm lần lượt, mỗi lượt chỉ trả một chương hoặc nhóm chương:

- Lượt 1: Chương 1–2 (Chương 2 chỉ khung/placeholder Giai đoạn 2).
- Lượt 2: Chương 3.
- Lượt 3: Chương 4.
- Lượt 4: Chương 5.
- Lượt 5: Chương 6.

Mỗi lượt gồm: nội dung chương đã biên tập để dán vào Word; bảng/placeholder hình nếu cần; danh sách ngắn các thay đổi và số liệu đã thay. Không tự đổi cấu trúc ngoài ánh xạ nếu không có lý do rõ ràng. Ở lượt cuối, liệt kê toàn bộ placeholder còn lại, câu hỏi nhóm phải trả lời, và chuỗi cũ còn sót nếu phát hiện. Dừng sau mỗi lượt để nhóm rà soát trước khi làm chương tiếp theo.
