# Dashboard REST API Documentation

This document describes the Dashboard REST API module for the Milk Center Management System, designed for mobile application integration (Flutter Android/iOS).

---

## Base URL

```
http://localhost:8000/api
```

---

## Authentication & Authorization

The Dashboard API endpoint requires Sanctum Bearer authentication. Authorization matches the existing web dashboard route (`['auth', 'verified']`).

- **Authentication:** `Authorization: Bearer <SANCTUM_TOKEN>`
- **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Role Permissions:**

| Endpoint | Method | `super_admin` | `manager` | `center_staff` | `collection_staff` |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/api/dashboard` | `GET` | Allowed | Allowed | Allowed | Allowed |

---

## Calculation Source of Truth

The Dashboard API returns identical data and KPIs calculated by `App\Services\DashboardService` (shared with the web application controller).

- **Read-Only Safety:** The Dashboard API is strictly a **GET** operation and does not mutate any database state.

---

## Endpoint Specifications

### Get Admin Dashboard Summary

Retrieve comprehensive dashboard KPIs, shift overview, 7-day milk collection trend, recent collections, village performance, and recent activity log.

- **HTTP Method:** `GET`
- **Endpoint:** `/api/dashboard`
- **Allowed Roles:** All authenticated roles (`super_admin`, `manager`, `center_staff`, `collection_staff`)
- **Query Parameters:**
  - `date` (optional, string YYYY-MM-DD): Target dashboard summary date (defaults to current date `today()`).

#### Example Request

```bash
GET /api/dashboard?date=2026-08-13
Authorization: Bearer 1|sanctum_access_token_here
Accept: application/json
```

#### Response (200 OK - Success)

```json
{
  "success": true,
  "message": "Dashboard data retrieved successfully",
  "data": {
    "today": "2026-08-13",
    "kpis": {
      "total_farmers": 25,
      "active_farmers": 24,
      "total_villages": 4,
      "active_villages": 4,
      "today_quantity": 450.5,
      "today_amount": 18020.0
    },
    "todayOverview": {
      "morning": {
        "quantity": 260.0,
        "farmers": 18
      },
      "evening": {
        "quantity": 190.5,
        "farmers": 15
      },
      "total": {
        "quantity": 450.5,
        "farmers": 24
      }
    },
    "collectionTrend": [
      {
        "date": "2026-08-07",
        "label": "Fri",
        "full_label": "Aug 07",
        "litres": 380.0
      },
      {
        "date": "2026-08-13",
        "label": "Thu",
        "full_label": "Aug 13",
        "litres": 450.5
      }
    ],
    "trendMax": 450.5,
    "recentCollections": [
      {
        "id": 105,
        "farmer_id": 12,
        "collection_date": "2026-08-13",
        "shift": "evening",
        "milk_quantity": "25.00",
        "fat": "4.50",
        "snf": "8.50",
        "amount": "1000.00",
        "farmer": {
          "id": 12,
          "name": "Gurmeet Singh",
          "village": {
            "id": 1,
            "name": "Green Valley Village"
          }
        }
      }
    ],
    "villagePerformance": [
      {
        "id": 1,
        "name": "Green Valley Village",
        "code": "VIL-101",
        "status": 1,
        "farmers_count": 12,
        "today_quantity": "250.50",
        "today_amount": "10020.00"
      }
    ],
    "recentActivity": [
      {
        "type": "collection",
        "message": "Milk collection recorded for Gurmeet Singh",
        "detail": "25.00 L · Evening",
        "timestamp": "2026-08-13T17:30:00.000000Z",
        "icon": "collection"
      }
    ]
  }
}
```

---

## Error Status Codes Summary

- `401 Unauthorized`: Missing or invalid Bearer token.
- `422 Unprocessable Entity`: Invalid `date` query parameter format.
