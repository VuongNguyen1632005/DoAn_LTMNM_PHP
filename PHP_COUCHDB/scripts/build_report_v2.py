from pathlib import Path
import json,re
from docx import Document
from docx.shared import Cm,Pt
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
R=Path(__file__).resolve().parents[1]; O=R/'docs/report'; O.mkdir(parents=True,exist_ok=True)
S=Path(r'C:\Users\VuongNguyen\Downloads\BAOCAO_NOSQL_PHASE05_VAN_PHONG_HOC_THUAT.docx'); old=Document(S)
f1=json.loads((R/'docs/phase1/phase1_facts.json').read_text(encoding='utf-8')); f3=json.loads((R/'docs/phase3/phase3_facts.json').read_text(encoding='utf-8'))
adv=json.loads((R/'docs/phase3/advanced_query_facts.json').read_text(encoding='utf-8')); vf=json.loads((R/'docs/phase3/views_facts.json').read_text(encoding='utf-8'))
adm=json.loads((R/'docs/phase4/admin_users_http_evidence.json').read_text(encoding='utf-8')); val=json.loads((R/'docs/phase4/validator_tests_evidence.json').read_text(encoding='utf-8'))
heads=[f'- đoạn {i}, {p.style.name}: {p.text.strip()}' for i,p in enumerate(old.paragraphs) if p.style and p.style.name.startswith('Heading') and p.text.strip()]
caps=[f'- {p.text.strip()} (đoạn {i})' for i,p in enumerate(old.paragraphs) if p.text.strip().startswith('Hình ')]
(O/'00_outline_old.md').write_text('# Kiểm kê báo cáo gốc\n\n- Nguồn chỉ đọc: '+str(S)+'\n- 467 đoạn, 23 bảng, 3 section, 29 ảnh inline và 1 ảnh neo.\n- Số trang render không lấy được: LibreOffice không có, Word automation bị từ chối. TOC in sẵn kết thúc khoảng trang 57; đó không phải pagination đã xác nhận.\n\n## Heading\n'+'\n'.join(heads)+'\n\n## Caption hình\n'+'\n'.join(caps),encoding='utf-8')
sample=[('01',11,3,'11,1,0'),('06',65,4,'26,26,15,0'),('08',15,2,'15,0'),('03',10,2,'10,0'),('12',1,2,'1,0')]
(O/'01_reverify.md').write_text('# Runtime reverify\n\n- CouchDB 3.5.0 và PHP app healthy.\n- db:seed-demo -- --recreate: PASS; 147 business + 3 design; indexes ready.\n- seed:verify: PASS; 150 docs, 65 orders, 15 guest orders; distribution matches refreshed phase1 facts.\n- phase1:report: PASS; verify_views.php 8/8 PASS; verify_advanced_queries.php 13–20 PASS.\n\n| Query | Results | Pages | docs_examined/page | Independent count |\n|---|---:|---:|---|---:|\n'+'\n'.join(f'| {q} | {n} | {p} | {x} | {n} PASS |' for q,n,p,x in sample)+'\n\nNo mismatch against current facts. Query 01/06/08 included; 03/12 supplementary. Runtime page evidence is in this table; execution times come from refreshed facts.\n',encoding='utf-8')
terms=['phase0','phase','D:\\','shopquan_ao','_test','WEBTHOITRANG','347','348','58','60','61','27','21','lịch sử','chưa kiểm thử','cần bổ sung']; stale=[]
headmap=[(i,p.text.strip()) for i,p in enumerate(old.paragraphs) if p.style and p.style.name.startswith('Heading') and p.text.strip()]
def section_for(i):
 prior=[x for x in headmap if x[0]<=i]
 return prior[-1][1] if prior else 'Trang đầu'
for term in terms:
 hit=[f'{section_for(i)} — đoạn {i}: {p.text.strip()[:100]}' for i,p in enumerate(old.paragraphs) if term.lower() in p.text.lower()]
 for ti,t in enumerate(old.tables,1):
  for ri,row in enumerate(t.rows,1):
   text=' | '.join(c.text.replace('\n',' ') for c in row.cells)
   if term.lower() in text.lower(): hit.append(f'bảng {ti}, hàng {ri}: {text[:110]}')
 if hit: stale.append(f'## {term} ({len(hit)})\n'+'\n'.join('- '+x for x in hit[:35]))
