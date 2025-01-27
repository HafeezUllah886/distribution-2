# Authentication API Documentation

## Base URL
```
/api
```

## Authentication Endpoints

### 1. Login
Authenticate a user and receive an access token.

**Endpoint:** `POST /login`

**Headers:**
```http
Accept: application/json
```

**Request Body:**
```json
{
    "user_name": "string",
    "password": "string"
}
```

**Request Parameters:**
| Parameter  | Type   | Required | Description         |
|------------|--------|----------|---------------------|
| user_name  | string | Yes      | User's username     |
| password   | string | Yes      | User's password     |

**Success Response:**
- Status Code: 200
```json
{
    "user": {
        // User object with details
    },
    "token": "string",
    "message": "Logged in successfully"
}
```

**Error Responses:**
- Status Code: 401 (Unauthorized)
```json
{
    "message": "Invalid username or password"
}
```

- Status Code: 403 (Forbidden)
```json
{
    "message": "Account is inactive"
}
```

- Status Code: 422 (Validation Error)
```json
{
    "message": "The user name field is required.",
    "errors": {
        "user_name": [
            "The user name field is required."
        ]
    }
}
```

### 2. Logout
Logout the authenticated user and invalidate their token.

**Endpoint:** `POST /logout`

**Headers:**
```http
Accept: application/json
Authorization: Bearer {token}
```

**Success Response:**
- Status Code: 200
```json
{
    "message": "Logged out successfully"
}
```

**Error Response:**
- Status Code: 401 (Unauthorized)
```json
{
    "message": "Unauthenticated"
}
```

## Authentication
The API uses Bearer token authentication. After successful login, include the token in all subsequent requests:

```http
Authorization: Bearer your_token_here
```

## Security
- All endpoints are protected with CSRF protection
- Passwords are hashed using Laravel's Hash facade
- Tokens are managed using Laravel Sanctum
- Failed login attempts return generic messages to prevent user enumeration
