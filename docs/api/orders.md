# Orders API Documentation

## Create Order

Creates a new order with its details.

**URL**: `https://gs.diamondquetta.com/api/order/store`

**Method**: `POST`

**Authentication**: Required (`auth:sanctum`)

### Request Body

| Field | Type | Description | Required |
|-------|------|-------------|-----------|
| customerID | integer | ID of the customer from accounts table | Yes |
| date | date | Date of the order (YYYY-MM-DD) | Yes |
| notes | string | Additional notes for the order | No |
| id[] | array | Array of product IDs | Yes |
| unit[] | array | Array of product unit IDs | Yes |
| pack_qty[] | array | Array of pack quantities | Yes |
| loose_qty[] | array | Array of loose quantities | Yes |
| price[] | array | Array of product prices | Yes |

### Validation Rules

- `customerID`: Must exist in the accounts table
- `date`: Must be a valid date
- `id.*`: Each product ID must exist in products table
- `unit.*`: Each unit ID must exist in product_units table
- `pack_qty.*`: Must be numeric and >= 0
- `loose_qty.*`: Must be numeric and >= 0
- `price.*`: Must be numeric and >= 0
- At least one product must be included in the order

### Example Request

```json
{
    "customerID": 1,
    "date": "2025-01-29",
    "notes": "Urgent delivery required",
    "id": [1, 2],
    "unit": [1, 1],
    "pack_qty": [5, 3],
    "loose_qty": [2, 0],
    "price": [100.00, 150.00]
}
```

### Success Response

**Code**: `201 Created`

```json
{
    "status": "success",
    "message": "Order created successfully",
    "data": {
        "order": {
            "id": 1,
            "customerID": 1,
            "branchID": 1,
            "orderbookerID": 1,
            "date": "2025-01-29",
            "notes": "Urgent delivery required",
            "refID": "123456",
            "created_at": "2025-01-29T14:21:56+05:00",
            "updated_at": "2025-01-29T14:21:56+05:00"
        },
        "order_details": [
            {
                "id": 1,
                "orderID": 1,
                "productID": 1,
                "price": 100.00,
                "pack_qty": 5,
                "loose_qty": 2,
                "total_pieces": 52,
                "date": "2025-01-29",
                "unitID": 1,
                "refID": "123456"
            },
            {
                "id": 2,
                "orderID": 1,
                "productID": 2,
                "price": 150.00,
                "pack_qty": 3,
                "loose_qty": 0,
                "total_pieces": 30,
                "date": "2025-01-29",
                "unitID": 1,
                "refID": "123456"
            }
        ]
    }
}
```

### Error Responses

**Condition**: When validation fails

**Code**: `422 Unprocessable Entity`
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "customerID": ["The selected customerID is invalid."],
        "pack_qty.0": ["The pack qty must be a number."]
    }
}
```

**Condition**: When no products are selected

**Code**: `422 Unprocessable Entity`
```json
{
    "status": "error",
    "message": "Please select at least one product"
}
```

**Condition**: When server error occurs

**Code**: `500 Internal Server Error`
```json
{
    "status": "error",
    "message": "Error message details"
}
```

### Notes

1. The `total_pieces` for each order detail is automatically calculated:
   - `total_pieces = (pack_qty * unit.value) + loose_qty`
2. Each order is assigned a unique `refID`
3. The `branchID` and `orderbookerID` are automatically set based on the authenticated user
4. All arrays (`id`, `unit`, `pack_qty`, `loose_qty`, `price`) must have the same length
5. The timezone is set to Asia/Karachi