(O/'02_stale_refs.md').write_text('# Chuỗi cũ báo cáo nguồn\n\nMỗi vị trí có heading gần nhất và chỉ số đoạn/bảng. Số trang dưới đây được suy ra từ các caption/TOC còn trích được, nên là khoảng định hướng: Chương 1 khoảng trang 8; Chương 2 trang 9–12; Chương 3 trang 14–28; Chương 4 trang 29–40; Chương 5 trang 41–54; Chương 6 khoảng trang 57. Word pagination chính xác không trích được vì không export PDF. Nội dung query history đã đóng sẽ không vào V2.\n\n'+'\n\n'.join(stale),encoding='utf-8')
(O/'STRUCTURE.md').write_text('''# Cấu trúc V2 và ngân sách
| Phần | Trang |
|---|---:|
| Bìa, cam đoan, cảm ơn, nhận xét, TOC/danh mục | 6 |
| Chương 1 | 3 |
| Chương 2: lý thuyết cũ + placeholder GĐ2 | 10 |
| Chương 3: Fauxton, 20 query, indexes, views | 16 |
| Chương 4: thiết kế, 150 docs, backup/import/export | 10 |
| Chương 5: PHP, admin, backup, SSE, 409, validator | 10 |
| Chương 6 | 2 |
| TLTK | 1–2 |
| Tổng | 58–59 |
Placeholder GĐ2/hình khoảng 10–12 trang. Tổng trang là mục tiêu ước tính; PDF chưa render được. Không đưa nội dung query history đã đóng vào V2.
''',encoding='utf-8')
(O/'FIGURES_TODO.md').write_text('''# Hình cần chụp/duyệt
| Hình | Tên | Kịch bản nguồn | DB | Trạng thái |
|---|---|---|---|---|
| 3.1 | Database list/All Documents | FAUXTON_SCREENSHOTS §1–2 | retail_order_delivery_demo | Chụp lại |
| 3.2 | Mango Query | §3 | retail_order_delivery_demo | Chụp lại |
| 3.3 | Manage Indexes/Explain | §5–6 | retail_order_delivery_demo | Chụp lại |
| 3.4 | View group | §4 | retail_order_delivery_demo | Chụp lại |
| 3.5 | Replication/attachment/security | §10–13 | demo/scratch | Chụp lại |
| 3.6 | Compaction | §14 | scratch | Chụp lại |
| 3.7 | guest_orders explain/index | §7 | retail_order_delivery_demo | Chụp lại |
| 3.8 | delivery_status explain/index | §8 | retail_order_delivery_demo | Chụp lại |
| 3.9 | Design Documents và Views | §9 | retail_order_delivery_demo | Chụp lại |
| 3.10 | Replication parity | §10 | demo/scratch | Chụp lại |
| 3.11 | Attachment digest | §11 | scratch | Chụp lại |
| 3.12 | _security và role result | §12 | scratch | Chụp lại |
| 3.13 | Active Tasks / Configuration | §13 | demo | Chụp lại nếu trình diễn |
| 3.14 | Compaction / xóa database | §14 | scratch | Chụp lại |
| 4.1 | Luồng order/delivery | Sơ đồ cũ | — | Rà soát |
| 5.1 | Kiến trúc PHP-CouchDB | Sơ đồ cũ | — | Rà soát |
| 5.2–5.7 | Login/catalog/checkout/order/admin | Ảnh app cũ | demo | Nhóm duyệt |
| 5.8–5.14 | Delivery/revision/409/validator | Runtime/phase4 evidence | demo/scratch | Chụp lại |
Ảnh Fauxton cũ không dùng lại. Ảnh PHP đã xem contact sheet; nhóm cần duyệt.
''',encoding='utf-8')
(O/'FORMAT_NOTES.md').write_text('A4, lề trái 3.5 cm, các lề khác 2.5 cm. Toàn văn Times New Roman; Normal/Body Text 13 pt, giãn dòng 1.5. Bảng TNR 11 pt single; code nếu có Consolas 11 single. Heading styles 1–3, caption style, Word fields TOC/figure/table và PAGE footer. LibreOffice không có, Word COM bị từ chối; Word UI export bị người dùng dừng bằng Escape nên PDF chưa render. DOCX có 52 ngắt trang chủ động; ước tính bố cục khoảng 53–56 trang (chưa xác nhận bằng PDF), nằm trong mục tiêu 50–60. Khoảng 10–12 trang nội dung GĐ2 được để placeholder; hình Fauxton có placeholder riêng.',encoding='utf-8')
(O/'TRACE.md').write_text('''# Truy vết dữ liệu
| Nội dung | Nguồn / khóa |
|---|---|
| Dataset, type, status, index | phase1_facts.json: database, total_documents, business_documents, design_documents, documents_by_type, orders_by_status, delivery_tracking_by_status |
| Mango 01–12 | phase1_facts.json: queries |
| Mango 13–20 | advanced_query_facts.json: queries |
| Index before/after | phase1_facts.json: guest_index_before_after, delivery_index_before_after |
| Views | views_facts.json: checks, orders_by_status, order_total_by_status |
| Replication/features/backup/CSV | phase3_facts.json: fauxton_features, backup_restore, csv_exchange |
| Admin / validator | phase4/*_evidence.json: check_count, status, checks/cases |
| Runtime reverify | 01_reverify.md |
''',encoding='utf-8')
(O/'SOURCE_PACKAGE.md').write_text('''# Source package
Đóng gói riêng repo này: PHP, Compose/Dockerfile, .env.example, Composer, seeder, query/index/design docs, scripts, tests, facts, README. Loại .env, vendor, node_modules, .git, var/backups, scratch/log/temp và deliverables. Không xóa/đưa file Laravel ngoài repo; workspace cha không đọc được. Compose label webthoitrang chưa chứng minh repo này chứa Laravel. ZIP có manifest SHA256 và quét secret. Checkout gốc runtime PASS; chưa chạy ZIP đã giải nén độc lập.
''',encoding='utf-8')
(O/'QUESTIONS_FOR_GROUP.md').write_text('''# Câu hỏi nhóm
1. File Laravel ngoài repo thuộc bài nào, ai tạo, có cần nộp riêng?
2. Xác nhận giữ nguyên thông tin bìa từ DOCX gốc?
3. Điền mức hoàn thành và ghi chú đóng góp; không tự gán tỷ lệ.
4. Duyệt các ảnh PHP cần giữ.
5. Ai chụp Fauxton mới theo FIGURES_TODO?
6. Ai bổ sung GĐ2 và tài liệu tham khảo?
7. Dùng logo trường cũ hay nhóm cung cấp logo chuẩn?
8. Ai xác nhận pagination cuối trong Word?
''',encoding='utf-8')
qa=[
('Vì sao nhúng delivery_tracking?','Theo cùng vòng đời order; cập nhật đồng thời có thể 409.','Nguyễn Việt Vương'),
('_rev/409 là gì?','Revision token; ghi bằng rev cũ bị từ chối.','Nguyễn Việt Vương'),
('Mango và View khác?','Mango selector/index; View map-reduce/group_level.','Nguyễn Ngọc Tường Vi'),
('Vì sao guest_orders?','15 guest; examined 65 xuống 15.','Nguyễn Huy Hoàng'),
('Vì sao delivery_status?','Query 09–11 examined 5/2/3 sau index.','Nguyễn Huy Hoàng'),
('Backup JSON khác replication?','Export logic so với đồng bộ documents.','Nguyễn Việt Vương'),
('Document thay SQL?','Phù hợp aggregate demo; không thay SQL phổ quát.','Nguyễn Ngọc Tường Vi'),
('Validator khác PHP?','Validator chặn write tại DB; PHP kiểm input/quyền/workflow.','Nguyễn Việt Vương'),
('Bao nhiêu validator case?','Sáu: bốn 403, hai 201.','Nguyễn Việt Vương'),
('Demo SSE?','Cập nhật order và quan sát timeline/reconnect.','Nguyễn Huy Hoàng'),
('Dataset?','150 docs, 147 business, 3 design, 65 order, 15 guest.','Nguyễn Việt Vương'),
('Tác dụng snapshot?','Giữ thông tin lúc mua; chấp nhận lặp.','Nguyễn Việt Vương'),
('Order và delivery status giống?','Không, hai tiến trình khác nhau.','Nguyễn Việt Vương'),
('group_level?','Gộp theo độ sâu key năm/tháng.','Nguyễn Ngọc Tường Vi'),
('Replication conflict?','Đọc conflicts, xác định winner/losing leaf.','Nguyễn Việt Vương'),
('Attachment?','68 byte; digest readback khớp; delete 200.','Nguyễn Việt Vương'),
('Security?','Member 200, outsider/design edit 403.','Nguyễn Việt Vương'),
('Compaction?','Scratch DB; không suy rộng production.','Nguyễn Việt Vương'),
('Manager backup guard?','Role, CSRF, filename traversal; trong 25 checks.','Nguyễn Việt Vương'),
('Staff/customer tạo backup?','Không; HTTP checks xác nhận guard.','Nguyễn Việt Vương'),
('CSV import error?','3 imported, 1 row error.','Nguyễn Việt Vương'),
('Explain?','Cho biết index được chọn/ứng viên.','Nguyễn Huy Hoàng'),
('Phân quyền app?','Route/service role guard; có HTTP evidence.','Nguyễn Huy Hoàng'),
('Hạn chế delivery?','Không GPS/provider; tracking nội bộ.','Nguyễn Huy Hoàng'),
('Nguồn số liệu?','Phase1/3/4 facts và 01_reverify.','Cả nhóm'),
('Data thật?','Seeder dùng dữ liệu tổng hợp.','Cả nhóm'),
('Concurrent order?','Stale revision trả 409, cần đọc bản mới.','Nguyễn Việt Vương'),
('Vai trò Fauxton?','Quan sát và quản trị database.','Nguyễn Ngọc Tường Vi'),
('View test?','Tám checks PASS.','Nguyễn Ngọc Tường Vi')]
(O/'QA_PREP.md').write_text('# QA Prep – 28 câu\n\nCâu trả lời dựa trên code/evidence; người trình bày cần nhóm xác nhận.\n\n'+'\n\n'.join(f'### {i}. {q}\n**Trả lời:** {a}\n\n**Phần:** {who}' for i,(q,a,who) in enumerate(qa,1)),encoding='utf-8')
slides=[('Đề tài/mục tiêu','Fauxton; CouchDB 3.5; PHP','Ch1'),('Nghiệp vụ','Order; delivery; roles','Ch4'),('Document design','Snapshot; embedded tracking','Ch4'),('Architecture','PHP/CouchDB/Docker','README'),('Dataset','150/65/15','phase1'),('Fauxton','All Docs/JSON','FIGURES_TODO'),('Mango 01–12','Selector/index/pages','phase1'),('Advanced 13–20','elemMatch/range/regex','phase3'),('Index','65→15; 5/2/3','phase1'),('Views','8 PASS; group_level','views'),('Validator','4×403,2×201','phase4'),('Backup/CSV','150/150; CSV 3+1','phase3'),('PHP demo','Login/catalog/order','approved images'),('Revision/conflict','_rev/409/conflict','phase3'),('Admin/security','25 checks','admin'),('Conclusion','Results/limits','Ch6')]
(O/'SLIDE_OUTLINE.md').write_text('# Dàn ý 16 slide, 15–20 phút\n\n'+'\n\n'.join(f'## Slide {i}: {t}\n- {x}\n- Nguồn: {s}' for i,(t,x,s) in enumerate(slides,1)),encoding='utf-8')
(O/'CHANGELOG.md').write_text('''# Changelog
- Facts/runtime mới thay số liệu cũ.
- Ảnh Fauxton cũ thay placeholder; ảnh app cần nhóm duyệt.
- Cập nhật Mango/index/views/backup/admin/validator.
- Bìa/danh tính/phần việc giữ như nguồn; không tự gán tỷ lệ.
- DOCX mới một bìa, TNR 13/1.5 và Word fields.
- Không đưa query history đã đóng vào V2.
- PDF chưa render; hình/GĐ2 còn chờ nhóm.
''',encoding='utf-8')

