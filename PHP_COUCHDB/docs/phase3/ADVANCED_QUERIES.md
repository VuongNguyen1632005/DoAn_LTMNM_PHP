# Truy vấn Mango nâng cao

Cơ sở dữ liệu: `retail_order_delivery_demo`. Bằng chứng dưới đây lấy từ `_explain`, `_find`/`execution_stats` và bộ lọc độc lập PHP trên documents hiện có. Từng payload JSON có thể dán vào Fauxton Mango Query.

| Mã | Mục đích | Toán tử | Index `_explain` | Kết quả / đếm PHP | docs_examined | execution_time_ms | Trạng thái |
|---:|---|---|---|---:|---:|---:|---|
| 13 | Order pending hoặc confirmed của một khách cụ thể. | `$or` | `customer_orders_status` | 1 / 1 | 7 | 72.664 | PASS |
| 14 | Lọc nhiều trạng thái và loại cancelled. | `$in, $nin` | `admin_orders` | 37 / 37 | 0 | 5.996 | PASS |
| 15 | Order có một item quantity ≥ 2 và unit_price > 200000 VND. | `$elemMatch, $gte, $gt` | `orders_type_scan` | 48 / 48 | 66 | 31.49 | PASS |
| 16 | Order theo khoảng tổng tiền và ngày, sắp xếp theo tổng tiền. | `$gt, $lte, $gte, $lt, sort` | `orders_by_total` | 1 / 1 | 2 | 5.911 | PASS |
| 17 | Order có delivery_tracking; dùng $exists=false để tìm order không có tracking. | `$exists` | `orders_type_scan` | 15 / 15 | 82 | 33.546 | PASS |
| 18 | Tìm tên sản phẩm theo regex, giới hạn tập ứng viên bằng type=product và active=true có index. | `$regex` | `active_products` | 5 / 5 | 55 | 20.656 | PASS |
| 19 | Order COD không cancelled, chiếu một số trường và giới hạn trang. | `$not, $and, fields, limit, skip` | `admin_orders` | 40 / 40 | 68 | 38.677 | PASS |
| 20 | Tra cứu trường lồng receiver.name vì schema demo không có receiver.city. | `nested field, $eq` | `orders_by_receiver` | 15 / 15 | 15 | 10.329 | PASS |

## Query 13 — Order pending hoặc confirmed của một khách cụ thể.

Toán tử: `$or`. Index: `customer_orders_status`. Kết quả: 1; đếm độc lập PHP: 1; docs_examined: 7; trạng thái: **PASS**.

```json
{
    "selector": {
        "type": {
            "$eq": "order"
        },
        "customer.customer_id": {
            "$eq": "DEMO-C001"
        },
        "$or": [
            {
                "status": {
                    "$eq": "pending"
                }
            },
            {
                "status": {
                    "$eq": "confirmed"
                }
            }
        ]
    },
    "use_index": [
        "_design/catalog_indexes",
        "customer_orders_status"
    ],
    "fields": [
        "_id",
        "customer",
        "status",
        "ordered_at",
        "totals.grand_total"
    ],
    "limit": 25,
    "execution_stats": true
}
```

## Query 14 — Lọc nhiều trạng thái và loại cancelled.

Toán tử: `$in, $nin`. Index: `admin_orders`. Kết quả: 37; đếm độc lập PHP: 37; docs_examined: 0; trạng thái: **PASS**.

```json
{
    "selector": {
        "type": {
            "$eq": "order"
        },
        "status": {
            "$in": [
                "pending",
                "confirmed",
                "shipping",
                "cancelled"
            ],
            "$nin": [
                "cancelled"
            ]
        }
    },
    "use_index": [
        "_design/catalog_indexes",
        "admin_orders"
    ],
    "sort": [
        {
            "status": "asc"
        },
        {
            "ordered_at": "asc"
        }
    ],
    "fields": [
        "_id",
        "status",
        "ordered_at"
    ],
    "limit": 50,
    "execution_stats": true
}
```

## Query 15 — Order có một item quantity ≥ 2 và unit_price > 200000 VND.

Toán tử: `$elemMatch, $gte, $gt`. Index: `orders_type_scan`. Kết quả: 48; đếm độc lập PHP: 48; docs_examined: 66; trạng thái: **PASS**.

