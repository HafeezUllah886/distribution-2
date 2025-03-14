# Balance API Documentation

## Balance

**Purpose**: Retrieves the current account balance for the authenticated user.

**Request**:
- **Method**: GET
- **Endpoint**: `/api/balance`
- **Authorization**: Bearer Token (User must be authenticated)

**Response**:
- **Status Code**: 200 OK
- **Response Body**:
```json
{
    "status": "success",
    "data": {
        "balance": <current_balance>
    }
}