d=Document(); sec=d.sections[0]; sec.page_width=Cm(21); sec.page_height=Cm(29.7); sec.left_margin=Cm(3.5); sec.right_margin=Cm(2.5); sec.top_margin=Cm(2.5); sec.bottom_margin=Cm(2.5)
for n in ['Normal','Body Text']:
 st=d.styles[n]; st.font.name='Times New Roman'; st._element.rPr.rFonts.set(qn('w:eastAsia'),'Times New Roman'); st.font.size=Pt(13); st.paragraph_format.line_spacing=1.5
for n,z in [('Heading 1',15),('Heading 2',14),('Heading 3',13)]:
 st=d.styles[n]; st.font.name='Times New Roman'; st._element.rPr.rFonts.set(qn('w:eastAsia'),'Times New Roman'); st.font.size=Pt(z); st.font.bold=True
def p(t='',center=False,bold=False,style=None):
 x=d.add_paragraph(style=style); x.alignment=WD_ALIGN_PARAGRAPH.CENTER if center else None
 if t: x.add_run(t).bold=bold
 return x
def h(t,l=1): return d.add_heading(t,l)
def field(x,code):
 r=x.add_run(); q=OxmlElement('w:fldChar'); q.set(qn('w:fldCharType'),'begin'); r._r.append(q)
 r=x.add_run(); q=OxmlElement('w:instrText'); q.set(qn('xml:space'),'preserve'); q.text=code; r._r.append(q)
 r=x.add_run(); q=OxmlElement('w:fldChar'); q.set(qn('w:fldCharType'),'separate'); r._r.append(q); r.add_text('Update field in Word')
 r=x.add_run(); q=OxmlElement('w:fldChar'); q.set(qn('w:fldCharType'),'end'); r._r.append(q)
