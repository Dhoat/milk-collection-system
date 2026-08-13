# Users REST API Documentation

This document describes the Users REST API module for the Milk Center Management System, designed for mobile application integration (Flutter Android/iOS).

---

## Base URL

```
http://localhost:8000/api
```

---

## Authentication & Authorization

All Users API endpoints require Sanctum Bearer authentication and are restricted exclusively to users holding the `super_admin` role, matching the web application's `UserPolicy` and role middleware rules.

- **Authentication:** `Authorization: Bearer <SANCTUM_TOKEN>`
- **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Role Permissions:**

| Endpoint | Method | `super_admin` | `manager` | `center_staff` | `collection_staff` |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/api/users` | `GET` | Allowed | Forbidden (403) | Forbidden (403) | Forbidden (403) |
| `/api/users/{user}` | `GET` | Allowed | Forbidden (403) | Forbidden (403) | Forbidden (403) |
| `/api/users` | `POST` | Allowed | Forbidden (403) | Forbidden (403) | Forbidden (403) |
| `/api/users/{user}` | `PUT` / `PATCH` | Allowed | Forbidden (403) | Forbidden (403) | Forbidden (403) |
| `/api/users/{user}/toggle-status` | `PATCH` | Allowed | Forbidden (403) | Forbidden (403) | Forbidden (403) |
| `/api/users/{user}` | `DELETE` | Allowed | Forbidden (403) | Forbidden (403) | Forbidden (403) |

---

## Valid User Roles

- `super_admin`
- `manager`
- `collection_staff`
- `center_staff`

---

## Security & Business Safeguards

1. **Last Super Admin Protection:** System blocks demoting or deactivating the last active `super_admin` account.
2. **Self-Deactivation & Self-Deletion Protection:** System prevents super admins from deactivating or deleting their own logged-in account.
3. **Attribute Protection:** Passwords, tokens, and internal secrets are strictly hidden from responses.

---

## Endpoints

### 1. List Users

Retrieve a paginated list of system users.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/users`
- **Allowed Roles:** `super_admin`
- **Query Parameters:**
  - `page` (optional, integer): Page number for pagination (15 per page).
  - `search` (optional, string): Search term matching user `name` or `email`.
  - `role` (optional, string): Filter by role (`super_admin`, `manager`, `collection_staff`, `center_staff`).
  - `status` (optional, boolean/string): Filter by active status (`1`/`true` or `0`/`false`).

#### Example Request

```bash
GET /api/users?search=Manager&role=manager&status=1
Authorization: Bearer 1|sanctum_access_token_here
Accept: application/json
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Users retrieved successfully",
  "data": [
    {
      "id": 2,
      "name": "Dairy Manager",
      "email": "manager@dairy.com",
      "role": "manager",
      "status": true,
      "email_verified_at": "2026-08-01T10:00:00.000000Z",
      "created_at": "2026-08-01T08:30:00.000000Z",
      "updated_at": "2026-08-13T09:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

---

### 2. View Single User

Retrieve details of a single user account.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/users/{id}`
- **Allowed Roles:** `super_admin`

#### Example Request

```bash
GET /api/users/2
Authorization: Bearer 1|sanctum_access_token_here
Accept: application/json
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "User retrieved successfully",
  "data": {
    "id": 2,
    "name": "Dairy Manager",
    "email": "manager@dairy.com",
    "role": "manager",
    "status": true,
    "email_verified_at": "2026-08-01T10:00:00.000000Z",
    "created_at": "2026-08-01T08:30:00.000000Z",
    "updated_at": "2026-08-13T09:00:00.000000Z"
  }
}
```

---

### 3. Create User Account

Register a new user account in the system.

- **HTTP Method:** `POST`
- **Endpoint:** `/api/users`
- **Allowed Roles:** `super_admin`

#### Request Body Parameters

