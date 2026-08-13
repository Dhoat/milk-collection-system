# Deliveries REST API Documentation

This document describes the Deliveries REST API module for the Milk Center Management System, designed for mobile application integration (Flutter Android/iOS).

---

## Base URL

```
http://localhost:8000/api
```

---

## Authentication & Authorization

All Deliveries API endpoints require Sanctum Bearer authentication and role-based authorization matching the existing web `DeliveryPolicy`.

- **Authentication:** `Authorization: Bearer <SANCTUM_TOKEN>`
- **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Role Permissions:**

| Endpoint | Method | `super_admin` | `manager` | `center_staff` | `collection_staff` |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/api/deliveries` | `GET` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/deliveries/{delivery}` | `GET` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/deliveries` | `POST` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/deliveries/{delivery}/status` | `PATCH` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/deliveries/{delivery}` | `PUT/PATCH` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/deliveries/{delivery}` | `DELETE` | Allowed | Allowed | Forbidden (403) | Forbidden (403) |

---

## Delivery Workflow & Order Synchronization

### Allowed Status Transitions Graph

- `pending` $\rightarrow$ `assigned`, `out_for_delivery`, `cancelled`
- `assigned` $\rightarrow$ `out_for_delivery`, `pending`, `cancelled`
- `out_for_delivery` $\rightarrow$ `delivered`, `failed`, `cancelled`
- `delivered` $\rightarrow$ `[]` (Terminal state)
- `failed` $\rightarrow$ `out_for_delivery`, `cancelled`
- `cancelled` $\rightarrow$ `[]` (Terminal state)

### Linked Shop Order Status Synchronization
- **`out_for_delivery`:** Automatically sets the linked `ShopOrder` status to `dispatched` and sets `dispatched_at` timestamp.
- **`delivered`:** Automatically sets the linked `ShopOrder` status to `delivered` and sets `delivered_at` timestamp.

### Inventory / Stock Impact
- Delivery status progression does **NOT** directly alter stock balances. Stock deduction is executed when the order is confirmed/dispatched via the Shop Order module.

---

## Endpoints

### 1. List Deliveries