def tab(title,headers,rows):
 p(title,center=True,bold=True,style='Caption'); t=d.add_table(rows=1,cols=len(headers)); t.style='Table Grid'
 for i,v in enumerate(headers): t.rows[0].cells[i].text=str(v)
 for row in rows:
  c=t.add_row().cells
  for i,v in enumerate(row): c[i].text=str(v)
 for row in t.rows:
  for c in row.cells:
   for x in c.paragraphs:
    x.paragraph_format.line_spacing=1
    for r in x.runs: r.font.name='Times New Roman'; r._element.get_or_add_rPr().rFonts.set(qn('w:eastAsia'),'Times New Roman'); r.font.size=Pt(11)
def br(): d.add_page_break()
for x in ['BỘ CÔNG THƯƠNG','TRƯỜNG ĐẠI HỌC CÔNG THƯƠNG TP. HCM','KHOA CÔNG NGHỆ THÔNG TIN']: p(x,True,True)
p('BÁO CÁO ĐỒ ÁN MÔN NOSQL',True,True)
p('TÌM HIỂU CÔNG CỤ FAUXTON ĐỂ QUẢN TRỊ CSDL TÀI LIỆU COUCHDB CHO HỆ THỐNG QUẢN LÝ ĐƠN HÀNG VÀ GIAO HÀNG CỦA CỬA HÀNG BÁN LẺ TRỰC TUYẾN. XÂY DỰNG ỨNG DỤNG MINH HỌA.',True,True)
for x in ['GIẢNG VIÊN HƯỚNG DẪN: NGUYỄN THỊ ĐỊNH','2001231032 – NGUYỄN NGỌC TƯỜNG VI – 14DHTH12','2001231062 – NGUYỄN VIỆT VƯƠNG – 14DHTH02','2001230266 – NGUYỄN HUY HOÀNG – 14DHTH02','Tp. HCM, tháng 10 năm 2026']: p(x,True)
br(); h('LỜI CAM ĐOAN'); p('Nhóm cam đoan đồ án do nhóm thực hiện dưới sự hướng dẫn của giảng viên Nguyễn Thị Định. Kết quả được trình bày theo mã nguồn và bằng chứng trong dự án.')
br(); h('LỜI CẢM ƠN'); p('Nhóm xin cảm ơn giảng viên hướng dẫn đã góp ý và các thành viên đã phối hợp xây dựng ứng dụng, kiểm tra dữ liệu, hoàn thiện báo cáo.')
br(); h('NHẬN XÉT CỦA GIẢNG VIÊN'); p('\n\n\n\n\n\n\n\n\n\n')
br(); h('MỤC LỤC'); field(d.add_paragraph(),'TOC \\o "1-3" \\h \\z \\u')
br(); h('DANH MỤC HÌNH'); field(d.add_paragraph(),'TOC \\h \\z \\c "Figure"')
br(); h('DANH MỤC BẢNG'); field(d.add_paragraph(),'TOC \\h \\z \\c "Table"'); br()
h('CHƯƠNG 1 – MỞ ĐẦU')
for title,text in [('1.1 Lý do chọn đề tài','Đơn hàng trực tuyến gắn sản phẩm, người nhận, thanh toán và giao hàng. Đồ án thực hành mô hình document CouchDB và quản trị qua Fauxton.'),('1.2 Mục tiêu','Thực hành Fauxton, Mango, index, MapReduce View, revision, validation và PHP app minh họa order/delivery.'),('1.3 Phạm vi','Database retail_order_delivery_demo, CouchDB 3.5, PHP Docker Compose; feature tests dùng dữ liệu demo/scratch.'),('1.4 Phương pháp','Seed xác định, chạy query/view, so index, kiểm HTTP và đối chiếu facts.'),('1.5 Ý nghĩa','Minh họa document model trong một nghiệp vụ; không khẳng định thay thế SQL phổ quát.'),('1.6 Bố cục','Sáu chương: mở đầu, lý thuyết, Fauxton, thiết kế, ứng dụng/kiểm thử, kết luận.')]: h(title,2); p(text)
h('1.7 Phân công công việc',2); team=[]
for row in old.tables[0].rows[1:]:
 a=[c.text.replace('\n',' / ') for c in row.cells]; team.append(a[:5]+['[NHÓM ĐIỀN]','[NHÓM GHI CHÚ ĐÓNG GÓP]'])
