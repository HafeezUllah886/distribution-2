# Invoice Payments API

Endpoint for submitting payments against specific customer invoices.

## Endpoint

```
POST /api/invoicespayment
```

## Authentication

Requires authentication via `auth:sanctum`. Include the Bearer token in the request header:

```
Authorization: Bearer <token>
```

## Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| customerID | integer | Yes | ID of the customer from accounts table |
| saleIDs | array | Yes | Array of sale invoice IDs to apply payments to |
| amount | array | Yes | Array of payment amounts corresponding to each saleID |
| date | date | Yes | Date of payment (YYYY-MM-DD format) |
| notes | string | No | Additional notes for the payment |

### Example Request Body

```json
{
    "customerID": 1,
    "saleIDs": [101, 102],
    "amount": [500.00, 750.00],
    "date": "2024-03-01",
    "notes": "Partial payment for February invoices"
}
```

## Responses

### Success Response

**Code**: 200 OK

```json
{
    "status": "success",
    "data": [
        {
            "salesID": 101,
            "date": "2024-03-01",
            "amount": 500.00,
            "notes": "Partial payment for February invoices",
            "userID": 1,
            "refID": "REF123"
        }
    ]
}
```

### Error Responses

#### Validation Error

**Code**: 422 Unprocessable Entity

```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "customerID": ["The customerID field is required"],
        "saleIDs": ["The saleIDs field is required"],
        "amount": ["The amount field is required"]
    }
}
```

#### Server Error

**Code**: 500 Internal Server Error

```json
{
    "status": "error",
    "message": "Error message details"
}
```

## Notes

- The number of elements in `saleIDs` array must match the number of elements in the `amount` array
- Each `amount` value must be greater than or equal to 0
- The endpoint creates payment records and corresponding transactions for each invoice payment
- All payments are processed within a database transaction to ensure data consistency