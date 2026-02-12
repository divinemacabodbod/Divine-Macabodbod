# Complete API Setup & Usage Guide

## ✅ Server Status
- **Server Running**: YES ✓
- **URL**: http://127.0.0.1:8000
- **All Endpoints**: WORKING ✓

---

## How to Use REST Client (Postman/Insomnia/VS Code)

### 1️⃣ CREATE USER (POST)

**Step-by-Step:**
1. Set Method: **POST**
2. URL: `http://127.0.0.1:8000/api/users`
3. Click **Headers** tab
4. Add header:
   - Key: `Content-Type`
   - Value: `application/json`
5. Click **Body** tab
6. Select **raw** or **JSON** format
7. Paste this:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123"
}
```
8. Click **Send**

**Expected Response (201 Created):**
```json
{
  "id": 3,
  "name": "John Doe",
  "email": "john@example.com",
  "created_at": "2026-02-12T04:42:33.000000Z",
  "updated_at": "2026-02-12T04:42:33.000000Z"
}
```

---

### 2️⃣ GET ALL USERS (GET)

**Step-by-Step:**
1. Set Method: **GET**
2. URL: `http://127.0.0.1:8000/api/users`
3. No headers needed
4. No body needed
5. Click **Send**

**Expected Response (200 OK):**
```json
[
  {
    "id": 1,
    "name": "Divine Macabodbod",
    "email": "john@example.com",
    "created_at": "2026-02-12T04:30:00.000000Z",
    "updated_at": "2026-02-12T04:30:00.000000Z"
  },
  {
    "id": 2,
    "name": "Test User",
    "email": "test@example.com",
    "created_at": "2026-02-12T04:42:33.000000Z",
    "updated_at": "2026-02-12T04:42:33.000000Z"
  }
]
```

---

### 3️⃣ GET SINGLE USER (GET)

**Step-by-Step:**
1. Set Method: **GET**
2. URL: `http://127.0.0.1:8000/api/users/1` (replace 1 with user ID)
3. No headers needed
4. No body needed
5. Click **Send**

**Expected Response (200 OK):**
```json
{
  "id": 1,
  "name": "Divine Macabodbod",
  "email": "john@example.com",
  "created_at": "2026-02-12T04:30:00.000000Z",
  "updated_at": "2026-02-12T04:30:00.000000Z"
}
```

---

### 4️⃣ UPDATE USER (PUT)

**Step-by-Step:**
1. Set Method: **PUT**
2. URL: `http://127.0.0.1:8000/api/users/1` (replace 1 with user ID)
3. Click **Headers** tab
4. Add header:
   - Key: `Content-Type`
   - Value: `application/json`
5. Click **Body** tab
6. Select **raw** or **JSON**
7. Paste this (only send fields you want to update):
```json
{
  "name": "Updated Name",
  "email": "newemail@example.com"
}
```
8. Click **Send**

**Expected Response (200 OK):**
```json
{
  "id": 1,
  "name": "Updated Name",
  "email": "newemail@example.com",
  "created_at": "2026-02-12T04:30:00.000000Z",
  "updated_at": "2026-02-12T04:50:00.000000Z"
}
```

---

### 5️⃣ DELETE USER (DELETE)

**Step-by-Step:**
1. Set Method: **DELETE**
2. URL: `http://127.0.0.1:8000/api/users/1` (replace 1 with user ID)
3. No headers needed
4. No body needed
5. Click **Send**

**Expected Response (200 OK):**
```json
{
  "message": "User deleted successfully"
}
```

---

## ❌ Common Errors & Solutions

### Error: "405 Method Not Allowed"
**Cause:** Wrong HTTP method or missing Content-Type header
**Fix:** 
- Use **POST** for creating
- Use **GET** for reading
- Use **PUT** for updating
- Use **DELETE** for deleting
- Add `Content-Type: application/json` header

### Error: "Validation error" / "Required fields"
**Cause:** Missing required fields in POST/PUT
**Fix:**
- For CREATE: Must have `name`, `email`, `password`
- For UPDATE: Can send just the fields you want to change
- `name` must be string
- `email` must be unique
- `password` must be at least 8 characters

### Error: "Email already exists"
**Cause:** Email is already in database
**Fix:** Use a different email address

### Server shows "Could not open input file: artisan"
**Solution:** Keep server running in background
```powershell
cd C:\Users\Romeo\Desktop\Divine-Macabodbod\test_app
php artisan serve
```
Don't close this terminal window while testing!

---

## ✅ Full cURL Examples (if using command line)

```bash
# CREATE
curl -X POST http://127.0.0.1:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123"}'

# GET ALL
curl -X GET http://127.0.0.1:8000/api/users

# GET SINGLE
curl -X GET http://127.0.0.1:8000/api/users/1

# UPDATE
curl -X PUT http://127.0.0.1:8000/api/users/1 \
  -H "Content-Type: application/json" \
  -d '{"name":"Updated Name"}'

# DELETE
curl -X DELETE http://127.0.0.1:8000/api/users/1
```

---

## Database Info
- **Type:** SQLite
- **Location:** `C:\Users\Romeo\Desktop\Divine-Macabodbod\test_app\database\database.sqlite`
- **Users Table:** Automatically created ✓