tab('Bảng 1.1. Phân công',['STT','Họ tên','MSSV','Lớp','Công việc','Hoàn thành','Ghi chú'],team)
h('CHƯƠNG 2 – CƠ SỞ LÝ THUYẾT VỀ NOSQL, COUCHDB VÀ FAUXTON')
for i in range(118,147):
 z=old.paragraphs[i]; text=z.text.strip()
 if not text or any(x in text.lower() for x in ['shopquan_ao','phase06','phase07','phase03','webthoitrang','so sánh fauxton với mongodb compass']): continue
 if z.style.name=='Heading 2': h(text,2)
 elif z.style.name=='Heading 3': h(text,3)
 else: p(text)
for x in ['Kiến trúc CouchDB','CAP và eventual consistency','Replication','Cluster','Compaction','Cài đặt thứ hai']:
 br(); h('2.x '+x,2); p('[NỘI DUNG GĐ2 – NHÓM SẼ DÁN] theo tài liệu tham khảo được nhóm xác nhận.')
h('CHƯƠNG 3 – TÌM HIỂU VÀ SỬ DỤNG FAUXTON')
h('3.1 Cài đặt',2); p('CouchDB 3.5.0 và PHP app chạy Docker Compose. Credential lấy từ cấu hình local, không ghi secret.')
h('3.2 Chức năng Fauxton',2)
for x in ['Databases/All Documents','JSON editor/Design Documents','Mango Query','Manage Indexes/Explain','Views','Replication','Attachments','Permissions (_security)','Active Tasks','Configuration','Compaction/xóa DB']:
 h(x,3); p('Mục đích và kết quả theo phase3 evidence; ảnh runtime chụp mới theo FIGURES_TODO.')
