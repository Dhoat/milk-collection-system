# Profile REST API Documentation

This document describes the Profile REST API module for the Milk Center Management System, designed for mobile application integration (Flutter Android/iOS).

---

## Base URL

```
http://localhost:8000/api
```

---

## Authentication & Authorization

All Profile API endpoints require Sanctum Bearer authentication. Profile operations are self-service, allowing authenticated users to manage exclusively their own account information.

- **Authentication:** `Authorization: Bearer <SANCTUM_TOKEN>`
- **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Role Permissions:**

| Endpoint | Method | `super_admin` | `manager` | `center_staff` | `collection_staff` |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/api/profile` | `GET` | Allowed (Self) | Allowed (Self) | Allowed (Self) | Allowed (Self) |
| `/api/profile` | `PUT` / `PATCH` | Allowed (Self) | Allowed (Self) | Allowed (Self) | Allowed (Self) |
| `/api/profile/password` | `PATCH` | Allowed (Self) | Allowed (Self) | Allowed (Self) | Allowed (Self) |

---

## Security Guarantees

1. **Self-Only Access:** The API targets `$request->user()` directly. A user cannot view or alter another user's profile information.
2. **Immutability of Role & Status:** `role` and `status` fields cannot be altered via Profile API requests.
3. **Sensitive Attribute Protection:** `password`, `remember_token`, and token hashes are strictly excluded from API responses.

---

## Endpoints

### 1. Get Authenticated Profile

Retrieve the currently authenticated user's profile details.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/profile`
- **Allowed Roles:** All authenticated roles (`super_admin`, `manager`, `center_staff`, `collection_staff`)

#### Example Request

```bash
GET /api/profile
Authorization: Bearer 1|sanctum_access_token_here
Accept: application/json
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Profile retrieved successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "Gurmeet Singh",
      "email": "gurmeet@milkcenter.com",
      "role": "manager",
      "status": true,
      "email_verified_at": "2026-08-01T10:00:00.000000Z",
      "created_at": "2026-08-01T08:30:00.000000Z",
      "updated_at": "2026-08-13T09:00:00.000000Z"
    }
  }
}
```

---

### 2. Update Profile Information

Update the authenticated user's name and email address.

- **HTTP Method:** `PUT` or `PATCH`
- **Endpoint:** `/api/profile`
- **Allowed Roles:** All authenticated roles (`super_admin`, `manager`, `center_staff`, `collection_staff`)

#### Request Body Parameters

| Parameter | Type | Required | Description | Rules |
| :--- | :--- | :--- | :--- | :--- |
| `name` | string | Yes | Full name of the user | `required`, `string`, `max:255` |
| `email` | string | Yes | Unique email address | `required`, `string`, `lowercase`, `email`, `max:255`, `unique:users` |

#### Example Request

```bash
PUT /api/profile
Authorization: Bearer 1|sanctum_access_token_here
Content-Type: application/json
Accept: application/json

{
  "name": "Gurmeet Singh - Manager",
  "email": "gurmeet.updated@milkcenter.com"
}
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "Gurmeet Singh - Manager",
      "email": "gurmeet.updated@milkcenter.com",
      "role": "manager",
      "status": true,
      "email_verified_at": null,
      "created_at": "2026-08-01T08:30:00.000000Z",
      "updated_at": "2026-08-13T09:50:00.000000Z"
    }
  }
}
```

---

### 3. Update Profile Password

Update the authenticated user's account password.

- **HTTP Method:** `PATCH`
- **Endpoint:** `/api/profile/password`
- **Allowed Roles:** All authenticated roles (`super_admin`, `manager`, `center_staff`, `collection_staff`)

#### Request Body Parameters

| Parameter | Type | Required | Description | Rules |
| :--- | :--- | :--- | :--- | :--- |
| `current_password` | string | Yes | Existing password | `required`, `current_password` |
| `password` | string | Yes | New password | `required`, `min:8`, `confirmed` |
| `password_confirmation` | string | Yes | Confirmation of new password | `required` |

#### Example Request

```bash
PATCH /api/profile/password
Authorization: Bearer 1|sanctum_access_token_here
Content-Type: application/json
Accept: application/json

{
  "current_password": "old_secret_password",
  "password": "new_secure_password_123",
  "password_confirmation": "new_secure_password_123"
}
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Password updated successfully"
}
```

---

## Error Status Codes Summary

- `401 Unauthorized`: Missing or invalid Bearer token.
- `422 Unprocessable Entity`: Validation failure (e.g. incorrect current password, mismatched password confirmation, non-unique email).
