# Runtime reverify

- CouchDB 3.5.0 và PHP app healthy.
- db:seed-demo -- --recreate: PASS; 147 business + 3 design; indexes ready.
- seed:verify: PASS; 150 docs, 65 orders, 15 guest orders; distribution matches refreshed phase1 facts.
- phase1:report: PASS; verify_views.php 8/8 PASS; verify_advanced_queries.php 13–20 PASS.

| Query | Results | Pages | docs_examined/page | Independent count |
|---|---:|---:|---|---:|
| 01 | 11 | 3 | 11,1,0 | 11 PASS |
| 06 | 65 | 4 | 26,26,15,0 | 65 PASS |
| 08 | 15 | 2 | 15,0 | 15 PASS |
| 03 | 10 | 2 | 10,0 | 10 PASS |
| 12 | 1 | 2 | 1,0 | 1 PASS |

No mismatch against current facts. Query 01/06/08 included; 03/12 supplementary. Runtime page evidence is in this table; execution times come from refreshed facts.