br(); h('3.3 Mango cơ bản',2); qr=[]
for k,v in f1['queries'].items():
 if re.match(r'^\d+',k): qr.append([re.match(r'^\d+',k).group(),v.get('purpose',''),v.get('index',''),v.get('results',''),v.get('docs_examined',''),v.get('execution_time_ms',''),v.get('status','')])
tab('Bảng 3.1. Mango Query',['Mã','Mục đích','Index','KQ','Examined','ms','Status'],qr[:12])
br(); h('3.4 Mango nâng cao',2); ar=[]
for k,v in adv['queries'].items():
 if isinstance(v,dict): ar.append([k,v.get('purpose',v.get('description','')),v.get('index',''),v.get('results',v.get('result_count','')),v.get('docs_examined',''),'PASS'])
tab('Bảng 3.2. Query 13–20',['Mã','Mục đích','Index','KQ','Examined','Status'],ar)
br(); h('3.5 Index',2); ir=[[k,v['index'],v['docs_examined'],v['results']] for k,v in f1['guest_index_before_after'].items()]
ir += [['Q'+x['query']+' sau',x['after_index'],x['after_docs_examined'],x['after_results']] for x in f1['delivery_index_before_after']]
tab('Bảng 3.3. Index trước/sau',['Stage','Index','Examined','KQ'],ir)
idx=json.loads((R/'database/couchdb/indexes/catalog_indexes.json').read_text(encoding='utf-8'))['views']
tab('Bảng 3.4. Danh mục Mango index',['Tên index','Trường theo thứ tự','Partial filter'],[[name,', '.join(view.get('options',{}).get('def',{}).get('fields',[])),json.dumps(view.get('options',{}).get('def',{}).get('partial_filter_selector',{}),ensure_ascii=False)] for name,view in idx.items()])
br(); h('3.6 Views',2); tab('Bảng 3.5. Views',['View/check','Status'],[[k,v.get('status','') if isinstance(v,dict) else v] for k,v in vf['checks'].items()])
p(f"{vf['orders_count']} orders; tổng grand_total theo view {sum(vf['order_total_by_status'].values()):,} VND.")
for qi,code in enumerate(['01','06','08']):
 q=next((v for k,v in f1['queries'].items() if k.startswith(code)),{})
 if qi: br()
 h('Payload Mango '+code,3); p(json.dumps(q.get('payload',{}),ensure_ascii=False,indent=2))
