# Shop Orders REST API Documentation

This document describes the Shop Orders REST API module for the Milk Center Management System, designed for mobile application integration (Flutter Android/iOS).

---

## Base URL

```
http://localhost:8000/api
```

---

## Authentication & Authorization

All Shop Orders API endpoints require Sanctum Bearer authentication and role-based authorization matching the existing web `ShopOrderPolicy`.

- **Authentication:** `Authorization: Bearer <SANCTUM_TOKEN>`
- **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Role Permissions:**

| Endpoint | Method | `super_admin` | `manager` | `center_staff` | `collection_staff` |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/api/shop-orders` | `GET` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/shop-orders/{shopOrder}` | `GET` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/shop-orders` | `POST` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/shop-orders/{shopOrder}/status` | `PATCH` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/shop-orders/{shopOrder}` | `PUT/PATCH` | Allowed | Allowed | Allowed | Forbidden (403) |
| `/api/shop-orders/{shopOrder}` | `DELETE` | Allowed | Allowed | Forbidden (403) | Forbidden (403) |

---

## Order Status Workflow & Stock Deduction Rules

Orders move through the following lifecycle statuses:
- `pending`: Order registered; stock is **NOT** deducted.
- `confirmed`, `preparing`, `dispatched`, `delivered`: Stock **IS** deducted idempotently upon entering any of these states (`stock_deducted = true`).
- `cancelled`: If stock was previously deducted, cancelling the order automatically **reverses** stock deduction (`stock_deducted = false`).

### Inventory Integration
- **Dairy Products (e.g. Curd, Ghee, Butter):** Decrements/increments the product's `stock_quantity`.
- **Raw Milk (`category === 'raw_milk'`):** Integrates directly with the `MilkStock` inventory ledger by recording a Stock OUT entry (`MilkStockService::recordStockOut`).

---

## Endpoints

### 1. List Shop Orders

Retrieve a paginated list of shop orders with optional filtering.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/shop-orders`
- **Allowed Roles:** `super_admin`, `manager`, `center_staff`
- **Query Parameters:**
  - `search` (optional, string): Keyword search matching order number, shop name, shop code, or owner name.
  - `status` (optional, string): Filter by order status (`pending`, `confirmed`, `preparing`, `dispatched`, `delivered`, `cancelled`).
  - `shop_id` (optional, integer): Filter by shop ID.
  - `date` (optional, string YYYY-MM-DD): Filter by order date.
  - `per_page` (optional, integer): Items per page (default: 15).

#### Example Request

```bash
GET /api/shop-orders?status=confirmed&date=2026-08-13
Authorization: Bearer 1|sanctum_access_token_here
Accept: application/json
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Shop orders retrieved successfully",
  "data": [
    {
      "id": 1,
      "order_number": "ORD-2026-0001",
      "shop_id": 1,
      "order_date": "2026-08-13",
      "status": "confirmed",
      "subtotal": 1500.0,
      "discount": 50.0,
      "total_amount": 1450.0,
      "stock_deducted": true,
      "created_by": 1,
      "notes": "Express delivery requested",
      "shop": {
        "id": 1,
        "shop_code": "SHP-101",
        "name": "Sunrise Dairy Store",
        "owner_name": "Ramesh Sharma",
        "phone": "9876543210"
      },
      "items": [
        {
          "id": 1,
          "shop_order_id": 1,
          "product_id": 1,
          "product_name": "Fresh Curd",
          "unit": "Kg",
          "quantity": 5.0,
          "unit_price": 100.0,
          "line_total": 500.0,
          "product": {
            "id": 1,
            "product_code": "PRD-CURD",
            "name": "Fresh Curd",
            "category": "dairy_product",
            "unit": "Kg",
            "unit_price": 100.0
          }
        }
      ],
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
    "first": "http://localhost:8000/api/shop-orders?page=1",
    "last": "http://localhost:8000/api/shop-orders?page=1",
    "prev": null,
    "next": null
  }
}
```

---

### 2. Create Shop Order

Place a new commercial shop order with items. Server automatically calculates line totals, subtotal, and final total amount.

