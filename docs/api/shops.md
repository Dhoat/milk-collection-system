# Shops REST API Documentation

This document describes the Shops REST API module for the Milk Center Management System, designed for mobile application integration (Flutter Android/iOS).

---

## Base URL

```
http://localhost:8000/api
```

---

## Authentication & Authorization

All Shops API endpoints require Sanctum Bearer authentication and role-based authorization matching the existing web `ShopPolicy`.

- **Authentication:** `Authorization: Bearer <SANCTUM_TOKEN>`
- **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Role Permissions:**

| Endpoint | Method | `super_admin` | `manager` | `center_staff` | `collection_staff` |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/api/shops` | `GET` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/shops/{shop}` | `GET` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/shops` | `POST` | Allowed | Allowed | Forbidden (403) | Forbidden (403) |
| `/api/shops/{shop}` | `PUT/PATCH` | Allowed | Allowed | Forbidden (403) | Forbidden (403) |
| `/api/shops/{shop}/toggle-status` | `PATCH` | Allowed | Allowed | Forbidden (403) | Forbidden (403) |
| `/api/shops/{shop}` | `DELETE` | Allowed | Allowed | Forbidden (403) | Forbidden (403) |

---

## Endpoints

### 1. List Shops

Retrieve a paginated list of registered commercial shops with filtering.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/shops`
- **Allowed Roles:** `super_admin`, `manager`, `center_staff`
- **Query Parameters:**
  - `search` (optional, string): Keyword search matching shop name, shop code, owner name, phone, or area.
  - `status` (optional, boolean/string): Filter by active status (`1` or `0`).
  - `village_id` (optional, integer): Filter by associated village ID.
  - `per_page` (optional, integer): Items per page (default: 15).

#### Example Request

```bash
GET /api/shops?search=Sunrise&status=1
Authorization: Bearer 1|sanctum_access_token_here
Accept: application/json
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Shops retrieved successfully",
  "data": [
    {
      "id": 1,
      "shop_code": "SHP-101",
      "name": "Sunrise Dairy Store",
      "owner_name": "Ramesh Sharma",
      "phone": "9876543210",
      "email": "sunrise@example.com",
      "village_id": 1,
      "area": "Market Square",
      "address": "123 Main Road",
      "status": true,
      "credit_limit": 50000.0,
      "notes": "Daily raw milk purchaser",
      "village": {
        "id": 1,
        "name": "Green Valley",
        "code": "GV-01"
      },
      "created_at": "2026-08-13T10:00:00.000000Z",
      "updated_at": "2026-08-13T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  },
  "links": {
    "first": "http://localhost:8000/api/shops?page=1",
    "last": "http://localhost:8000/api/shops?page=1",
    "prev": null,
    "next": null
  }
}
```

---

### 2. Create Shop

Register a new commercial shop.

- **HTTP Method:** `POST`
- **Endpoint:** `/api/shops`
- **Allowed Roles:** `super_admin`, `manager`

#### Request Body

```json
{
  "shop_code": "SHP-102",
  "name": "Royal Sweets & Dairy",
  "owner_name": "Suresh Patel",
  "phone": "9876543211",
  "email": "royal@example.com",
  "village_id": 1,
  "area": "Station Road",
  "address": "78 Commercial Market",
  "status": true,
  "credit_limit": 25000.0,
  "notes": "Bulk milk buyer"
}
```

| Field | Type | Required | Rules |
| :--- | :--- | :--- | :--- |
| `shop_code` | string | Yes | Unique across all shops, max 50 chars |
| `name` | string | Yes | Max 255 chars |
| `owner_name` | string | Yes | Max 255 chars |
| `phone` | string | Yes | Max 20 chars |
| `email` | string | No | Valid email, max 255 chars |
| `village_id` | integer | No | Must exist in `villages` table if provided |
| `area` | string | No | Max 255 chars |
| `address` | string | No | Text |
| `status` | boolean | Yes | `true` (active) or `false` (inactive) |
| `credit_limit` | numeric | No | Min: 0.00 |
| `notes` | string | No | Text |

#### Response (201 Created - Success)

```json
{
  "success": true,
  "message": "Shop created successfully",
  "data": {
    "id": 2,
    "shop_code": "SHP-102",
    "name": "Royal Sweets & Dairy",
    "owner_name": "Suresh Patel",
    "phone": "9876543211",
    "email": "royal@example.com",
    "village_id": 1,
    "area": "Station Road",
    "address": "78 Commercial Market",
    "status": true,
    "credit_limit": 25000.0,
    "notes": "Bulk milk buyer",
    "village": {
      "id": 1,
      "name": "Green Valley",
      "code": "GV-01"
    },
    "created_at": "2026-08-13T10:05:00.000000Z",
    "updated_at": "2026-08-13T10:05:00.000000Z"
  }
}
```

---

### 3. Get Single Shop Details

Retrieve complete profile details of a specific shop by ID.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/shops/{shop}`
- **Allowed Roles:** `super_admin`, `manager`, `center_staff`

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Shop retrieved successfully",
  "data": {
    "id": 1,
    "shop_code": "SHP-101",
    "name": "Sunrise Dairy Store",
    "owner_name": "Ramesh Sharma",
    "phone": "9876543210",
    "status": true,
    "credit_limit": 50000.0,
    "village": {
      "id": 1,
      "name": "Green Valley"
    }
  }
}
```

---

### 4. Update Shop

Update profile and configuration of an existing shop.

- **HTTP Method:** `PUT` or `PATCH`
- **Endpoint:** `/api/shops/{shop}`
- **Allowed Roles:** `super_admin`, `manager`

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Shop updated successfully",
  "data": {
    "id": 1,
    "shop_code": "SHP-101",
    "name": "Sunrise Dairy & Bakers",
    "owner_name": "Ramesh Sharma",
    "phone": "9876543210",
    "status": true,
    "credit_limit": 60000.0
  }
}
```

---

### 5. Toggle Shop Active Status

Quickly toggle active/inactive status of a shop.

- **HTTP Method:** `PATCH`
- **Endpoint:** `/api/shops/{shop}/toggle-status`
- **Allowed Roles:** `super_admin`, `manager`

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Shop status updated successfully",
  "data": {
    "id": 1,
    "shop_code": "SHP-101",
    "status": false
  }
}
```

---

### 6. Delete Shop

Remove a shop record.

- **HTTP Method:** `DELETE`
- **Endpoint:** `/api/shops/{shop}`
- **Allowed Roles:** `super_admin`, `manager`

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Shop deleted successfully",
  "data": null
}
```

---

## Error Status Codes Summary

- `401 Unauthorized`: Missing or invalid Bearer token.
- `403 Forbidden`: User role (`center_staff` for write ops, `collection_staff` for all ops) not authorized under `ShopPolicy`.
- `404 Not Found`: Invalid shop ID.
- `422 Unprocessable Entity`: Validation failure (e.g. duplicate `shop_code` or missing required fields).