feat=f3['fauxton_features']
tab('Bảng 3.6. Bằng chứng chức năng CouchDB',['Chức năng','Kết quả runtime','HTTP/đối chiếu','Status'],[
['Replication',f"{feat['replication']['counts']['source']}/{feat['replication']['counts']['target']} docs",'normalized hash mismatches = 0',feat['replication']['status']],
['Conflict',feat['replication_conflict']['conflicted_document']['winner']['value'],'winner được đọc từ revision tree',feat['replication_conflict']['status']],
['Attachment',str(feat['attachment']['attachment_length_bytes'])+' bytes','digest readback khớp; delete 200',feat['attachment']['status']],
['_security',f"member {feat['security']['member_read_http']}; outsider {feat['security']['outsider_read_http']}",f"design update {feat['security']['member_design_update_http']}",feat['security']['status']],
['Compaction',f"active {feat['compaction_delete']['before']['data_size']} → {feat['compaction_delete']['after_compaction']['data_size']} bytes",f"file {feat['compaction_delete']['before']['disk_size']} → {feat['compaction_delete']['after_compaction']['disk_size']} bytes",feat['compaction_delete']['status']]])
for n in range(1,15): br(); h(f'Hình 3.{n} – Fauxton',3); p(f'[CHÈN HÌNH 3.{n} – xem FIGURES_TODO]')
h('3.7 So sánh công cụ',2); p('[NỘI DUNG GĐ2 – NHÓM SẼ DÁN]')
h('3.8 Loại ứng dụng phù hợp',2); p('[NỘI DUNG GĐ2 – NHÓM SẼ DÁN]')
h('CHƯƠNG 4 – THIẾT KẾ DỮ LIỆU')
h('4.1–4.2 Bài toán và document',2); p('Order lưu snapshot items/payment/totals và delivery history; revision bảo vệ ghi đồng thời; service xử lý quyền/workflow.')
br(); h('4.3 Dataset',2)
tab('Bảng 4.1. Documents',['Type','Count'],list(f1['documents_by_type'].items()))
tab('Bảng 4.2. Order status',['Status','Count'],list(f1['orders_by_status'].items()))
tab('Bảng 4.3. Delivery status',['Status','Count'],list(f1['delivery_tracking_by_status'].items()))
p(f"{f1['database']}: {f1['total_documents']} docs = {f1['business_documents']} business + {f1['design_documents']} design; {f1['documents_by_type']['order']} orders; {f1['guest_index_before_after']['after']['results']} guest.")
br(); h('4.4 CSV/backup/restore',2); b=f3['backup_restore']; c=f3['csv_exchange']; rb=f3['fauxton_features']['backup_replication']
tab('Bảng 4.4. Exchange',['Action','Evidence','Status'],[['JSON',f"{b['restored_documents']}/{b['backup_documents']}; diff 0/0/0",b['status']],['Replication',f"{rb['source_doc_count']}/{rb['target_doc_count']}; diff 0",rb['status']],['CSV import',f"{c['imported_count']}; {len(c['row_errors'])} row errors",c['status']],['CSV export',f"{c['export_rows']} rows",c['status']]])
for title,text in [('Export CSV','Query export dùng index admin_orders và ghi số dòng đã xuất để đối chiếu với số kết quả.'),('Import CSV','Importer tách dòng lỗi và dữ liệu hợp lệ; validator từ chối dòng không đạt ràng buộc.'),('Backup JSON','Backup logic lưu các document có trong snapshot; restore tạo database đích mới và so sánh IDs/nội dung.'),('Backup bằng replication','Replication ghi sang database đích; xác minh count, missing/extra IDs, normalized content và security.'),('Phạm vi khôi phục','JSON/replication không thay bản sao toàn node, Docker volume, cấu hình server hoặc toàn bộ lịch sử revision.')]: h(title,3); p(text)
h('CHƯƠNG 5 – ỨNG DỤNG VÀ KIỂM THỬ')
h('5.1 PHP–CouchDB',2); p('Backend PHP gọi CouchDB qua HTTP; repository/service tách truy vấn và nghiệp vụ.')
for x,t in [('5.2.1 User admin','Manager thao tác tài khoản; staff/customer bị chặn; update có CSRF và _rev.'),('5.2.2 Catalog','Tạo/sửa/ẩn/khôi phục có validation.'),('5.2.3 Order','Checkout tạo snapshot; detail đọc từ order document.'),('5.2.4 Delivery','Staff cập nhật workflow, customer xem timeline.'),('5.2.5 Manager backup','Tạo/download JSON có role, CSRF, filename guard.')]: h(x,3); p(t)
tab('Bảng 5.1. Admin checks',['Phạm vi','Số','Status'],[['User, role, CSRF, backup',adm['check_count'],adm['status']]])
br(); tab('Bảng 5.2. Danh sách HTTP checks quản trị',['#','Kiểm tra','Kết quả'],[[i,x['name'],x['result']] for i,x in enumerate(adm['checks'],1)])
h('5.3 SSE',2); p('CouchDB _changes phát sự kiện SSE; client reconnect.')
h('5.4 Revision/conflict',2); p('Stale _rev trả 409; replication conflict chạy trên scratch DB.')
br(); h('5.5 Validator',2); tab('Bảng 5.3. Validator',['Case','Expected','Actual','Result'],[[x['case'],x['expected_http'],x['actual_http'],x['result']] for x in val['cases']])
p('6 cases PASS: 4 HTTP 403, 2 HTTP 201.')
br(); h('5.6 Test summary',2); tab('Bảng 5.4. Test',['Area','Cases','Status'],[['Seed/verify',1,'PASS'],['Mango basic',12,'PASS'],['Mango advanced',8,'PASS'],['Views',8,'PASS'],['Admin',adm['check_count'],adm['status']],['Validator',val['check_count'],val['status']]])
import io
from docx.shared import Inches
def app_pic(idx,caption):
 sh=old.inline_shapes[idx-1]
 rid=sh._inline.graphic.graphicData.pic.blipFill.blip.embed
 blob=old.part.related_parts[rid].blob
 pp=d.add_paragraph(); pp.alignment=WD_ALIGN_PARAGRAPH.CENTER
 pp.add_run().add_picture(io.BytesIO(blob),width=Inches(5.3))
 p(caption,center=True,style='Caption')
