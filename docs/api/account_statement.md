# Account Statement API

This API endpoint allows orderbookers to retrieve a his account statement details.

## Endpoint

```
GET /api/account_statement
```

## Authentication

This endpoint requires authentication. Include the bearer token in the request header:

```
Authorization: Bearer <your_token>
```

## Request Parameters

| Parameter   | Type    | Required | Description |
|------------|---------|----------|-------------|
| from       | date    | Yes      | Start date for the account statement |
| to         | date    | Yes      | End date for the account statement |

## Response

- **Success Response**:
  - **Code**: 200
  - **Content**: 
  ```json
  {
    "data": [
      {
        "id": 12345,
        "date": "2025-03-14",
        "cr": 100.00,
        "db": 100.00,
        "notes": "Deposit",
        "refID": "12345"
      },
    ]
  }


#### Validation Error (422 Unprocessable Entity)
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "from": [
            "The from field is required"
        ],
        "to": [
            "The to field is required"
        ]
    }
}
```

## Notes

- The endpoint will only return transactions between the specified dates