| Parameter | Type | Required | Description | Rules |
| :--- | :--- | :--- | :--- | :--- |
| `name` | string | Yes | User full name | `required`, `string`, `max:255` |
| `email` | string | Yes | Unique email address | `required`, `string`, `email`, `max:255`, `unique:users` |
| `password` | string | Yes | Account password | `required`, `string`, `min:8`, `confirmed` |
| `password_confirmation` | string | Yes | Password confirmation | `required` |
| `role` | string | Yes | Assigned system role | `required`, `in:super_admin,manager,collection_staff,center_staff` |
| `status` | boolean | Yes | Account active status | `required`, `boolean` |

#### Example Request

```bash
POST /api/users
Authorization: Bearer 1|sanctum_access_token_here
Content-Type: application/json
Accept: application/json

{
  "name": "New Supervisor",
  "email": "supervisor@dairy.com",
  "password": "securepassword123",
  "password_confirmation": "securepassword123",
  "role": "center_staff",
  "status": true
}
```

#### Response (201 Created - Success)

```json
{
  "success": true,
  "message": "User account created successfully",
  "data": {
    "id": 5,
    "name": "New Supervisor",
    "email": "supervisor@dairy.com",
    "role": "center_staff",
    "status": true,
    "email_verified_at": null,
    "created_at": "2026-08-13T09:55:00.000000Z",
    "updated_at": "2026-08-13T09:55:00.000000Z"
  }
}
```

---

### 4. Update User Account

Update details of an existing user account.

- **HTTP Method:** `PUT` or `PATCH`
- **Endpoint:** `/api/users/{id}`
- **Allowed Roles:** `super_admin`

#### Request Body Parameters

| Parameter | Type | Required | Description | Rules |
| :--- | :--- | :--- | :--- | :--- |
| `name` | string | Yes | User full name | `required`, `string`, `max:255` |
| `email` | string | Yes | Unique email address | `required`, `string`, `email`, `max:255`, `unique:users,email,{id}` |
| `password` | string | No | Optional new password | `nullable`, `string`, `min:8`, `confirmed` |
| `role` | string | Yes | Assigned system role | `required`, `in:super_admin,manager,collection_staff,center_staff` |
| `status` | boolean | Yes | Account active status | `required`, `boolean` |

#### Example Request

```bash
PUT /api/users/5
Authorization: Bearer 1|sanctum_access_token_here
Content-Type: application/json
Accept: application/json

{
  "name": "Senior Supervisor",
  "email": "supervisor@dairy.com",
  "role": "manager",
  "status": true
}
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "User account updated successfully",
  "data": {
    "id": 5,
    "name": "Senior Supervisor",
    "email": "supervisor@dairy.com",
    "role": "manager",
    "status": true,
    "email_verified_at": null,
    "created_at": "2026-08-13T09:55:00.000000Z",
    "updated_at": "2026-08-13T09:56:00.000000Z"
  }
}
```

---

### 5. Toggle User Active Status

Quickly activate or deactivate a user account.

- **HTTP Method:** `PATCH`
- **Endpoint:** `/api/users/{id}/toggle-status`
- **Allowed Roles:** `super_admin`

#### Example Request

```bash
PATCH /api/users/5/toggle-status
Authorization: Bearer 1|sanctum_access_token_here
Accept: application/json
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "User account deactivated successfully",
  "data": {
    "id": 5,
    "name": "Senior Supervisor",
    "email": "supervisor@dairy.com",
    "role": "manager",
    "status": false,
    "email_verified_at": null,
    "created_at": "2026-08-13T09:55:00.000000Z",
    "updated_at": "2026-08-13T09:57:00.000000Z"
  }
}
```

---

### 6. Delete User Account

Permanently remove a user account from the system.

- **HTTP Method:** `DELETE`
- **Endpoint:** `/api/users/{id}`
- **Allowed Roles:** `super_admin`

#### Example Request

```bash
DELETE /api/users/5
Authorization: Bearer 1|sanctum_access_token_here
Accept: application/json
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "User account deleted successfully"
}
```

---

## Error Status Codes Summary

- `401 Unauthorized`: Missing or invalid Bearer token.
- `403 Forbidden`: Authenticated user role is not `super_admin`.
- `404 Not Found`: User ID does not exist.
- `422 Unprocessable Entity`: Validation failure or business constraint violation (e.g. attempting to deactivate/delete last active super admin or self).
