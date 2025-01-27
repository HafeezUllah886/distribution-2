# Assigned Products / Customers API Documentation

This documentation covers the endpoints related to Assigned Products / Customers in the system.

## Authentication

All endpoints require authentication using a Bearer token. Include the token in the Authorization header:
```
Authorization: Bearer <your_token>
```

## Endpoints

### Get Orderbooker Products

Retrieves the list of active products assigned to the authenticated orderbooker.

**Endpoint:** `POST https://gs.diamondquetta.com/api/orderbookerproducts`

**Headers:**
- `Authorization: Bearer <token>`
- `Content-Type: application/json`

**Response:**
```json
{
    "products": [
        {
            "id": "integer",
            "name": "string",
            "name_urdu": "string",
            "price": "decimal",
            "units": [
                {
                    "id": "integer",
                    "unit_name": "string",
                    "value": "decimal"
                }
            ]
        }
    ]
}
```

### Get Customers

Retrieves the list of customers associated with the authenticated user's branch.

**Endpoint:** `POST https://gs.diamondquetta.com/api/customers`

**Headers:**
- `Authorization: Bearer <token>`
- `Content-Type: application/json`

**Response:**
```json
{
    "customers": [
        {
            "id": "integer",
            "branchID": "integer",
            "title": "string",
            "address": "string",
            "contact": "string",
            "email": "string",
            "c_type": "string",
            "credit_limit": "decimal",
            "areaID": "integer",
            "status": "string",
            "curren_balance": "decimal"
        }
    ]
}
```

## Error Responses

In case of errors, the API will return appropriate HTTP status codes along with error messages:

- `401 Unauthorized`: Invalid or missing authentication token
- `403 Forbidden`: User doesn't have permission to access the resource
- `404 Not Found`: Requested resource not found
- `500 Internal Server Error`: Server-side error

## Notes

- All endpoints require authentication using Sanctum
- The system filters out inactive products from the orderbooker products list
- Customer balances are calculated dynamically when retrieving the customer list
