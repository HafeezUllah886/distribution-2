# Pending Invoices API

This API endpoint allows orderbookers to retrieve a list of pending (unpaid or partially paid) invoices for a specific customer.

## Endpoint

```
GET /api/pendinginvoices
```

## Authentication

This endpoint requires authentication. Include the bearer token in the request header:

```
Authorization: Bearer <your_token>
```

## Request Parameters

| Parameter   | Type    | Required | Description |
|------------|---------|----------|-------------|
| customerID | integer | Yes      | The unique identifier of the customer account |

## Response

### Success Response (200 OK)

```json
{
    "status": "success",
    "data": [
        {
            "salesID": 1,
            "total_bill": 1000.00,
            "paid": 600.00,
            "due": 400.00
        }
    ]
}
```

#### Response Fields

| Field      | Type    | Description |
|------------|---------|-------------|
| salesID    | integer | Unique identifier of the sale |
| total_bill | decimal | Total amount of the invoice |
| paid       | decimal | Amount already paid for this invoice |
| due        | decimal | Remaining amount to be paid |

### Error Responses

#### Validation Error (422 Unprocessable Entity)
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "customerID": [
            "The customerID field is required"
        ]
    }
}
```

#### Customer Not Found (404 Not Found)
```json
{
    "status": "error",
    "message": "Customer does not belong to the orderbooker"
}
```

## Example Usage

### cURL
```bash
curl -X GET \
  'http://your-domain.com/api/pendinginvoices?customerID=123' \
  -H 'Authorization: Bearer your_token_here'
```

### Android (Kotlin + Retrofit)
```kotlin
interface ApiService {
    @GET("pending-invoices")
    suspend fun getPendingInvoices(@Query("customerID") customerId: Int): Response<PendingInvoicesResponse>
}

data class PendingInvoicesResponse(
    val status: String,
    val data: List<PendingInvoice>
)

data class PendingInvoice(
    val salesID: Int,
    val total_bill: Double,
    val paid: Double,
    val due: Double
)
```

## Notes

- The endpoint will only return invoices where the remaining payment (due amount) is greater than zero
- Only orderbookers can access invoices for customers assigned to them
- The response is paginated and returns a maximum of 50 records per request