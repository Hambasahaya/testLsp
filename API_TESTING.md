# API Testing Guide

## Access the Application

- **URL**: http://localhost:8000
- **Login Page**: http://localhost:8000/login

## Test Credentials

```
Admin:
  Email: admin@example.com
  Password: password

Seller:
  Email: seller@example.com
  Password: password

Customer:
  Email: customer@example.com
  Password: password
```

## Testing API Endpoints

### 1. Authentication

#### Login (Get Token)

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

Response:

```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@example.com",
        "role": {
            "id": 1,
            "name": "Admin",
            "description": "Administrator with full access"
        }
    },
    "token": "1|XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
}
```

#### Get Current User

```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### Logout

```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 2. Products

#### Get All Products (Authenticated Users)

```bash
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### Create Product (Admin Only)

```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -d '{
    "name": "New Product",
    "description": "Product description",
    "price": 99.99,
    "stock": 20
  }'
```

#### Sell Product (Admin & Seller Only)

```bash
curl -X POST http://localhost:8000/api/products/1/sell \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "quantity": 5
  }'
```

### 3. Users

#### Get All Users (Admin Only)

```bash
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

#### Change User Role (Admin Only)

```bash
curl -X PUT http://localhost:8000/api/users/2/change-role \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -d '{
    "role_id": 2
  }'
```

## Role IDs

- 1: Admin
- 2: Seller
- 3: Pelanggan (Customer)

## Testing Scenarios

### Scenario 1: Admin Workflow

1. Login as admin
2. View all products
3. Add a new product
4. Sell product
5. View all users
6. Change user role
7. Logout

### Scenario 2: Seller Workflow

1. Login as seller
2. View all products
3. Attempt to add product (should fail - 403)
4. Sell product (should succeed)
5. View users (should fail - 403)
6. Logout

### Scenario 3: Customer Workflow

1. Login as customer
2. View all products
3. Attempt to sell product (should fail - 403)
4. Logout

### Scenario 4: Stock Validation

1. Check current stock of a product
2. Try to sell more than available (should fail with 422)
3. Sell valid quantity (should succeed)
4. Verify stock decreases

## Frontend Features to Test

### Login Page

- [ ] Login with valid credentials
- [ ] Login with invalid email
- [ ] Login with wrong password
- [ ] Verify demo credentials are displayed

### Dashboard

- [ ] View total products count
- [ ] View total stock count
- [ ] View current user role
- [ ] Navigation to other pages

### Inventory Page

- [ ] View all products in table
- [ ] Check product details (name, price, stock)
- [ ] Stock status badge colors (green for sufficient, red for low)
- [ ] Admin: See "Tambah Produk" button
- [ ] Admin/Seller: See "Jual" button
- [ ] Customer: No "Jual" button

### Add Product Modal (Admin Only)

- [ ] Fill in all required fields
- [ ] Submit form successfully
- [ ] Verify new product appears in table
- [ ] Try submitting without required fields (should show validation)

### Sell Product Modal

- [ ] Open sell modal for a product
- [ ] Enter quantity less than stock (should show green checkmark)
- [ ] Enter quantity more than stock (should show error)
- [ ] Submit valid quantity
- [ ] Verify stock updates in table

### Users Management Page (Admin Only)

- [ ] View all users in table
- [ ] See current role of each user
- [ ] Change role from dropdown
- [ ] Click "Simpan" button
- [ ] Verify role updates

### Navigation & Security

- [ ] Navigation menu shows only relevant items
- [ ] Admin sees "Manajemen User" menu
- [ ] Seller doesn't see "Manajemen User" menu
- [ ] Customer doesn't see "Manajemen User" menu
- [ ] Logout button works correctly
- [ ] After logout, redirected to login page
- [ ] Cannot access protected pages without login

## Error Handling Tests

### Stock Validation

- [ ] Attempt to sell 0 quantity
- [ ] Attempt to sell negative quantity
- [ ] Attempt to sell more than available

### Authorization Tests

- [ ] Customer tries to access /api/products (should succeed)
- [ ] Customer tries to POST /api/products (should fail - 403)
- [ ] Customer tries to POST /api/products/{id}/sell (should fail - 403)
- [ ] Seller tries to PUT /api/users/{id}/change-role (should fail - 403)
- [ ] Unauthenticated user tries any API (should fail - 401)

### Form Validation

- [ ] Submit product form with empty name
- [ ] Submit product form with negative price
- [ ] Submit product form with negative stock

## Performance Notes

- All database queries use efficient Eloquent methods
- Products are loaded with relationships when needed
- Pagination can be added for large datasets
- API responses include appropriate HTTP status codes

## Security Considerations

- All passwords are hashed with bcrypt
- JWT tokens expire according to Sanctum config
- CSRF protection on web forms
- SQL injection protection with Eloquent ORM
- Authorization middleware on all protected routes
- Role-based access control enforced at controller level
