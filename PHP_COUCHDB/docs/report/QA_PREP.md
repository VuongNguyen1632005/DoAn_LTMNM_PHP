# QA Prep – 28 câu

Câu trả lời dựa trên code/evidence; người trình bày cần nhóm xác nhận.

### 1. Vì sao nhúng delivery_tracking?
**Trả lời:** Theo cùng vòng đời order; cập nhật đồng thời có thể 409.

**Phần:** Nguyễn Việt Vương

### 2. _rev/409 là gì?
**Trả lời:** Revision token; ghi bằng rev cũ bị từ chối.

**Phần:** Nguyễn Việt Vương

### 3. Mango và View khác?
**Trả lời:** Mango selector/index; View map-reduce/group_level.

**Phần:** Nguyễn Ngọc Tường Vi

### 4. Vì sao guest_orders?
**Trả lời:** 15 guest; examined 65 xuống 15.

**Phần:** Nguyễn Huy Hoàng

### 5. Vì sao delivery_status?
**Trả lời:** Query 09–11 examined 5/2/3 sau index.

**Phần:** Nguyễn Huy Hoàng

### 6. Backup JSON khác replication?
**Trả lời:** Export logic so với đồng bộ documents.

**Phần:** Nguyễn Việt Vương

### 7. Document thay SQL?
**Trả lời:** Phù hợp aggregate demo; không thay SQL phổ quát.

**Phần:** Nguyễn Ngọc Tường Vi

### 8. Validator khác PHP?
**Trả lời:** Validator chặn write tại DB; PHP kiểm input/quyền/workflow.

**Phần:** Nguyễn Việt Vương

### 9. Bao nhiêu validator case?
**Trả lời:** Sáu: bốn 403, hai 201.

**Phần:** Nguyễn Việt Vương

### 10. Demo SSE?
**Trả lời:** Cập nhật order và quan sát timeline/reconnect.

**Phần:** Nguyễn Huy Hoàng

### 11. Dataset?
**Trả lời:** 150 docs, 147 business, 3 design, 65 order, 15 guest.

**Phần:** Nguyễn Việt Vương

### 12. Tác dụng snapshot?
**Trả lời:** Giữ thông tin lúc mua; chấp nhận lặp.

**Phần:** Nguyễn Việt Vương

### 13. Order và delivery status giống?
**Trả lời:** Không, hai tiến trình khác nhau.

**Phần:** Nguyễn Việt Vương

### 14. group_level?
**Trả lời:** Gộp theo độ sâu key năm/tháng.

**Phần:** Nguyễn Ngọc Tường Vi

### 15. Replication conflict?
**Trả lời:** Đọc conflicts, xác định winner/losing leaf.

**Phần:** Nguyễn Việt Vương

### 16. Attachment?
**Trả lời:** 68 byte; digest readback khớp; delete 200.

**Phần:** Nguyễn Việt Vương

### 17. Security?
**Trả lời:** Member 200, outsider/design edit 403.

**Phần:** Nguyễn Việt Vương

### 18. Compaction?
**Trả lời:** Scratch DB; không suy rộng production.

**Phần:** Nguyễn Việt Vương

### 19. Manager backup guard?
**Trả lời:** Role, CSRF, filename traversal; trong 25 checks.

**Phần:** Nguyễn Việt Vương

### 20. Staff/customer tạo backup?
**Trả lời:** Không; HTTP checks xác nhận guard.

**Phần:** Nguyễn Việt Vương

### 21. CSV import error?
**Trả lời:** 3 imported, 1 row error.

**Phần:** Nguyễn Việt Vương

### 22. Explain?
**Trả lời:** Cho biết index được chọn/ứng viên.

**Phần:** Nguyễn Huy Hoàng

### 23. Phân quyền app?
**Trả lời:** Route/service role guard; có HTTP evidence.

**Phần:** Nguyễn Huy Hoàng

### 24. Hạn chế delivery?
**Trả lời:** Không GPS/provider; tracking nội bộ.

**Phần:** Nguyễn Huy Hoàng

### 25. Nguồn số liệu?
**Trả lời:** Phase1/3/4 facts và 01_reverify.

**Phần:** Cả nhóm

### 26. Data thật?
**Trả lời:** Seeder dùng dữ liệu tổng hợp.

**Phần:** Cả nhóm

### 27. Concurrent order?
**Trả lời:** Stale revision trả 409, cần đọc bản mới.

**Phần:** Nguyễn Việt Vương

### 28. Vai trò Fauxton?
**Trả lời:** Quan sát và quản trị database.

**Phần:** Nguyễn Ngọc Tường Vi

### 29. View test?
**Trả lời:** Tám checks PASS.

**Phần:** Nguyễn Ngọc Tường Vi