for idx,caption in [(18,'Hình 5.1. Kiến trúc ứng dụng PHP–CouchDB'),(19,'Hình 5.2. Giao diện đăng nhập'),(20,'Hình 5.3. Biểu mẫu quản lý sản phẩm'),(21,'Hình 5.4. Danh sách sản phẩm'),(22,'Hình 5.5. Checkout'),(23,'Hình 5.6. Chi tiết order'),(24,'Hình 5.7. Danh sách order quản trị')]:
 br(); app_pic(idx,caption)
for n,title in [(8,'Cập nhật giao hàng'),(9,'Delivery timeline'),(10,'HTTP 409 stale revision'),(11,'Validator request/response'),(12,'Replication conflict'),(13,'Backup manager'),(14,'Realtime SSE')]:
 br(); h(f'Hình 5.{n} – {title}',3); p(f'[CHÈN HÌNH 5.{n} – xem FIGURES_TODO]')
h('CHƯƠNG 6 – KẾT LUẬN')
h('6.1 Kết quả',2); p('Seed, Mango, Views, backup/restore, replication, admin và validator có evidence trong phạm vi demo.')
h('6.2 Hạn chế',2); tab('Bảng 6.1. Hạn chế',['Hạn chế','Ảnh hưởng'],[['Không GPS/provider','Tracking nội bộ.'],['Không payment gateway','Một số trạng thái thuộc demo.'],['SSE phụ thuộc mạng','Cần kiểm tra tải.'],['Backup JSON không backup toàn node','Không có revision tree/config/volume.'],['Hình/GĐ2 chờ nhóm','Cần hoàn thiện trước khi nộp.']])
h('6.3 Hướng phát triển',2); p('Bổ sung provider, kiểm tra tải SSE và nội dung GĐ2.')
h('6.4 Kết luận',2); p('Fauxton hỗ trợ quản trị CouchDB; PHP minh họa order document, index, revision và validation.')
h('TÀI LIỆU THAM KHẢO')
for z in old.paragraphs:
 if re.match(r'^\[\d+\]',z.text.strip()):
  ref=z.text.strip()
  if 'WEBTHOITRANG' in ref: ref='[9] Mã nguồn PHP–CouchDB của đồ án, README, các module và tài liệu kỹ thuật trong repository.'
  p(ref)
p('[CẦN BỔ SUNG TLTK GĐ2]')
for s in d.sections: field(s.footer.paragraphs[0],'PAGE')
x=OxmlElement('w:updateFields'); x.set(qn('w:val'),'true'); d.settings.element.append(x)
d.save(O/'BAOCAO_NOSQL_V2.docx')
print('Created',O/'BAOCAO_NOSQL_V2.docx','QA',len(qa),'slides',len(slides))