- **HTTP Method:** `POST`
- **Endpoint:** `/api/shop-orders`
- **Allowed Roles:** `super_admin`, `manager`, `center_staff`

#### Request Body

```json
{
  "shop_id": 1,
  "order_date": "2026-08-13",
  "status": "pending",
  "discount": 50.0,
  "notes": "Daily morning order",
  "items": [
    {
      "product_id": 1,
      "quantity": 5,
      "unit_price": 100.0
    },
    {
      "product_id": 2,
      "quantity": 2,
      "unit_price": 500.0
    }
  ]
}
```

| Field | Type | Required | Rules |
| :--- | :--- | :--- | :--- |
| `shop_id` | integer | Yes | Must exist in `shops` table |
| `order_date` | string | Yes | Valid date format (`YYYY-MM-DD`) |
| `status` | string | Yes | `pending`, `confirmed`, `preparing`, `dispatched`, `delivered`, or `cancelled` |
| `discount` | numeric | No | Min: 0.00 |
| `notes` | string | No | Text |
| `items` | array | Yes | Min: 1 item |
| `items.*.product_id` | integer | Yes | Must exist in `products` table |
| `items.*.quantity` | numeric | Yes | Greater than 0 (`gt:0`) |
| `items.*.unit_price` | numeric | No | Uses product default unit price if omitted |

#### Response (201 Created - Success)

```json
{
  "success": true,
  "message": "Shop order created successfully",
  "data": {
    "id": 1,
    "order_number": "ORD-2026-0001",
    "shop_id": 1,
    "order_date": "2026-08-13",
    "status": "pending",
    "subtotal": 1500.0,
    "discount": 50.0,
    "total_amount": 1450.0,
    "stock_deducted": false,
    "created_by": 1,
    "notes": "Daily morning order",
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "product_name": "Fresh Curd",
        "unit": "Kg",
        "quantity": 5.0,
        "unit_price": 100.0,
        "line_total": 500.0
      }
    ]
  }
}
```

---

### 3. Get Single Shop Order Details

Retrieve complete order details including items and shop by ID.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/shop-orders/{shopOrder}`
- **Allowed Roles:** `super_admin`, `manager`, `center_staff`

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Shop order retrieved successfully",
  "data": {
    "id": 1,
    "order_number": "ORD-2026-0001",
    "shop_id": 1,
    "status": "confirmed",
    "total_amount": 1450.0,
    "stock_deducted": true,
    "shop": {
      "id": 1,
      "name": "Sunrise Dairy Store"
    },
    "items": [...]
  }
}
```

---

### 4. Update Order Status

Transition order status. Deducts or reverses stock automatically as dictated by status workflow.

- **HTTP Method:** `PATCH` or `PUT`
- **Endpoint:** `/api/shop-orders/{shopOrder}/status` or `/api/shop-orders/{shopOrder}`
- **Allowed Roles:** `super_admin`, `manager`, `center_staff`

#### Request Body

```json
{
  "status": "confirmed"
}
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Shop order status updated successfully",
  "data": {
    "id": 1,
    "order_number": "ORD-2026-0001",
    "status": "confirmed",
    "stock_deducted": true
  }
}
```

---

### 5. Delete Shop Order

Remove an order record. Automatically reverses any stock deduction prior to deletion.

- **HTTP Method:** `DELETE`
- **Endpoint:** `/api/shop-orders/{shopOrder}`
- **Allowed Roles:** `super_admin`, `manager` (`center_staff` receives 403 Forbidden)

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Shop order deleted successfully",
  "data": null
}
```

---

## Error Status Codes Summary

- `401 Unauthorized`: Missing or invalid Bearer token.
- `403 Forbidden`: User role (`center_staff` for deletion, `collection_staff` for all ops) not authorized under `ShopOrderPolicy`.
- `404 Not Found`: Invalid shop order ID.
- `422 Unprocessable Entity`: Validation failure (e.g. missing items, invalid shop) OR business failure (e.g. requested item quantity exceeds available stock).
