# Fauxton screenshots to capture

No screenshots were taken during this task. Capture these manually in Fauxton at `http://127.0.0.1:5984/_utils/` after signing in; keep the database name and relevant controls visible, and never include credentials or password hashes. Scratch databases are removed by the reproducible scripts, so recreate them with the listed scripts or repeat the documented setup before capture.

## Demo database and Mango queries

| File name | Database | GUI path and ordered actions | Expected visible content |
|---|---|---|---|
| `01-demo-database.png` | `retail_order_delivery_demo` | Fauxton home → Databases → select demo → Overview | 150 documents; 147 business documents and 3 design documents |
| `02-demo-indexes.png` | `retail_order_delivery_demo` | Database → Manage Indexes; expand `_design/catalog_indexes` | 15 JSON Mango indexes including `guest_orders`, `delivery_status`, and `orders_by_total` (the `_index` API also returns the special `_all_docs` row) |
| `03-query-01-pending.png` | `retail_order_delivery_demo` | Mango Query → paste `database/couchdb/demo_queries/01_pending_orders.json` → Run | 11 matching orders; selected `admin_orders` index |
| `04-query-02-shipping.png` | `retail_order_delivery_demo` | Mango Query → paste query 02 JSON → Run | 15 shipping orders; selected JSON index |
| `05-query-03-delivered.png` | `retail_order_delivery_demo` | Mango Query → paste query 03 JSON → Run | 10 delivered orders; selected JSON index |
| `06-query-04-cancelled.png` | `retail_order_delivery_demo` | Mango Query → paste query 04 JSON → Run | 7 cancelled orders; selected JSON index |
| `07-query-05-customer.png` | `retail_order_delivery_demo` | Mango Query → paste query 05 JSON → Run | 4 orders for `DEMO-C001`; selected `customer_orders` |
| `08-query-06-pagination.png` | `retail_order_delivery_demo` | Mango Query → paste query 06 JSON → Run → click Show more until exhausted | 65 orders over 3 non-empty pages (25, 25, 15); limit 25; `admin_orders_by_date` |
| `09-query-07-unpaid-cod.png` | `retail_order_delivery_demo` | Mango Query → paste query 07 JSON → Run | 8 unpaid COD pending orders; selected JSON index |
| `10-query-08-guests.png` | `retail_order_delivery_demo` | Mango Query → paste query 08 JSON → Run | 15 guest orders; selected `guest_orders`; `docs_examined=15` |
| `11-query-09-delivery-transit.png` | `retail_order_delivery_demo` | Mango Query → paste query 09 JSON → Explain → Run | 5 in-transit orders; selected `delivery_status` |
| `12-query-10-delivery-out.png` | `retail_order_delivery_demo` | Mango Query → paste query 10 JSON → Explain → Run | 2 out-for-delivery orders; selected `delivery_status` |
| `13-query-11-delivery-failed.png` | `retail_order_delivery_demo` | Mango Query → paste query 11 JSON → Explain → Run | 3 failed-delivery orders; selected `delivery_status` |
| `14-query-12-tracking-code.png` | `retail_order_delivery_demo` | Mango Query → paste query 12 JSON → Explain → Run | 1 matching tracking code; selected `delivery_tracking_code` |
| `15-advanced-query-13-20.png` | `retail_order_delivery_demo` | Mango Query → paste each query JSON 13–20 and run; capture one image per query if a single capture is unreadable | Query results/indexes match `docs/phase3/ADVANCED_QUERIES.md`; all eight runtime checks PASS |
| `16-order-statistics-views.png` | `retail_order_delivery_demo` | Design Documents → `_design/order_statistics` → run `order_count_by_status`, `order_total_by_status`, `orders_by_month` with `group=true&group_level=2`, `top_products?group=true`, `revenue_delivered_by_month?group=true&group_level=2`, and `orders_by_customer?reduce=false&include_docs=true` | View keys and aggregate values match `docs/phase3/VIEWS.md` |
| `17-full-domain-validator.png` | `retail_order_delivery_demo` | Design Documents → `_design/domain_validation`; open source/document view without editing | Full `validate_doc_update` function is visible; do not save edits |

For a later investigation of the legacy discrepancies, open browser DevTools → Network while running the original query 01 and query 06 in the exact historical Fauxton database. Select each `/_find` request and capture Request Payload, including `use_index`, `sort`, `limit`, and `bookmark`. Those historical payloads were not available during this work, so the original two discrepancies remain unverified.

## Scratch feature demonstrations

