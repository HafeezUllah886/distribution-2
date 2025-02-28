# Payment Receiving API Documentation

This API endpoint allows Android clients to submit customer payments.

## Endpoint

```
POST /api/payment/receiving
```

## Authentication

This endpoint requires authentication using Laravel Sanctum. Include the bearer token in the request header:

```
Authorization: Bearer <your_access_token>
```

## Request Parameters

| Parameter   | Type    | Required | Description                                     |
|-------------|---------|----------|-------------------------------------------------|
| customerID  | integer | Yes      | The ID of the customer making the payment       |
| date        | string  | Yes      | The date of the payment                        |
| amount      | numeric | Yes      | The payment amount                             |
| notes       | string  | No       | Additional notes about the payment (optional)   |

## Example Request

```json
{
    "customerID": 123,
    "date": "2024-03-10",
    "amount": 1000.00,
    "notes": "Payment for invoice #INV-001"
}
```

## Success Response

**Status Code:** 201 Created

```json
{
    "status": "success",
    "message": "Payment received successfully",
    "data": {
        "payment": {
            "customerID": 123,
            "date": "2024-03-10",
            "amount": 1000.00,
            "branchID": 1,
            "notes": "Payment for invoice #INV-001",
            "receivedBy": 1,
            "refID": "REF123"
        }
    }
}
```

## Error Responses

### Validation Error

**Status Code:** 422 Unprocessable Entity

```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "customerID": ["The customer id field is required"],
        "date": ["The date field is required"],
        "amount": ["The amount field is required"]
    }
}
```

### Server Error

**Status Code:** 500 Internal Server Error

```json
{
    "status": "error",
    "message": "Error message details"
}
```

## Notes

- The API automatically captures the branch ID and user ID from the authenticated user's context
- All monetary amounts should be sent as numeric values
- Dates should be in YYYY-MM-DD format
- The response includes a unique reference ID (refID) for tracking the transaction
- The API creates corresponding transaction records for both the customer and user accounts