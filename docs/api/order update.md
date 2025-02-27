# Update Order API Documentation

## Update Order

Updates an existing order and its details.

**URL**: `https://gs.diamondquetta.com/api/order/update`

**Method**: `POST`

**Authentication**: Required (`auth:sanctum`)

### Request Body

| Field | Type | Description | Required |
|-------|------|-------------|-----------|
| orderID | integer | ID of the order to update | Yes |
| date | date | Date of the order (YYYY-MM-DD) | Yes |
| notes | string | Additional notes for the order | No |
| id[] | array | Array of product IDs | Yes |
| unit[] | array | Array of product unit IDs | Yes |
| pack_qty[] | array | Array of pack quantities | Yes |
| loose_qty[] | array | Array of loose quantities | Yes |
| price[] | array | Array of product prices | Yes |

### Validation Rules

- `orderID`: Must exist in orders table
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
    "orderID": 1,
    "date": "2025-01-29",
    "notes": "Updated delivery instructions",
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
    "message": "Order Updated successfully",
    "data": {
        "order": {
            "id": 1,
            "customerID": 1,
            "branchID": 1,
            "orderbookerID": 1,
            "date": "2025-01-29",
            "notes": "Updated delivery instructions",
            "net": 1500.00,
            "status": "Pending",
            "updated_at": "2025-01-29T14:21:56+05:00"
        },
        "order_details": [
            {
                "id": 1,
                "orderID": 1,
                "productID": 1,
                "price": 100.00,
                "discount": 0,
                "discountp": 0,
                "discountvalue": 0,
                "qty": 5,
                "loose": 2,
                "pc": 52,
                "fright": 0,
                "labor": 0,
                "claim": 0,
                "netprice": 100.00,
                "amount": 5200.00,
                "date": "2025-01-29",
                "unitID": 1
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
        "date": ["The date field is required."],
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

**Condition**: When order is already approved/finalized

**Code**: `422 Unprocessable Entity`
```json
{
    "status": "error",
    "message": "Order Already Approved / Finalized"
}
```

**Condition**: When order doesn't belong to the orderbooker

**Code**: `422 Unprocessable Entity`
```json
{
    "status": "error",
    "message": "This order does not belong to you"
}
```

**Condition**: When credit limit is exceeded

**Code**: `422 Unprocessable Entity`
```json
{
    "status": "error",
    "message": "Customer credit limit exceeded"
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

1. All existing order details are deleted and replaced with new ones
2. The order must belong to the authenticated orderbooker
3. The order must not be in "Finalized" or "Approved" status
4. The total order amount must not exceed the customer's credit limit
5. Prices, discounts, and other calculations are automatically updated based on current product settings
6. All arrays (`id`, `unit`, `pack_qty`, `loose_qty`, `price`) must have the same length
7. The timezone is set to Asia/Karachi
