# Location Tracking API

## Store Location

This endpoint allows authenticated users to store their current location coordinates.

### Endpoint

```
POST /api/storelocation
```

### Authentication

Bearer Token authentication is required.

### Request Parameters

| Parameter  | Type    | Required | Description |
|------------|---------|----------|-------------|
| latitude   | numeric | Yes      | The latitude coordinate of the user's location |
| longitude  | numeric | Yes      | The longitude coordinate of the user's location |

### Success Response

**Code:** 200

**Content Example:**

```json
{
    "status": "success",
    "message": "Location stored successfully",
    "location": {
        "latitude": "23.7461",
        "longitude": "90.3742",
        "date": "2024-03-07",
        "time": "14:30:45",
        "userID": 1,
        "id": 1
    }
}
```

### Error Response

**Code:** 422 Unprocessable Entity

**Condition:** When required parameters are missing or invalid

**Content Example:**

```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "latitude": [
            "The latitude field is required."
        ],
        "longitude": [
            "The longitude field is required."
        ]
    }
}
```

### Sample Call

```bash
curl -X POST \
  'http://your-domain.com/api/store-location' \
  -H 'Authorization: Bearer {your_access_token}' \
  -H 'Content-Type: application/json' \
  -d '{
    "latitude": "23.7461",
    "longitude": "90.3742"
}'
```

### Notes

- The API automatically captures the current date and time when storing the location
- The userID is automatically determined from the authenticated user's token
- All timestamps are stored in the server's timezone