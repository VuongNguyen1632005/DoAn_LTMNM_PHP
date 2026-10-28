# Hướng dẫn nhóm tự chụp minh chứng

Không có ảnh terminal trong gói bàn giao. Các tên PNG dưới đây đang để trống. Mở PowerShell tại thư mục dự án có `docker-compose.yml`; Docker Desktop và hai service phải đang chạy. Dùng cửa sổ đủ rộng, chụp dòng lệnh và toàn bộ kết quả, không cắt mất lỗi hay kết quả. Có thể dùng `function prompt { 'PS> ' }` để không hiển thị đường dẫn cá nhân. Không chạy `Get-Content .env`, không in biến mật khẩu/token; đăng nhập bằng tài khoản đọc từ cấu hình nhưng không đưa thông tin đó vào ảnh.

Các lệnh dưới đây đã được chạy thật, trừ T6 được ghi rõ chưa chạy. Kết quả tham chiếu nằm ở `logs/`, chỉ là văn bản/JSON để đối chiếu, không phải ảnh. Kích thước backup có thể thay đổi giữa lần chạy do thời điểm và revision.

Ứng dụng gốc chỉ cho backup/restore tên kết thúc `_test` hoặc `_demo`, trong khi prompt yêu cầu thao tác ghi trên `_scratch`. Vì vậy `tools/screenshots/composer.json` cung cấp các lệnh `capture:*`: tái sử dụng logic nguồn qua wrapper trong bộ công cụ, mở rộng bộ lọc tên để cho phép scratch, không sửa source ứng dụng. Không coi wrapper là bằng chứng lệnh restore nguyên bản chấp nhận `_scratch`.

## F4.5 – Sao lưu JSON, chương 4

Điều kiện: database chính `retail_order_delivery_demo` có 150 document; chỉ đọc database này để chuẩn bị bản sao scratch. Tên scratch và file riêng bên dưới phải chưa tồn tại. Lệnh cleanup chỉ nhắm tên dành riêng của bộ công cụ; không dùng nếu nhóm đang có một phiên chụp khác sử dụng chúng.

```powershell
docker compose exec -T app composer --working-dir=tools/screenshots capture:cleanup
docker compose exec -T app composer --working-dir=tools/screenshots capture:copy
docker compose exec -T app composer --working-dir=tools/screenshots capture:backup
```

Phải thấy: `Scratch documents: 150`, `Documents exported: 150`, `JSON validation: PASS`, `Backup: PASS`, dòng kích thước file. Lần thử: 155368 byte. Đối chiếu `logs/F4.5_backup.txt`. Chụp lệnh backup và toàn bộ kết quả; tên đề xuất `F4.5_backup_json.png`.

Nếu tiếp tục F4.6, giữ database/file này. Nếu chỉ chụp F4.5, dọn:

```powershell
docker compose exec -T app composer --working-dir=tools/screenshots capture:cleanup
```

## F4.6 – Restore, restore-verify và Fauxton, chương 4

Điều kiện: đã chạy chuỗi F4.5; `retail_manual_restore_scratch` chưa tồn tại. Nếu chưa chuẩn bị, chạy trọn chuỗi F4.5 trước.

```powershell
docker compose exec -T app composer --working-dir=tools/screenshots capture:restore
docker compose exec -T app composer --working-dir=tools/screenshots capture:restore-verify
docker compose exec -T app composer --working-dir=tools/screenshots capture:parity
```

Phải thấy: `Imported: 150`, `Failed: 0`, `Restored documents: 150`, ba design document, `Mango indexes: 16`, `Validator rejection: HTTP 403 PASS`, kiểm tra `_security`, SHA-256 và source parity PASS, `Missing/extra/different: 0/0/0`, `Restore verification: PASS`. Xem `logs/F4.6_restore.txt`, `logs/F4.6_restore_verify.txt`, `logs/F4.6_parity.txt`. Có thể chia hai ảnh terminal nếu kết quả dài, nhưng phải giữ toàn bộ kết quả. Tên đề xuất `F4.6_restore_terminal.png` và `F4.6_restore_verify.png`.

**Trước khi xóa:** mở Fauxton ở `http://127.0.0.1:5984/_utils/`, đăng nhập ngoài lúc chụp, chọn Databases → `retail_manual_restore_scratch`; xác nhận 150 document rồi mở All Documents. Ảnh GUI tham chiếu đã có: `F4.6_restore_gui.png`. Sau khi nhóm hoàn tất ảnh terminal và GUI mới chạy:

```powershell
docker compose exec -T app composer --working-dir=tools/screenshots capture:cleanup
```

## F5.12 – Revision cũ bị từ chối, chương 5

Điều kiện: `retail_manual_capture_scratch` chưa tồn tại. Lệnh tự tạo document, cập nhật một lần, gửi lại revision cũ và tự xóa database.

```powershell
docker compose exec -T app composer --working-dir=tools/screenshots capture:cleanup
docker compose exec -T app composer --working-dir=tools/screenshots capture:revision
```

