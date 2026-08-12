# Milk Collection REST API Documentation

This document describes the Milk Collection REST API module for the Milk Center Management System, designed for mobile application integration (Flutter Android/iOS).

---

## Base URL

```
http://localhost:8000/api
```

---

## Authentication & Authorization

All Milk Collection API endpoints require Sanctum Bearer authentication and role-based authorization matching the existing web `MilkCollectionPolicy`.

- **Authentication:** `Authorization: Bearer <SANCTUM_TOKEN>`
- **Headers:** 
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Role Permissions:**

| Endpoint | Method | `super_admin` | `manager` | `collection_staff` | `center_staff` |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/api/milk-collections` | `GET` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/milk-collections/{collection}` | `GET` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/milk-collections` | `POST` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/milk-collections/{collection}` | `PUT/PATCH` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/milk-collections/{collection}` | `DELETE` | Allowed | Allowed | Allowed | Forbidden (403) |

---

## Business Rules & Calculations

1. **Server-Side Total Amount Calculation:**
   - `amount = milk_quantity * rate`
   - The total amount is automatically calculated on the server side prior to persistence to ensure a single source of truth across Web and Mobile.

2. **Duplicate Collection Prevention:**
   - A unique entry is enforced per `(farmer_id, collection_date, shift)`.
   - Attempting to log a second collection for the same farmer on the same date and shift returns a `422 Unprocessable Entity` validation error.

3. **Relationships:**
   - Each collection is linked to a `Farmer`.
   - The `Village` details are retrieved via the Farmer's relationship and nested inside the API response.

---

## Endpoints

### 1. List Milk Collections

Retrieve a paginated list of milk collection entries with search and filtering.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/milk-collections`
- **Query Parameters:**
  - `search` (optional, string): Filter by farmer name or farmer code.
  - `village_id` (optional, integer): Filter collections by farmer's village ID.
  - `farmer_id` (optional, integer): Filter collections by farmer ID.
  - `date` (optional, string): Filter collections by collection date (`YYYY-MM-DD`).
  - `shift` (optional, string): Filter by shift (`morning` or `evening`).
  - `per_page` (optional, integer): Number of records per page (default: 15).

#### Example Request

```bash
GET /api/milk-collections?date=2026-08-12&shift=morning&per_page=10
Authorization: Bearer 1|sanctum_access_token_here
Accept: application/json
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Milk collections retrieved successfully",
  "data": [
    {
      "id": 1,
      "farmer_id": 1,
      "collection_date": "2026-08-12",
      "shift": "morning",
      "milk_quantity": 10.5,
      "fat": 4.2,
      "snf": 8.5,
      "rate": 45.0,
      "amount": 472.5,
      "notes": "Morning entry",
      "farmer": {
        "id": 1,
        "farmer_code": "FAR-001",
        "name": "John Doe",
        "mobile": "9876543210",
        "village": {
          "id": 1,
          "name": "Green Valley",
          "code": "GV-01"
        }
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
    "first": "http://localhost:8000/api/milk-collections?page=1",
    "last": "http://localhost:8000/api/milk-collections?page=1",
    "prev": null,
    "next": null
  }
}
```

---

### 2. Create Milk Collection

Log a new milk collection entry.

- **HTTP Method:** `POST`
- **Endpoint:** `/api/milk-collections`
- **Allowed Roles:** `super_admin`, `manager`, `collection_staff`

#### Request Body

```json
{
  "farmer_id": 1,
  "collection_date": "2026-08-12",
  "shift": "morning",
  "milk_quantity": 20.0,
  "fat": 4.0,
  "snf": 8.5,
  "rate": 40.0,
  "notes": "Morning collection"
}
```

| Field | Type | Required | Rules |
| :--- | :--- | :--- | :--- |
| `farmer_id` | integer | Yes | Must exist in `farmers` table and be active |
| `collection_date` | string (date) | Yes | Valid date (`YYYY-MM-DD`) |
| `shift` | string | Yes | `morning` or `evening` |
| `milk_quantity` | numeric | Yes | Greater than zero (`gt:0`) |
| `fat` | numeric | No | Min: 0, Max: 100 |
| `snf` | numeric | No | Min: 0, Max: 100 |
| `rate` | numeric | Yes | Min: 0 |
| `notes` | string | No | Max 1000 characters |

#### Response (201 Created - Success)

```json
{
  "success": true,
  "message": "Milk collection created successfully",
  "data": {
    "id": 2,
    "farmer_id": 1,
    "collection_date": "2026-08-12",
    "shift": "morning",
    "milk_quantity": 20.0,
    "fat": 4.0,
    "snf": 8.5,
    "rate": 40.0,
    "amount": 800.0,
    "notes": "Morning collection",
    "farmer": {
      "id": 1,
      "farmer_code": "FAR-001",
      "name": "John Doe"
    },
    "created_at": "2026-08-12T17:34:00.000000Z",
    "updated_at": "2026-08-12T17:34:00.000000Z"
  }
}
```

#### Response (422 Unprocessable Entity - Duplicate Entry)

```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "farmer_id": [
      "A milk collection entry already exists for this farmer on the selected date and shift."
    ]
  }
}
```

---

### 3. Get Single Milk Collection Details

Retrieve full details for a specific collection entry by ID.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/milk-collections/{milk_collection}`
- **Allowed Roles:** `super_admin`, `manager`, `collection_staff`

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Milk collection retrieved successfully",
  "data": {
    "id": 1,
    "farmer_id": 1,
    "collection_date": "2026-08-12",
    "shift": "morning",
    "milk_quantity": 10.5,
    "fat": 4.2,
    "snf": 8.5,
    "rate": 45.0,
    "amount": 472.5,
    "farmer": {
      "id": 1,
      "name": "John Doe",
      "village": {
        "id": 1,
        "name": "Green Valley"
      }
    }
  }
}
```

---

### 4. Update Milk Collection

Update an existing collection entry.

- **HTTP Method:** `PUT` or `PATCH`
- **Endpoint:** `/api/milk-collections/{milk_collection}`
- **Allowed Roles:** `super_admin`, `manager`, `collection_staff`

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Milk collection updated successfully",
  "data": {
    "id": 1,
    "milk_quantity": 15.0,
    "rate": 45.0,
    "amount": 675.0
  }
}
```

---

### 5. Delete Milk Collection

Remove a milk collection entry from storage.

- **HTTP Method:** `DELETE`
- **Endpoint:** `/api/milk-collections/{milk_collection}`
- **Allowed Roles:** `super_admin`, `manager`, `collection_staff`

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Milk collection deleted successfully",
  "data": null
}
```

---

## Error Status Codes Summary

- `401 Unauthorized`: Token missing or invalid (`"message": "Unauthenticated."`).
- `403 Forbidden`: User role (`center_staff`) is forbidden per `MilkCollectionPolicy` / middleware.
- `404 Not Found`: Invalid Milk Collection ID.
- `422 Unprocessable Entity`: Validation failure on input parameters or duplicate entry violation.
