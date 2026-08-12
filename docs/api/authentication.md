# Authentication REST API Documentation

This document describes the Authentication REST API module for the Milk Center Management System, designed for mobile application integration (Flutter Android/iOS).

---

## Base URL

```
http://localhost:8000/api
```

---

## Endpoints Summary

| Method | Endpoint | Auth Required | Description |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/login` | No | Authenticate user and issue Sanctum Bearer token |
| `GET` | `/api/me` | Yes (`Bearer Token`) | Retrieve current user profile and role details |
| `POST` | `/api/logout` | Yes (`Bearer Token`) | Revoke current access token and log out |

---

## 1. User Login

Authenticates user credentials and returns a Bearer access token for subsequent API requests.

- **HTTP Method:** `POST`
- **Endpoint:** `/api/login`
- **Authentication:** None (Public)
- **Headers:** 
  - `Content-Type: application/json`
  - `Accept: application/json`

### Request Body

```json
{
  "email": "admin@milkcenter.com",
  "password": "password123"
}
```

### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Super Admin",
      "email": "admin@milkcenter.com",
      "role": "super_admin",
      "status": true,
      "email_verified_at": "2026-08-12T12:00:00.000000Z",
      "created_at": "2026-08-12T12:00:00.000000Z",
      "updated_at": "2026-08-12T12:00:00.000000Z"
    },
    "token": "1|sanctum_generated_bearer_token_here",
    "token_type": "Bearer"
  }
}
```

### Response (401 Unauthorized - Invalid Credentials)

```json
{
  "success": false,
  "message": "Invalid credentials",
  "errors": {}
}
```

### Response (403 Forbidden - Inactive Account)

```json
{
  "success": false,
  "message": "Your account is inactive. Please contact system administrator.",
  "errors": {}
}
```

### Response (422 Unprocessable Entity - Validation Error)

```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password field is required."
    ]
  }
}
```

---

## 2. Get User Profile (`/me`)

Retrieves profile details and system role for the authenticated user.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/me`
- **Authentication:** Bearer Token (`auth:sanctum`)
- **Headers:** 
  - `Authorization: Bearer <TOKEN>`
  - `Accept: application/json`

### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "User profile retrieved successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "Super Admin",
      "email": "admin@milkcenter.com",
      "role": "super_admin",
      "status": true,
      "email_verified_at": "2026-08-12T12:00:00.000000Z",
      "created_at": "2026-08-12T12:00:00.000000Z",
      "updated_at": "2026-08-12T12:00:00.000000Z"
    }
  }
}
```

### Response (401 Unauthorized - Unauthenticated / Missing Token)

```json
{
  "success": false,
  "message": "Unauthenticated.",
  "errors": {}
}
```

---

## 3. User Logout

Revokes the active Sanctum Bearer token used for the current session.

- **HTTP Method:** `POST`
- **Endpoint:** `/api/logout`
- **Authentication:** Bearer Token (`auth:sanctum`)
- **Headers:** 
  - `Authorization: Bearer <TOKEN>`
  - `Accept: application/json`

### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Successfully logged out"
}
```

### Response (401 Unauthorized - Missing / Invalid Token)

```json
{
  "success": false,
  "message": "Unauthenticated.",
  "errors": {}
}
```
