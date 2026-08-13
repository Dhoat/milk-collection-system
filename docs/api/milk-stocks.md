# Milk Stock REST API Documentation

This document describes the Milk Stock REST API module for the Milk Center Management System, designed for mobile application integration (Flutter Android/iOS).

---

## Base URL

```
http://localhost:8000/api
```

---

## Authentication & Authorization

All Milk Stock API endpoints require Sanctum Bearer authentication and role-based authorization matching the existing web `MilkStockPolicy`.

- **Authentication:** `Authorization: Bearer <SANCTUM_TOKEN>`
- **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Role Permissions:**

| Endpoint | Method | `super_admin` | `manager` | `center_staff` | `collection_staff` |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/api/milk-stocks` | `GET` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/milk-stocks/{milkStock}` | `GET` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/milk-stocks/out` | `POST` | Allowed | Allowed | Allowed | Forbidden (403) |

---

## Architecture & Single Source of Truth

1. **Shared Stock Ledger:**
   - The API uses the exact same `milk_stocks` table, `MilkStock` model, and `MilkStockService` as the web application.
   - All stock operations run against the same unified database.

2. **Automatic Stock IN Synchronization:**
   - Stock IN records (`type = 'in'`) are created, updated, and deleted automatically via `MilkReceiving` model lifecycle events (`saved`, `deleted`) calling `MilkStockService`.
   - Mobile and Web clients cannot manually create, alter, or delete Stock IN records directly, guaranteeing single source of truth.

3. **Manual Stock OUT Recording:**
   - Authorized users (`super_admin`, `manager`, `center_staff`) can record manual Stock OUT transactions via `POST /api/milk-stocks/out`.
   - Quantity is validated against current available stock (`MilkStock::getAvailableStock()`). Exceeding available stock produces a `422 Unprocessable Entity` error.

---

## Endpoints

### 1. List Milk Stock Transactions & KPI Summary

Retrieve a paginated transaction history ledger along with real-time KPI metrics (`opening_stock`, `today_received`, `today_stock_out`, `available_stock`).

- **HTTP Method:** `GET`
- **Endpoint:** `/api/milk-stocks`
- **Allowed Roles:** `super_admin`, `manager`, `center_staff`
- **Query Parameters:**
  - `date` (optional, string): Filter by transaction date (`YYYY-MM-DD`).
  - `type` (optional, string): Filter by transaction type (`in` or `out`).
  - `search` (optional, string): Search keyword matching `source_or_reason` or `notes`.
  - `per_page` (optional, integer): Items per page (default: 15).

#### Example Request

```bash
GET /api/milk-stocks?date=2026-08-13&type=in
Authorization: Bearer 1|sanctum_access_token_here
Accept: application/json
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Milk stock transactions retrieved successfully",
  "summary": {
    "opening_stock": 100.0,
    "today_received": 500.0,
    "today_stock_out": 150.0,
    "available_stock": 450.0
  },
  "data": [
    {
      "id": 1,
      "transaction_date": "2026-08-13",
      "type": "in",
      "item_type": "raw_milk",
      "quantity": 500.0,
      "fat": 4.5,
      "snf": 8.5,
      "milk_receiving_id": 1,
      "created_by": 2,
      "source_or_reason": "Milk Receiving - Green Valley (Aug 13, 2026 - Morning)",
      "notes": null,
      "milk_receiving": {
        "id": 1,
        "village_id": 1,
        "receiving_date": "2026-08-13",
        "shift": "morning",
        "village": {
          "id": 1,
          "name": "Green Valley",
          "code": "GV-01"
        }
      },
      "creator": {
        "id": 2,
        "name": "Center Staff Member",
        "email": "staff@dairy.com"
      },
      "created_at": "2026-08-13T09:00:00.000000Z",
      "updated_at": "2026-08-13T09:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  },
  "links": {
    "first": "http://localhost:8000/api/milk-stocks?page=1",
    "last": "http://localhost:8000/api/milk-stocks?page=1",
    "prev": null,
    "next": null
  }
}
```

---

### 2. Get Single Milk Stock Record Details

Retrieve details of a specific stock transaction record by ID.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/milk-stocks/{milkStock}`
- **Allowed Roles:** `super_admin`, `manager`, `center_staff`

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Milk stock record retrieved successfully",
  "data": {
    "id": 1,
    "transaction_date": "2026-08-13",
    "type": "in",
    "item_type": "raw_milk",
    "quantity": 500.0,
    "fat": 4.5,
    "snf": 8.5,
    "milk_receiving_id": 1,
    "created_by": 2,
    "source_or_reason": "Milk Receiving - Green Valley (Aug 13, 2026 - Morning)",
    "notes": null,
    "milk_receiving": {
      "id": 1,
      "village_id": 1,
      "receiving_date": "2026-08-13",
      "shift": "morning",
      "village": {
        "id": 1,
        "name": "Green Valley"
      }
    },
    "creator": {
      "id": 2,
      "name": "Center Staff Member"
    }
  }
}
```

---

### 3. Record Stock OUT Transaction

Log a manual stock reduction (e.g. shop dispatch, processing batch).

- **HTTP Method:** `POST`
- **Endpoint:** `/api/milk-stocks/out`
- **Allowed Roles:** `super_admin`, `manager`, `center_staff`

#### Request Body

```json
{
  "transaction_date": "2026-08-13",
  "quantity": 150.0,
  "source_or_reason": "Shop Order #101 Dispatch",
  "fat": 4.0,
  "snf": 8.5,
  "notes": "Dispatched to Central Market Shop"
}
```

| Field | Type | Required | Rules |
| :--- | :--- | :--- | :--- |
| `transaction_date` | string (date) | Yes | Valid date (`YYYY-MM-DD`) |
| `quantity` | numeric | Yes | Must be > 0 and <= available stock |
| `source_or_reason` | string | Yes | Max 255 characters |
| `fat` | numeric | No | Min: 0, Max: 100 |
| `snf` | numeric | No | Min: 0, Max: 100 |
| `notes` | string | No | Max 1000 characters |

#### Response (201 Created - Success)

```json
{
  "success": true,
  "message": "Stock OUT transaction recorded successfully",
  "data": {
    "id": 2,
    "transaction_date": "2026-08-13",
    "type": "out",
    "item_type": "raw_milk",
    "quantity": 150.0,
    "fat": 4.0,
    "snf": 8.5,
    "milk_receiving_id": null,
    "created_by": 2,
    "source_or_reason": "Shop Order #101 Dispatch",
    "notes": "Dispatched to Central Market Shop",
    "creator": {
      "id": 2,
      "name": "Center Staff Member"
    }
  }
}
```

#### Error Response (422 Exceeding Available Stock)

```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "quantity": [
      "Stock OUT quantity (600.00 L) cannot exceed current available stock (450.00 L)."
    ]
  }
}
```

---

## Error Status Codes Summary

- `401 Unauthorized`: Missing or invalid Bearer token.
- `403 Forbidden`: User role (`collection_staff`) not authorized under `MilkStockPolicy`.
- `404 Not Found`: Invalid stock record ID.
- `422 Unprocessable Entity`: Validation failure or stock out quantity exceeds available stock.