Phải thấy `Stale revision: HTTP 409`, `error: conflict`, `Document update conflict.`. Xem `logs/F5.12_stale_rev.txt`. Chụp cả lệnh và JSON lỗi; lưu `F5.12_http409.png`. Dọn kiểm chứng:

```powershell
docker compose exec -T app composer --working-dir=tools/screenshots capture:cleanup
```

## T1 – Sao lưu bằng replication

Điều kiện: nguồn chính tồn tại, đích `retail_manual_replica_scratch` chưa tồn tại. Chỉ đọc nguồn. Validator được cài vào đích sau business document để không từ chối dữ liệu lịch sử.

```powershell
docker compose exec -T app composer --working-dir=tools/screenshots capture:cleanup
docker compose exec -T app composer --working-dir=tools/screenshots capture:replication
```

Phải thấy `Replicated documents: 150`, `Missing/extra/different: 0/0/0`, `Normalized content parity: PASS`. Xem `logs/T1_replication.txt`. Chụp cả lệnh và kết quả, lưu `T1_replication_backup.png`. Lệnh tự xóa đích; dọn kiểm chứng bằng `capture:cleanup` như F5.12.

## T2 – Nhập CSV, ba dòng nhận và một dòng bị từ chối

Điều kiện: file mẫu `data/samples/products_import.csv` có sẵn; database `retail_csv_capture_scratch` chưa tồn tại. Lệnh chuẩn bị cài validator đầy đủ.

```powershell
docker compose exec -T app composer --working-dir=tools/screenshots capture:cleanup
docker compose exec -T app composer --working-dir=tools/screenshots capture:csv-prepare
docker compose exec -T -e COUCHDB_DATABASE=retail_csv_capture_scratch app composer import:products-csv -- data/samples/products_import.csv
```

Phải thấy `rows_submitted_to_bulk_docs: 4`, `imported_count: 3`, một lỗi `forbidden` cho dòng 5, `PASS_WITH_ROW_ERRORS`. Đây là kết quả hợp lệ của thử nghiệm, không xóa dòng lỗi khỏi ảnh. Xem `logs/T2_import.txt`. Chụp toàn bộ JSON, lưu `T2_csv_import.png`.

```powershell
docker compose exec -T app composer --working-dir=tools/screenshots capture:cleanup
```

## T3 – Xuất 65 dòng đơn hàng

Điều kiện: database demo chính tồn tại; file `docs/phase3/exports/orders_capture.csv` chưa tồn tại. Lệnh chỉ đọc database chính và ghi file riêng.

```powershell
docker compose exec -T -e DEMO_DATABASE=retail_order_delivery_demo app composer export:orders-csv -- orders_capture.csv
```

Phải thấy `query_count: 65`, `rows_exported: 65`, `docs_examined: 65`. Xem `logs/T3_export.txt`. Chụp lệnh và kết quả, lưu `T3_csv_export.png`. Dọn file đúng tên sau khi chụp:

```powershell
Remove-Item -LiteralPath .\docs\phase3\exports\orders_capture.csv
```

## T4 – Sáu ca validator đầy đủ

Điều kiện: Docker đang chạy. Wrapper chỉ đổi tên database kiểm thử sang `_scratch` và chuyển evidence sang `logs/`; không thay đổi validator hoặc kỳ vọng ca kiểm thử.

```powershell
docker compose exec -T app composer --working-dir=tools/screenshots capture:validator
(Get-Content -LiteralPath .\docs\figures\logs\T4_validator_cases.json -Raw | ConvertFrom-Json).cases | Select-Object case,actual_http,result | Format-Table -AutoSize
```

Phải thấy `PASS: 6 full-validator cases`, bốn ca `403` và hai ca `201`, cả sáu `PASS`. Xem `logs/T4_validator.txt` và `logs/T4_validator_cases.json`. Chụp lệnh cùng bảng sáu dòng và tổng PASS, lưu `T4_validator.png`. Database ngẫu nhiên `_scratch` được tự xóa trong `finally`; không cần xóa database demo hay gọi cập nhật validator trên nguồn chính.

## T5 – 25 ca HTTP quản lý user và backup

Điều kiện: không có phiên chụp UI khác, `retail_ui_capture_demo` chưa tồn tại. Bộ kiểm thử tạo fixture độc lập trên đúng database này, mở PHP server riêng bên trong container và dọn database/file backup sau khi hoàn tất. Không đổi database của ứng dụng gốc. Wrapper chỉ điều chỉnh tên database và nơi lưu evidence; nội dung 25 ca được giữ nguyên.

```powershell
docker compose exec -T app composer --working-dir=tools/screenshots capture:http
```

Phải thấy `PASS: 25 HTTP integration checks. Database: retail_ui_capture_demo`. Xem `logs/T5_http.txt` và `logs/admin_users_http_evidence.json`. Chụp dòng lệnh và tổng kết PASS, lưu `T5_admin_http.png`. Nếu cần bảng chi tiết:

