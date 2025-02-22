## Delete Order API

### Endpoint
`GET /order/delete`

### Authentication
This endpoint requires authentication using a Bearer token via `auth:sanctum` middleware.

### Request Parameters
| Parameter   | Type   | Required | Description |
|------------|--------|----------|-------------|
| order_id   | int    | Yes      | The ID of the order to be deleted |

### Headers
| Header           | Type   | Required | Description |
|-----------------|--------|----------|-------------|
| Authorization   | string | Yes      | Bearer token for authentication |

### Response
#### Success Response
**Status Code:** `201`
```json
{
    "status": "success",
    "message": "Order deleted successfully"
}
```

#### Error Responses
- **Order Cannot Be Deleted (422)**
```json
{
    "status": "error",
    "message": "Order cannot be deleted"
}
```

- **Unauthorized Order Access (422)**
```json
{
    "status": "error",
    "message": "This order does not belong to you"
}
```

- **General Error (500)**
```json
{
    "status": "error",
    "message": "Error message details"
}
```

### Notes
- Orders with `Finalized` or `Approved` status cannot be deleted.
- Only the order creator (`orderbookerID`) can delete the order.
- Transaction is rolled back in case of an error.