```json
{
    "selector": {
        "type": {
            "$eq": "order"
        },
        "items": {
            "$elemMatch": {
                "quantity": {
                    "$gte": 2
                },
                "unit_price": {
                    "$gt": 200000
                }
            }
        }
    },
    "use_index": [
        "_design/catalog_indexes",
        "orders_type_scan"
    ],
    "fields": [
        "_id",
        "status",
        "items",
        "totals.grand_total"
    ],
    "limit": 50,
    "execution_stats": true
}
```

## Query 16 — Order theo khoảng tổng tiền và ngày, sắp xếp theo tổng tiền.

Toán tử: `$gt, $lte, $gte, $lt, sort`. Index: `orders_by_total`. Kết quả: 1; đếm độc lập PHP: 1; docs_examined: 2; trạng thái: **PASS**.

```json
{
    "selector": {
        "type": {
            "$eq": "order"
        },
        "totals.grand_total": {
            "$gt": 350000,
            "$lte": 500000
        },
        "ordered_at": {
            "$gte": "2026-06-01T00:00:00Z",
            "$lt": "2026-08-01T00:00:00Z"
        }
    },
    "use_index": [
        "_design/catalog_indexes",
        "orders_by_total"
    ],
    "sort": [
        {
            "totals.grand_total": "asc"
        }
    ],
    "fields": [
        "_id",
        "ordered_at",
        "status",
        "totals.grand_total"
    ],
    "limit": 50,
    "execution_stats": true
}
```

## Query 17 — Order có delivery_tracking; dùng $exists=false để tìm order không có tracking.

Toán tử: `$exists`. Index: `orders_type_scan`. Kết quả: 15; đếm độc lập PHP: 15; docs_examined: 82; trạng thái: **PASS**.

```json
{
    "selector": {
        "type": {
            "$eq": "order"
        },
        "delivery_tracking": {
            "$exists": true
        }
    },
    "use_index": [
        "_design/catalog_indexes",
        "orders_type_scan"
    ],
    "fields": [
        "_id",
        "status",
        "delivery_tracking.tracking_code",
        "delivery_tracking.status"
    ],
    "limit": 25,
    "execution_stats": true
}
```

## Query 18 — Tìm tên sản phẩm theo regex, giới hạn tập ứng viên bằng type=product và active=true có index.

Toán tử: `$regex`. Index: `active_products`. Kết quả: 5; đếm độc lập PHP: 5; docs_examined: 55; trạng thái: **PASS**.

```json
{
    "selector": {
        "type": {
            "$eq": "product"
        },
        "active": {
            "$eq": true
        },
        "name": {
            "$regex": "^Sản phẩm Demo 0[1-5]$"
        }
    },
    "use_index": [
        "_design/catalog_indexes",
        "active_products"
    ],
    "fields": [
        "_id",
        "name",
        "category",
        "variants"
    ],
    "limit": 25,
    "execution_stats": true
}
```

## Query 19 — Order COD không cancelled, chiếu một số trường và giới hạn trang.

Toán tử: `$not, $and, fields, limit, skip`. Index: `admin_orders`. Kết quả: 40; đếm độc lập PHP: 40; docs_examined: 68; trạng thái: **PASS**.

```json
{
    "selector": {
        "$and": [
            {
                "type": {
                    "$eq": "order"
                }
            },
            {
                "$not": {
                    "status": {
                        "$eq": "cancelled"
                    }
                }
            },
            {
                "payment.method": {
                    "$eq": "cod"
                }
            }
        ]
    },
    "use_index": [
        "_design/catalog_indexes",
        "admin_orders"
    ],
    "sort": [
        {
            "status": "asc"
        },
        {
            "ordered_at": "asc"
        }
    ],
    "fields": [
        "_id",
        "status",
        "payment.method",
        "totals.grand_total"
    ],
    "limit": 10,
    "skip": 0,
    "execution_stats": true
}
```

## Query 20 — Tra cứu trường lồng receiver.name vì schema demo không có receiver.city.

Toán tử: `nested field, $eq`. Index: `orders_by_receiver`. Kết quả: 15; đếm độc lập PHP: 15; docs_examined: 15; trạng thái: **PASS**.

```json
{
    "selector": {
        "type": {
            "$eq": "order"
        },
        "receiver.name": {
            "$eq": "Khách vãng lai Demo"
        }
    },
    "use_index": [
        "_design/catalog_indexes",
        "orders_by_receiver"
    ],
    "sort": [
        {
            "receiver.name": "asc"
        }
    ],
    "fields": [
        "_id",
        "receiver.name",
        "status",
        "ordered_at"
    ],
    "limit": 25,
    "execution_stats": true
}
```

