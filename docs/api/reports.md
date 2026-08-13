# Reports REST API Documentation

This document describes the Management & Executive Reports REST API module for the Milk Center Management System, designed for mobile application integration (Flutter Android/iOS).

---

## Base URL

```
http://localhost:8000/api
```

---

## Authentication & Authorization

All Reports API endpoints require Sanctum Bearer authentication and strict role-based authorization matching the existing web `ReportController` and `view-reports` Gate.

- **Authentication:** `Authorization: Bearer <SANCTUM_TOKEN>`
- **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Role Permissions:**

| Endpoint | Method | `super_admin` | `manager` | `center_staff` | `collection_staff` |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/api/reports/daily` | `GET` | Allowed | Allowed | Forbidden (403) | Forbidden (403) |
| `/api/reports/monthly` | `GET` | Allowed | Allowed | Forbidden (403) | Forbidden (403) |

---

## Calculation Source of Truth

The Reports API exposes calculation metrics powered directly by `App\Services\ReportService`. The API and Web application share identical database queries, SQL aggregations, date logic, and business formulas.

- **Read-Only Safety:** Reports API endpoints perform **GET** operations only and do not mutate any database records.

---

## Endpoints

### 1. Daily Executive Report

Retrieve aggregated daily metrics across milk collection, receiving, stock, shop orders, deliveries, product sales, and net financials for a target date.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/reports/daily`
- **Allowed Roles:** `super_admin`, `manager`
- **Query Parameters:**
  - `date` (optional, string YYYY-MM-DD): Target report date (defaults to current date `today()`).
  - `village_id` (optional, integer): Filter collections and receivings by village ID.
  - `shop_id` (optional, integer): Filter orders, deliveries, and sales by shop ID.
  - `product_id` (optional, integer): Filter product breakdown by product ID.

#### Example Request

```bash
GET /api/reports/daily?date=2026-08-11&village_id=1
Authorization: Bearer 1|sanctum_access_token_here
Accept: application/json
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Daily report retrieved successfully",
  "data": {
    "date": "2026-08-11",
    "collection": {
      "farmers_count": 12,
      "total_litres": 450.5,
      "avg_per_farmer": 37.54,
      "total_amount": 18020.0,
      "avg_fat": 4.5,
      "avg_snf": 8.5,
      "village_breakdown": [
        {
          "village_id": 1,
          "village_name": "Green Valley Village",
          "total_litres": "450.50",
          "total_amount": "18020.00",
          "farmers_count": 12
        }
      ]
    },
    "center": {
      "total_received": 448.0,
      "records_count": 1,
      "diff_litres": -2.5
    },
    "stock": {
      "opening": 120.0,
      "in": 448.0,
      "out": 300.0,
      "closing": 268.0
    },
    "orders": {
      "total_count": 5,
      "total_value": 15000.0,
      "by_status": {
        "pending": 1,
        "confirmed": 1,
        "preparing": 0,
        "dispatched": 1,
        "delivered": 2,
        "cancelled": 0
      }
    },
    "deliveries": {
      "total_count": 4,
      "by_status": {
        "pending": 0,
        "assigned": 1,
        "out_for_delivery": 1,
        "delivered": 2,
        "failed": 0,
        "cancelled": 0
      }
    },
    "products": {
      "total_units": 150.0,
      "items": [
        {
          "product_id": 1,
          "product_name": "Fresh Packaged Milk",
          "unit": "Litre",
          "total_qty": "100.00",
          "total_sales": "6000.00"
        }
      ]
    },
    "financial": {
      "total_sales": 15000.0,
      "milk_expense": 18020.0,
      "net_balance": -3020.0
    }
  }
}
```

---

### 2. Monthly Executive Report

Retrieve aggregated monthly metrics across a target month and year.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/reports/monthly`
- **Allowed Roles:** `super_admin`, `manager`
- **Query Parameters:**
  - `month` (optional, integer 1-12): Target report month (defaults to current month).
  - `year` (optional, integer 2000-2099): Target report year (defaults to current year).
  - `village_id` (optional, integer): Filter collections and receivings by village ID.
  - `shop_id` (optional, integer): Filter orders, deliveries, and sales by shop ID.
  - `product_id` (optional, integer): Filter product breakdown by product ID.

#### Example Request

```bash
GET /api/reports/monthly?month=8&year=2026
Authorization: Bearer 1|sanctum_access_token_here
Accept: application/json
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Monthly report retrieved successfully",
  "data": {
    "month": 8,
    "year": 2026,
    "start_date": "2026-08-01",
    "end_date": "2026-08-31",
    "days_in_month": 31,
    "collection": {
      "total_litres": 12500.0,
      "farmers_count": 45,
      "avg_daily": 403.23,
      "total_amount": 500000.0,
      "avg_fat": 4.5,
      "avg_snf": 8.5,
      "village_breakdown": [...]
    },
    "center": {
      "total_received": 12450.0,
      "avg_daily": 401.61,
      "opening_stock": 200.0,
      "stock_in": 12450.0,
      "stock_out": 11000.0,
      "closing_stock": 1650.0
    },
    "orders": {
      "total_count": 80,
      "total_value": 650000.0,
      "by_status": {
        "delivered": 75,
        "pending": 2,
        "cancelled": 3,
        "confirmed": 0
      },
      "top_shops": [...]
    },
    "products": {
      "items": [...],
      "total_units": 4500.0
    },
    "deliveries": {
      "total_count": 78,
      "by_status": {
        "delivered": 75,
        "pending": 1,
        "failed": 2,
        "cancelled": 0
      }
    },
    "financial": {
      "total_sales": 650000.0,
      "milk_expense": 500000.0,
      "net_balance": 150000.0
    }
  }
}
```

---

## Empty Report Behavior

Periods with no transactions safely return zeroed data and empty breakdowns without throwing errors or null references:

```json
{
  "success": true,
  "message": "Daily report retrieved successfully",
  "data": {
    "date": "2020-01-01",
    "collection": {
      "farmers_count": 0,
      "total_litres": 0,
      "avg_per_farmer": 0,
      "total_amount": 0,
      "avg_fat": 0,
      "avg_snf": 0,
      "village_breakdown": []
    },
    "financial": {
      "total_sales": 0,
      "milk_expense": 0,
      "net_balance": 0
    }
  }
}
```

---

## Error Status Codes Summary

- `401 Unauthorized`: Missing or invalid Bearer token.
- `403 Forbidden`: User role (`center_staff`, `collection_staff`) not authorized under `ReportController` & `view-reports` Gate.
- `422 Unprocessable Entity`: Validation failure on filter parameters (e.g. invalid date, month outside 1-12, non-existent `village_id`).