Retrieve a paginated list of delivery records with optional filtering.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/deliveries`
- **Allowed Roles:** `super_admin`, `manager`, `center_staff`
- **Query Parameters:**
  - `search` (optional, string): Search keyword matching delivery number, order number, or shop name/code.
  - `status` (optional, string): Filter by delivery status (`pending`, `assigned`, `out_for_delivery`, `delivered`, `failed`, `cancelled`).
  - `shop_id` (optional, integer): Filter by shop ID.
  - `assigned_to` (optional, integer): Filter by assigned driver/staff user ID.
  - `date` (optional, string YYYY-MM-DD): Filter by delivery date.
  - `per_page` (optional, integer): Items per page (default: 15).

#### Example Request

```bash
GET /api/deliveries?status=out_for_delivery&assigned_to=3
Authorization: Bearer 1|sanctum_access_token_here
Accept: application/json
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Deliveries retrieved successfully",
  "data": [
    {
      "id": 1,
      "delivery_number": "DEL-2026-0001",
      "shop_order_id": 1,
      "shop_id": 1,
      "delivery_date": "2026-08-13",
      "status": "out_for_delivery",
      "delivery_address": "123 Market Street",
      "contact_person": "Sunil Kumar",
      "contact_phone": "9876543210",
      "assigned_to": 3,
      "created_by": 1,
      "notes": "Morning dispatch",
      "dispatched_at": "2026-08-13T08:30:00.000000Z",
      "delivered_at": null,
      "shop": {
        "id": 1,
        "shop_code": "SHP-101",
        "name": "Metro Dairy Hub"
      },
      "shop_order": {
        "id": 1,
        "order_number": "ORD-2026-0001",
        "status": "dispatched"
      },
      "assigned_staff": {
        "id": 3,
        "name": "Driver Rahul",
        "email": "rahul@example.com"
      },
      "created_at": "2026-08-13T08:00:00.000000Z",
      "updated_at": "2026-08-13T08:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  },
  "links": {
    "first": "http://localhost:8000/api/deliveries?page=1",
    "last": "http://localhost:8000/api/deliveries?page=1",
    "prev": null,
    "next": null
  }
}
```

---

### 2. Create Delivery

Create a new delivery dispatch record linked to a shop order. If address/contact details are omitted, recipient data is automatically pre-filled from the shop record.

- **HTTP Method:** `POST`
- **Endpoint:** `/api/deliveries`
- **Allowed Roles:** `super_admin`, `manager`, `center_staff`

#### Request Body

```json
{
  "shop_order_id": 1,
  "delivery_date": "2026-08-13",
  "status": "pending",
  "assigned_to": 3,
  "notes": "Handle fragile packaging with care"
}
```

| Field | Type | Required | Rules |
| :--- | :--- | :--- | :--- |
| `shop_order_id` | integer | Yes | Must exist in `shop_orders`. Order cannot already have an active delivery (`pending`, `assigned`, `out_for_delivery`). |
| `delivery_date` | string | Yes | Valid date format (`YYYY-MM-DD`) |
| `status` | string | Yes | `pending`, `assigned`, `out_for_delivery`, `delivered`, `failed`, `cancelled` |
| `assigned_to` | integer | No | Must exist in `users` |
| `delivery_address` | string | No | Max 255 chars (defaults to shop address) |
| `contact_person` | string | No | Max 255 chars (defaults to shop owner) |
| `contact_phone` | string | No | Max 50 chars (defaults to shop phone) |
| `notes` | string | No | Text |

#### Response (201 Created - Success)

```json
{
  "success": true,
  "message": "Delivery created successfully",
  "data": {
    "id": 1,
    "delivery_number": "DEL-2026-0001",
    "shop_order_id": 1,
    "shop_id": 1,
    "delivery_date": "2026-08-13",
    "status": "assigned",
    "delivery_address": "123 Market Street",
    "contact_person": "Sunil Kumar",
    "contact_phone": "9876543210",
    "assigned_to": 3,
    "created_by": 1,
    "notes": "Handle fragile packaging with care"
  }
}
```

---

### 3. Get Single Delivery Details

Retrieve complete delivery details including linked order items, shop, driver, and creator.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/deliveries/{delivery}`
- **Allowed Roles:** `super_admin`, `manager`, `center_staff`

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Delivery retrieved successfully",
  "data": {
    "id": 1,
    "delivery_number": "DEL-2026-0001",
    "status": "out_for_delivery",
    "shop_order": {
      "id": 1,
      "order_number": "ORD-2026-0001",
      "items": [...]
    },
    "shop": {
      "id": 1,
      "name": "Metro Dairy Hub"
    },
    "assigned_staff": {
      "id": 3,
      "name": "Driver Rahul"
    }
  }
}
```

---

### 4. Update Delivery Status & Driver Assignment

Transition delivery status or assign/reassign driver.

- **HTTP Method:** `PATCH` or `PUT`
- **Endpoint:** `/api/deliveries/{delivery}/status` or `/api/deliveries/{delivery}`
- **Allowed Roles:** `super_admin`, `manager`, `center_staff`

#### Request Body

```json
{
  "status": "delivered",
  "assigned_to": 3
}
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Delivery updated successfully",
  "data": {
    "id": 1,
    "delivery_number": "DEL-2026-0001",
    "status": "delivered",
    "delivered_at": "2026-08-13T10:15:00.000000Z",
    "shop_order": {
      "id": 1,
      "status": "delivered"
    }
  }
}
```

---

### 5. Delete Delivery Record

Remove a delivery dispatch record.

- **HTTP Method:** `DELETE`
- **Endpoint:** `/api/deliveries/{delivery}`
- **Allowed Roles:** `super_admin`, `manager` (`center_staff` receives 403 Forbidden)

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Delivery deleted successfully",
  "data": null
}
```

---

## Error Status Codes Summary

- `401 Unauthorized`: Missing or invalid Bearer token.
- `403 Forbidden`: User role (`center_staff` for deletion, `collection_staff` for all ops) not authorized under `DeliveryPolicy`.
- `404 Not Found`: Invalid delivery ID.
- `422 Unprocessable Entity`: Validation failure OR business violation (e.g. creating duplicate active delivery for an order, or invalid status transition).
