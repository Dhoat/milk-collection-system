# Farmer REST API Documentation

This document describes the Farmer REST API module for the Milk Center Management System, designed for mobile application integration (Flutter Android/iOS).

---

## Base URL

```
http://localhost:8000/api
```

---

## Authentication & Authorization

All Farmer API endpoints require Sanctum Bearer authentication and role-based authorization matching the existing web `FarmerPolicy`.

- **Authentication:** `Authorization: Bearer <SANCTUM_TOKEN>`
- **Headers:** 
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Role Permissions:**

| Endpoint | Method | `super_admin` | `manager` | `collection_staff` | `center_staff` |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/api/farmers` | `GET` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/farmers/{farmer}` | `GET` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/farmers` | `POST` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/farmers/{farmer}` | `PUT/PATCH` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/farmers/{farmer}` | `DELETE` | Allowed | Allowed | Allowed | Forbidden (403) |

---

## Village Relationship

Each Farmer belongs to a Village via `village_id` foreign key.
When retrieving farmer objects from the API, the related `village` object (`id`, `name`, `code`, `address`, `status`) is nested inside the response data.

---

## Endpoints

### 1. List Farmers

Retrieve a paginated list of registered farmers with search, village, and status filtering.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/farmers`
- **Query Parameters:**
  - `search` (optional, string): Filter by farmer name, farmer code, or mobile number.
  - `village_id` (optional, integer): Filter farmers belonging to a specific village ID.
  - `status` (optional, boolean): Filter by active status (`true` / `false`).
  - `per_page` (optional, integer): Number of records per page (default: 15).

#### Example Request

```bash
GET /api/farmers?search=John&village_id=1&status=1&per_page=10
Authorization: Bearer 1|sanctum_access_token_here
Accept: application/json
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Farmers retrieved successfully",
  "data": [
    {
      "id": 1,
      "village_id": 1,
      "farmer_code": "FAR-001",
      "name": "John Doe",
      "father_name": "Robert Doe",
      "mobile": "9876543210",
      "alternate_mobile": "9876543211",
      "address": "123 Dairy Lane, Sector 1",
      "gender": "male",
      "joining_date": "2026-08-01",
      "bank_name": "State Bank",
      "account_number": "12345678901",
      "ifsc_code": "SBIN0001234",
      "status": true,
      "village": {
        "id": 1,
        "name": "Green Valley",
        "code": "GV-01",
        "address": "Main Road",
        "status": true,
        "created_at": "2026-08-12T12:00:00.000000Z",
        "updated_at": "2026-08-12T12:00:00.000000Z"
      },
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
    "first": "http://localhost:8000/api/farmers?page=1",
    "last": "http://localhost:8000/api/farmers?page=1",
    "prev": null,
    "next": null
  }
}
```

---

### 2. Create Farmer

Register a new farmer in storage.

- **HTTP Method:** `POST`
- **Endpoint:** `/api/farmers`
- **Allowed Roles:** `super_admin`, `manager`, `collection_staff`

#### Request Body

```json
{
  "village_id": 1,
  "farmer_code": "FAR-002",
  "name": "Michael Brown",
  "father_name": "David Brown",
  "mobile": "9876543214",
  "alternate_mobile": null,
  "address": "House 42, Sunrise Village",
  "gender": "male",
  "joining_date": "2026-08-12",
  "bank_name": "National Bank",
  "account_number": "98765432109",
  "ifsc_code": "NBIN0004321",
  "status": true
}
```

| Field | Type | Required | Rules |
| :--- | :--- | :--- | :--- |
| `village_id` | integer | Yes | Must exist in `villages` table |
| `farmer_code` | string | Yes | Unique in `farmers` table |
| `name` | string | Yes | Max 255 characters |
| `father_name` | string | No | Max 255 characters |
| `mobile` | string | Yes | 10-15 digits (`regex:/^[0-9+() -]{10,15}$/`) |
| `alternate_mobile` | string | No | 10-15 digits (`regex:/^[0-9+() -]{10,15}$/`) |
| `address` | string | No | Max 1000 characters |
| `gender` | string | No | `male`, `female`, or `other` |
| `joining_date` | string (date) | No | Valid ISO date format (`YYYY-MM-DD`) |
| `bank_name` | string | No | Max 255 characters |
| `account_number` | string | No | Max 50 characters |
| `ifsc_code` | string | No | Max 20 characters |
| `status` | boolean | No | Default: `true` |

#### Response (201 Created - Success)

```json
{
  "success": true,
  "message": "Farmer created successfully",
  "data": {
    "id": 2,
    "village_id": 1,
    "farmer_code": "FAR-002",
    "name": "Michael Brown",
    "mobile": "9876543214",
    "status": true,
    "village": {
      "id": 1,
      "name": "Green Valley",
      "code": "GV-01"
    },
    "created_at": "2026-08-12T17:30:00.000000Z",
    "updated_at": "2026-08-12T17:30:00.000000Z"
  }
}
```

#### Response (422 Unprocessable Entity - Invalid Village ID)

```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "village_id": [
      "The selected village does not exist."
    ]
  }
}
```

---

### 3. Get Single Farmer Details

Retrieve profile details and village relation for a specific farmer by ID.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/farmers/{farmer}`
- **Allowed Roles:** `super_admin`, `manager`, `collection_staff`

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Farmer retrieved successfully",
  "data": {
    "id": 1,
    "village_id": 1,
    "farmer_code": "FAR-001",
    "name": "John Doe",
    "mobile": "9876543210",
    "village": {
      "id": 1,
      "name": "Green Valley",
      "code": "GV-01"
    }
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

### 4. Update Farmer

Update an existing farmer's profile.

- **HTTP Method:** `PUT` or `PATCH`
- **Endpoint:** `/api/farmers/{farmer}`
- **Allowed Roles:** `super_admin`, `manager`, `collection_staff`

#### Request Body

```json
{
  "village_id": 2,
  "farmer_code": "FAR-001",
  "name": "Johnathan Doe",
  "mobile": "9876543210",
  "status": true
}
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Farmer updated successfully",
  "data": {
    "id": 1,
    "village_id": 2,
    "farmer_code": "FAR-001",
    "name": "Johnathan Doe",
    "mobile": "9876543210",
    "village": {
      "id": 2,
      "name": "Sunrise Village",
      "code": "SUN-01"
    }
  }
}
```

---

### 5. Delete Farmer

Delete a farmer profile.

- **HTTP Method:** `DELETE`
- **Endpoint:** `/api/farmers/{farmer}`
- **Allowed Roles:** `super_admin`, `manager`, `collection_staff`

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Farmer deleted successfully",
  "data": null
}
```

---

## Error Status Codes Summary

- `401 Unauthorized`: Token missing or invalid (`"message": "Unauthenticated."`).
- `403 Forbidden`: User role (`center_staff`) is forbidden per `FarmerPolicy` / middleware.
- `404 Not Found`: Invalid Farmer ID.
- `422 Unprocessable Entity`: Validation failure on input or village relationship.
