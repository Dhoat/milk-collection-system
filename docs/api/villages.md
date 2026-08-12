# Village REST API Documentation

This document describes the Village REST API module for the Milk Center Management System, designed for mobile application integration (Flutter Android/iOS).

---

## Base URL

```
http://localhost:8000/api
```

---

## Authentication & Authorization

All Village API endpoints require Sanctum Bearer authentication and role-based authorization.

- **Authentication:** `Authorization: Bearer <SANCTUM_TOKEN>`
- **Headers:** 
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Role Permissions:**

| Endpoint | Method | `super_admin` | `manager` | `collection_staff` | `center_staff` |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/api/villages` | `GET` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/villages/{village}` | `GET` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/villages` | `POST` | Allowed | Allowed | Forbidden (403) | Forbidden (403) |
| `/api/villages/{village}` | `PUT/PATCH` | Allowed | Allowed | Forbidden (403) | Forbidden (403) |
| `/api/villages/{village}` | `DELETE` | Allowed | Allowed | Forbidden (403) | Forbidden (403) |

---

## Endpoints

### 1. List Villages

Retrieve a paginated list of villages with search and status filtering.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/villages`
- **Query Parameters:**
  - `search` (optional, string): Filter by village name or code.
  - `status` (optional, boolean): Filter by active status (`true` / `false`).
  - `per_page` (optional, integer): Number of records per page (default: 15).

#### Example Request

```bash
GET /api/villages?search=Green&status=1&per_page=10
Authorization: Bearer 1|sanctum_access_token_here
Accept: application/json
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Villages retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Green Valley",
      "code": "GV-01",
      "address": "Main Road, Sector 4",
      "status": true,
      "farmers_count": 12,
      "created_at": "2026-08-12T12:00:00.000000Z",
      "updated_at": "2026-08-12T12:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 1
  },
  "links": {
    "first": "http://localhost:8000/api/villages?page=1",
    "last": "http://localhost:8000/api/villages?page=1",
    "prev": null,
    "next": null
  }
}
```

---

### 2. Create Village

Store a newly registered village.

- **HTTP Method:** `POST`
- **Endpoint:** `/api/villages`
- **Allowed Roles:** `super_admin`, `manager`

#### Request Body

```json
{
  "name": "Sunshine Village",
  "code": "SUN-01",
  "address": "123 Dairy Road",
  "status": true
}
```

| Field | Type | Required | Rules |
| :--- | :--- | :--- | :--- |
| `name` | string | Yes | Max 255 characters |
| `code` | string | Yes | Max 50 characters, Must be unique in `villages` table |
| `address` | string | No | Max 1000 characters |
| `status` | boolean | No | Default: `true` |

#### Response (201 Created - Success)

```json
{
  "success": true,
  "message": "Village created successfully",
  "data": {
    "id": 2,
    "name": "Sunshine Village",
    "code": "SUN-01",
    "address": "123 Dairy Road",
    "status": true,
    "farmers_count": 0,
    "created_at": "2026-08-12T17:25:00.000000Z",
    "updated_at": "2026-08-12T17:25:00.000000Z"
  }
}
```

#### Response (422 Unprocessable Entity - Validation Error)

```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "code": [
      "The village code has already been taken."
    ]
  }
}
```

---

### 3. Get Single Village Details

Retrieve full details for a specific village by ID.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/villages/{village}`
- **Allowed Roles:** `super_admin`, `manager`, `collection_staff`

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Village retrieved successfully",
  "data": {
    "id": 1,
    "name": "Green Valley",
    "code": "GV-01",
    "address": "Main Road, Sector 4",
    "status": true,
    "farmers_count": 12,
    "created_at": "2026-08-12T12:00:00.000000Z",
    "updated_at": "2026-08-12T12:00:00.000000Z"
  }
}
```

#### Response (404 Not Found)

```json
{
  "success": false,
  "message": "The requested resource was not found.",
  "errors": {}
}
```

---

### 4. Update Village

Update an existing village record.

- **HTTP Method:** `PUT` or `PATCH`
- **Endpoint:** `/api/villages/{village}`
- **Allowed Roles:** `super_admin`, `manager`

#### Request Body

```json
{
  "name": "Green Valley Central",
  "code": "GV-01",
  "address": "Updated Address",
  "status": true
}
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Village updated successfully",
  "data": {
    "id": 1,
    "name": "Green Valley Central",
    "code": "GV-01",
    "address": "Updated Address",
    "status": true,
    "farmers_count": 12,
    "created_at": "2026-08-12T12:00:00.000000Z",
    "updated_at": "2026-08-12T17:25:00.000000Z"
  }
}
```

---

### 5. Delete Village

Remove a village record from storage.

- **HTTP Method:** `DELETE`
- **Endpoint:** `/api/villages/{village}`
- **Allowed Roles:** `super_admin`, `manager`

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Village deleted successfully",
  "data": null
}
```

---

## Error Status Codes Summary

- `401 Unauthorized`: Token missing, invalid, or expired (`"message": "Unauthenticated."`).
- `403 Forbidden`: User role lacks required authorization per `VillagePolicy`.
- `404 Not Found`: Invalid Village ID.
- `422 Unprocessable Entity`: Validation failure on request parameters.
