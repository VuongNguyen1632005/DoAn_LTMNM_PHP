# Fauxton screenshots to capture after runtime verification

Use Fauxton at `http://127.0.0.1:5984/_utils/`, sign in locally, and select `retail_order_delivery_demo`. Keep the database name visible in each capture and avoid showing credentials.

1. **Database list:** Fauxton home → Databases. Capture `retail_order_delivery_demo` in the list.
2. **All Documents:** open `retail_order_delivery_demo` → All Documents. Show the document list and total count, then capture a second page if needed to show design documents.
3. **Queries 01–12:** database → Mango Query → paste the exact corresponding JSON from `database/couchdb/demo_queries/01_*.json` through `12_*.json` → Run. Capture selector, result count, and index/warning shown by Fauxton for each query. For 06, capture pagination controls and returned count. The separate investigation of the historical query 01/06 discrepancies is closed by user decision; no old DevTools payload is required.
4. **Manage Indexes:** database → Manage Indexes. Show `_design/catalog_indexes`, all prior nine index names, and the new `delivery_status` index.
5. **Explain:** Mango Query → load query 01, click Explain, capture selected `admin_orders`; repeat for query 02 and query 06. Capture Explain for 09, 10, 11 showing `delivery_status`.
6. **Statistics view:** database → Design Documents → `_design/order_statistics` → `orders_by_status`, run with `group=true`; repeat for `orders_by_month` with `group=true`. Capture key and sum columns.
7. **Validator:** Design Documents → `_design/domain_validation`; show validator source without editing or saving.
