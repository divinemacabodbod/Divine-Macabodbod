# API Testing Guide

## Create User (POST)
**URL:** `http://127.0.0.1:8000/api/users`

**Method:** POST

**Headers:**
```
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "name": "Divine Macabodbod",
  "email": "john@example.com",
  "password": "password123"
}
```

**Expected Response:**
```json
{
  "id": 1,
  "name": "Divine Macabodbod",
  "email": "john@example.com",
  "created_at": "2026-02-12T12:00:00.000000Z",
  "updated_at": "2026-02-12T12:00:00.000000Z"
}
```

---

## Get All Users (GET)
**URL:** `http://127.0.0.1:8000/api/users`

**Method:** GET

**No body needed**

---

## Get Single User (GET)
**URL:** `http://127.0.0.1:8000/api/users/1`

**Method:** GET

**No body needed**

---

## Update User (PUT)
**URL:** `http://127.0.0.1:8000/api/users/1`

**Method:** PUT

**Headers:**
```
Content-Type: application/json
```

**Body:**
```json
{
  "name": "Updated Name",
  "email": "newemail@example.com"
}
```

---

## Delete User (DELETE)
**URL:** `http://127.0.0.1:8000/api/users/1`

**Method:** DELETE

**No body needed**

---

## Common Issues & Fixes

### 405 Method Not Allowed
- Make sure you're using **POST** not GET
- Check the URL includes `/api/users`
- Verify `Content-Type: application/json` header is set

### Missing Field Errors
- Must include: `name`, `email`, `password` (for create)
- Email must be unique
- Password must be at least 8 characters

### Server Not Running
- Run: `php artisan serve` in the test_app directory
- Check it says: `Server running on [http://127.0.0.1:8000]`
