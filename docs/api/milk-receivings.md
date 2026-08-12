# Milk Receiving REST API Documentation

This document describes the Milk Receiving REST API module for the Milk Center Management System, designed for mobile application integration (Flutter Android/iOS).

---

## Base URL

```
http://localhost:8000/api
```

---

## Authentication & Authorization

All Milk Receiving API endpoints require Sanctum Bearer authentication and role-based authorization matching the existing web `MilkReceivingPolicy`.

- **Authentication:** `Authorization: Bearer <SANCTUM_TOKEN>`
- **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Role Permissions:**

| Endpoint | Method | `super_admin` | `manager` | `center_staff` | `collection_staff` |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/api/milk-receivings` | `GET` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/milk-receivings/{receiving}` | `GET` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/milk-receivings` | `POST` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/milk-receivings/{receiving}` | `PUT/PATCH` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/milk-receivings/{receiving}` | `DELETE` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/milk-receivings/summary` | `GET` | Allowed | Allowed | Allowed | Forbidden (403) |

---

## Automated Business Rules & Calculations

1. **Expected Metrics Calculation:**
   - Aggregated from farmer collections for the selected `village_id`, `receiving_date`, and `shift`.
   - `expected_quantity` = sum of `milk_quantity` from farmer collections.
   - `expected_fat` = weighted average: `sum(milk_quantity * fat) / expected_quantity`.
   - `expected_snf` = weighted average: `sum(milk_quantity * snf) / expected_quantity`.

2. **Automatic Status Assessment:**
   - If `abs(received_quantity - expected_quantity) > 0.1` Liters, `status` is set to `'discrepancy'`.
   - Otherwise, `status` is set to `'received'`.

3. **Automatic Stock Integration:**
   - Model `saved` and `deleted` events automatically trigger `MilkStockService` to update stock ledgers, maintaining single source of truth across Web and Mobile.

4. **Duplicate Prevention:**
   - Unique entry enforced per `(village_id, receiving_date, shift)`.

---

## Endpoints

### 1. List Milk Receivings

Retrieve a paginated list of milk receiving records with filtering.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/milk-receivings`
- **Query Parameters:**
  - `date` (optional, string): Filter by receiving date (`YYYY-MM-DD`).
  - `village_id` (optional, integer): Filter by village ID.
  - `shift` (optional, string): Filter by shift (`morning` or `evening`).
  - `status` (optional, string): Filter by status (`received` or `discrepancy`).
  - `per_page` (optional, integer): Number of items per page (default: 15).

#### Example Request

```bash
GET /api/milk-receivings?date=2026-08-12&shift=morning
Authorization: Bearer 1|sanctum_access_token_here
Accept: application/json
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Milk receiving records retrieved successfully",
  "data": [
    {
      "id": 1,
      "village_id": 1,
      "receiving_date": "2026-08-12",
      "shift": "morning",
      "expected_quantity": 100.0,
      "received_quantity": 100.0,
      "quantity_variance": 0.0,
      "quantity_variance_percent": 0.0,
      "expected_fat": 4.6,
      "received_fat": 4.6,
      "expected_snf": 8.6,
      "received_snf": 8.6,
      "status": "received",
      "verified_by": 2,
      "notes": null,
      "village": {
        "id": 1,
        "name": "Green Valley",
        "code": "GV-01"
      },
      "verifier": {
        "id": 2,
        "name": "Center Staff Member",
        "email": "staff@dairy.com"
      },
      "created_at": "2026-08-12T12:00:00.000000Z",
      "updated_at": "2026-08-12T12:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  },
  "links": {
    "first": "http://localhost:8000/api/milk-receivings?page=1",
    "last": "http://localhost:8000/api/milk-receivings?page=1",
    "prev": null,
    "next": null
  }
}
```

---

### 2. Create Milk Receiving Record

Log a new milk batch received at the center from a village.

- **HTTP Method:** `POST`
- **Endpoint:** `/api/milk-receivings`
- **Allowed Roles:** `super_admin`, `manager`, `center_staff`

#### Request Body

```json
{
  "village_id": 1,
  "receiving_date": "2026-08-12",
  "shift": "morning",
  "received_quantity": 100.0,
  "received_fat": 4.6,
  "received_snf": 8.6,
  "notes": "Batch received in good condition"
}
```

| Field | Type | Required | Rules |
| :--- | :--- | :--- | :--- |
| `village_id` | integer | Yes | Must exist in `villages` table; unique for date & shift |
| `receiving_date` | string (date) | Yes | Valid date (`YYYY-MM-DD`) |
| `shift` | string | Yes | `morning` or `evening` |
| `received_quantity` | numeric | Yes | Min: 0 |
| `received_fat` | numeric | No | Min: 0, Max: 100 |
| `received_snf` | numeric | No | Min: 0, Max: 100 |
| `notes` | string | No | Max 1000 characters |

#### Response (201 Created - Success)

```json
{
  "success": true,
  "message": "Milk receiving record created successfully",
  "data": {
    "id": 2,
    "village_id": 1,
    "receiving_date": "2026-08-12",
    "shift": "morning",
    "expected_quantity": 100.0,
    "received_quantity": 100.0,
    "quantity_variance": 0.0,
    "quantity_variance_percent": 0.0,
    "expected_fat": 4.6,
    "received_fat": 4.6,
    "expected_snf": 8.6,
    "received_snf": 8.6,
    "status": "received",
    "verified_by": 2,
    "notes": "Batch received in good condition",
    "village": {
      "id": 1,
      "name": "Green Valley"
    },
    "verifier": {
      "id": 2,
      "name": "Center Staff Member"
    }
  }
}
```

---

### 3. Get Collection Summary Metrics

Query expected collection metrics before submitting receiving record.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/milk-receivings/summary`
- **Query Parameters:** `village_id`, `date`, `shift`

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Collection summary retrieved successfully",
  "data": {
    "expected_quantity": 100.0,
    "expected_fat": 4.6,
    "expected_snf": 8.6,
    "farmer_count": 2
  }
}
```

---

### 4. Get Single Milk Receiving Details

Retrieve single receiving record by ID.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/milk-receivings/{milk_receiving}`

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Milk receiving record retrieved successfully",
  "data": {
    "id": 1,
    "village_id": 1,
    "receiving_date": "2026-08-12",
    "shift": "morning",
    "expected_quantity": 100.0,
    "received_quantity": 100.0,
    "status": "received"
  }
}
```

---

### 5. Update Milk Receiving Record

- **HTTP Method:** `PUT` or `PATCH`
- **Endpoint:** `/api/milk-receivings/{milk_receiving}`

---

### 6. Delete Milk Receiving Record

- **HTTP Method:** `DELETE`
- **Endpoint:** `/api/milk-receivings/{milk_receiving}`

---

## Error Status Codes Summary

- `401 Unauthorized`: Missing or invalid Bearer token.
- `403 Forbidden`: User role (`collection_staff`) not authorized under `MilkReceivingPolicy`.
- `404 Not Found`: Invalid receiving record ID.
- `422 Unprocessable Entity`: Validation failure or duplicate entry.