| File name | Database | GUI path and ordered actions | Expected visible content |
|---|---|---|---|
| `18-replication.png` | Source `retail_order_delivery_demo`, target `retail_demo_replica_scratch` | Replication → create one-shot replication with selector `{"_id":{"$ne":"_design/domain_validation"}}` → compare target count → enable continuous briefly → cancel; install validator design doc after replication | Source/target 150 docs; normalized content hashes match; scheduler job appears then is canceled |
| `19-replication-conflict.png` | `retail_conflict_left_scratch`, `retail_conflict_right_scratch` | Create same document in left → replicate to right → edit both with their current `_rev` → replicate → GET right document with `?conflicts=true` → delete the losing leaf revision | Winner is `edited-on-right`; one losing revision before resolution; no `_conflicts` after deletion |
| `20-attachment.png` | `retail_attachment_scratch` | Create product document → Add Attachment → upload `tiny.png` as `image/png` → open attachment → delete attachment | 1×1 PNG, 68 bytes, expected MD5/digest; attachment is absent after delete |
| `21-security.png` | `retail_security_scratch` | Create member user in `_users` → create DB and ordinary/design docs → Security → set member name → verify member read, outsider read, member design edit | HTTP 200, 403, 403 respectively; do not capture passwords; remove test user and DB after capture |
| `22-compaction-delete.png` | `retail_compaction_scratch` | Create document → update repeatedly using latest `_rev` → record Overview sizes → click Compact → wait until complete → record sizes → delete DB | 51 revisions; active size 3195→2437; file size 225629→33123; delete HTTP 200 and subsequent GET 404 |

## Backup, restore, and CSV

| File name | Database | GUI path and ordered actions | Expected visible content |
|---|---|---|---|
| `23-replication-backup.png` | Source `retail_order_delivery_demo`, target `retail_order_delivery_backup_scratch` | Replication → one-shot replication with validator excluded → install validator on target → compare documents and Security | 150/150 documents; no missing, extra, or different IDs/content; `_security` matches |
| `24-json-backup.png` | `retail_order_delivery_demo` | Open database Overview and `_security`; compare to generated `var/backups/phase3-evidence-20261028.json` | 150 documents, 3 design docs, `_security` included; use only this single dated evidence backup |
| `25-json-restore.png` | `retail_phase3_restore_test` | Run documented restore wrapper → open target database → inspect `_design` docs and representative order | 150 documents; content parity; 15 JSON Mango indexes (16 `_index` rows including `_all_docs`); validator rejects invalid write with HTTP 403; target removed after verification |
| `26-csv-import.png` | `retail_csv_import_scratch` | Run `verify_csv_exchange.php`; inspect imported products and validator response evidence | 3 products accepted; CSV row 5 rejected with validator reason in evidence; scratch removed |
| `27-csv-export.png` | `retail_order_delivery_demo` | Open exported `docs/phase3/exports/orders_phase3_20261028.csv`; compare with Mango orders count | 65 CSV data rows and 65 query results; selected `admin_orders` index |

## Application administration

| File name | Database | GUI path and ordered actions | Expected visible content |
|---|---|---|---|
| `28-admin-users-search.png` | `retail_admin_http_<run-id>_test` | Run `tests/admin_users_integration.php`; while signed in as manager open `/admin/users`, search and filter | Matching customer only; no password hash or auth object in rendered page |
| `29-admin-user-detail-edit.png` | Same isolated HTTP test DB | Open a seeded staff detail → Edit → submit current revision; then replay stale revision | First save succeeds; stale save returns HTTP 409 and conflict message; newest profile stays intact |
| `30-admin-user-status-create.png` | Same isolated HTTP test DB | Manager locks then unlocks customer; open New staff and create account | State changes; new account has role Staff; missing CSRF makes no state change |
| `31-manager-backup.png` | Same isolated HTTP test DB | Manager → Sao lưu → create → download; attempt traversal-style name | Unique JSON backup downloads; missing CSRF and traversal are rejected; backup file is cleaned afterward |
| `32-role-denials.png` | Same isolated HTTP test DB | Login as staff then customer; visit `/admin/users`, `/admin/backup`, create/download routes | Each non-manager receives HTTP 403; generated backup is not downloadable |

## Full validator requests

| File name | Database | GUI path and ordered actions | Expected visible content |
|---|---|---|---|
| `33-validator-invalid-status.png` | `retail_full_validator_<run-id>_test` | Run `scripts/verify_full_validator.php`; inspect captured request/response evidence | Full validator returns HTTP 403 for `invalid_status`; response reason is recorded verbatim |
| `34-validator-delivery-payment.png` | Same scratch DB | Inspect evidence for shortened delivery history, paid→unpaid, and skipped delivery transition | Each request returns HTTP 403 with the exact reason stored in `validator_tests_evidence.json` |
| `35-validator-valid-write.png` | Same scratch DB | Inspect valid shipping/tracking create and `created`→`picked_up` update | Both CouchDB writes return HTTP 201; revisions advance |
