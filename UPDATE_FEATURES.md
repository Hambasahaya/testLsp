# Update Features Added for Admin

## Overview
Added comprehensive UPDATE functionality for Admin users to manage products and users fully.

## New Endpoints

### 1. Update Product (Admin Only)
**Endpoint:** `PUT /api/products/{id}`

**Request:**
```json
{
  "name": "Updated Product Name",
  "description": "Updated description",
  "price": 99.99,
  "stock": 50
}
```

**Response:**
```json
{
  "message": "Produk berhasil diperbarui",
  "data": {
    "id": 1,
    "name": "Updated Product Name",
    "description": "Updated description",
    "price": 99.99,
    "stock": 50,
    "created_at": "...",
    "updated_at": "..."
  }
}
```

### 2. Update User (Admin Only)
**Endpoint:** `PUT /api/users/{id}`

**Request:**
```json
{
  "name": "Updated Name",
  "email": "newemail@example.com",
  "password": "newpassword"
}
```

**Response:**
```json
{
  "message": "User berhasil diperbarui",
  "data": {
    "id": 1,
    "name": "Updated Name",
    "email": "newemail@example.com",
    "role": {
      "id": 1,
      "name": "Admin"
    }
  }
}
```

## Frontend Features Added

### Product Management (Inventory Page)
- **Edit Button**: Admin can click "Edit" button on each product
- **Edit Modal**: Opens with form containing:
  - Nama Produk
  - Deskripsi
  - Harga
  - Stok
- **Delete Button**: Placeholder for delete functionality (to be implemented)

### User Management (Users Page)
- **Edit Button**: Next to role change button
- **Edit User Modal**: Opens with form containing:
  - Nama
  - Email
  - Password (optional, only changes if filled)

## Implementation Details

### Backend (Controllers)

#### ProductController@update
- Validates input fields (all optional with `sometimes`)
- Updates product with new values
- Returns updated product data

#### UserController@update
- Validates name, email, password
- Hashes password using bcrypt
- Ensures email is unique (except current user's email)
- Returns updated user with role

### Routes
```php
Route::put('/products/{product}', [ProductController::class, 'update'])
    ->middleware('role:Admin');
    
Route::put('/users/{user}', [UserController::class, 'update'])
    ->middleware('role:Admin');
```

### Frontend (JavaScript)

#### Inventory Page
- `openEditModal(productId, name, description, price, stock)` - Opens edit modal with product data
- `shouldShowEditButton()` - Shows edit/delete buttons only for Admin
- Form submission handles PUT request to `/api/products/{id}`

#### Users Page
- `openEditUserModal(userId, name, email)` - Opens edit user modal
- Form submission handles PUT request to `/api/users/{id}`
- Password field is optional

## Admin Full Access Summary

✅ **Products:**
- View all products (GET /api/products)
- Create new products (POST /api/products)
- **Update products** (PUT /api/products/{id}) - NEW
- Sell products (POST /api/products/{id}/sell)

✅ **Users:**
- View all users (GET /api/users)
- Change user roles (PUT /api/users/{id}/change-role)
- **Update user details** (PUT /api/users/{id}) - NEW

✅ **Authorization:**
- All endpoints protected by `auth:sanctum` middleware
- Update endpoints protected by `role:Admin` middleware
- Full access control enforced

## Security Features

1. **Password Hashing**: Passwords are hashed with bcrypt
2. **Email Uniqueness**: Email validation ensures no duplicates
3. **Authorization**: Update endpoints require Admin role
4. **CSRF Protection**: All requests include CSRF token
5. **Input Validation**: Server-side validation on all inputs
6. **SQL Injection Protection**: Eloquent ORM with parameterized queries

## Testing the Features

### Test Update Product
1. Login as admin@example.com / password
2. Go to Inventaris (Inventory) page
3. Click "Edit" button on any product
4. Change details and click "Simpan Perubahan"
5. Verify product updates in table

### Test Update User
1. Login as admin (already in Inventory)
2. Go to Manajemen User (Users) page
3. Click "Edit" button next to any user
4. Change name/email/password
5. Click "Simpan Perubahan"
6. Verify user updates in table

## Future Enhancements
- [ ] Delete product endpoint
- [ ] Delete user endpoint
- [ ] Batch update operations
- [ ] Audit logging for updates
- [ ] Update history tracking