```powershell
(Get-Content -LiteralPath .\docs\figures\logs\admin_users_http_evidence.json -Raw | ConvertFrom-Json).checks | Format-Table name,result -AutoSize
```

Database và các file backup do bộ kiểm thử tạo được tự dọn. Nếu lần chạy bị ngắt trước cleanup, chỉ xóa database riêng sau khi chắc chắn không còn phiên UI sử dụng:

```powershell
docker compose exec -T app composer --working-dir=tools/screenshots capture:ui-cleanup
```

## T6 – `_security`: CHƯA CHẠY

Kỳ vọng yêu cầu: member GET 200, outsider GET 403, member sửa design doc 403. Để kiểm chứng đúng cần hai user thường có thông tin xác thực trong database hệ thống `_users`. Tạo hai user này là thao tác ghi ngoài `_scratch`, trái quy tắc bất biến của prompt. Vì vậy không chạy `scripts/demo_security.php`, không có log PASS hoặc lệnh đã kiểm chứng cho T6; ảnh `T6_security.png` vẫn để trống.

Nhóm cần quyết định cho phép ngoại lệ tạo rồi xóa hai user tạm trong `_users`, hoặc dùng tài khoản thử nghiệm thường đã được chuẩn bị từ trước. Sau khi có điều kiện đó mới kiểm chứng trọn chuỗi, ghi kết quả thực và bổ sung các lệnh dán cụ thể. Không dùng admin thay member vì admin bỏ qua `_security`; không lấy ảnh Permissions làm bằng chứng HTTP 403.

## T7 và F5.14 – Cặp GUI và terminal từ chối validator

Ảnh GUI F5.14 đã có, dùng validator đầy đủ từ `database/couchdb/design-docs/domain_validation.json`, nhận HTTP 403 được lưu trong `logs/F5.14_rejection.json`. Thứ tự: xem/chụp GUI trước, sau đó chụp terminal. Nếu muốn chạy lại GUI:

```powershell
node tools/screenshots/validator-gui.cjs
```

Script tạo scratch riêng, cài validator đầy đủ, sửa `status` thành `invalid_status` trong editor Fauxton, chụp lỗi, rồi xóa scratch. Không chụp màn hình nhập mật khẩu. Tiếp theo terminal dùng fixture tương đương được tạo mới trên scratch riêng:

```powershell
docker compose exec -T app composer --working-dir=tools/screenshots capture:cleanup
docker compose exec -T app composer --working-dir=tools/screenshots capture:invalid
```

Phải thấy `Invalid order status: HTTP 403`, `error: forbidden`, lý do `order requires a valid status, receiver, and non-empty items`. Xem `logs/T7_invalid.txt`. GUI và terminal là hai lần chạy độc lập cùng validator/trạng thái lỗi, không tuyên bố cùng một HTTP request. Chụp toàn bộ lệnh và JSON lỗi, lưu `T7_http403.png`. Lệnh tự xóa scratch; có thể gọi `capture:cleanup` kiểm chứng.

## Bốn ảnh cài CouchDB trên Windows – nhóm tự chụp

| Mã | Phần báo cáo | Phần cần có trong ảnh | Tên file |
|---|---|---|---|
| W1 | Tải phần mềm | Trang tải chính thức Apache CouchDB, tên gói Windows và phiên bản được chọn | W1_windows.png |
| W2 | Cài đặt | Trình cài đặt, bước chọn thành phần/đường dẫn không lộ tên cá nhân | W2_windows.png |
| W3 | Single Node Setup | Fauxton Setup hoàn tất Single Node, không hiện mật khẩu | W3_windows.png |
| W4 | Kiểm chứng cài đặt | Verify Installation trên bản Windows thực tế, đủ kết quả từng mục | W4_windows.png |

Không dùng ảnh Docker để thay cho ảnh cài Windows. Nếu Docker đang chiếm cổng 5984, dùng một cổng khác cho bản Windows hoặc tạm dừng service CouchDB Docker theo kế hoạch của nhóm; ghi rõ URL/cổng thật. Đừng chạy hai tiến trình cùng cổng 5984. Che mật khẩu và đường dẫn cá nhân bằng ô đặc nếu lọt vào ảnh; ghi chú thao tác che khi cập nhật manifest. Sau khi chụp, khôi phục service/cổng theo cấu hình ban đầu; không gỡ cài đặt hay xóa dữ liệu chỉ để dọn ảnh.

## Kiểm tra sau khi chụp

```powershell
docker compose exec -T app composer --working-dir=tools/screenshots capture:cleanup
node tools/screenshots/audit.cjs
```

Fingerprint nguồn chính phải khớp. Nếu khác, dừng và báo ngay, không sửa dữ liệu để làm khớp. `audit.cjs` chỉ đọc database chính. Khi nhóm thêm ảnh terminal/Windows, cập nhật SHA-256, kích thước và trạng thái trong manifest; không ghi đè ảnh GUI bằng ảnh dựng từ văn bản.
