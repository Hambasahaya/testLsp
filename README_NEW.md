# Inventory Management System

A professional web application for managing product inventory and user roles with JWT authentication and role-based access control.

![Laravel](https://img.shields.io/badge/Laravel-12-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2-blue?logo=php)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-purple?logo=bootstrap)
![MySQL](https://img.shields.io/badge/MySQL-Relational-orange?logo=mysql)
![Sanctum](https://img.shields.io/badge/Auth-Sanctum%20JWT-green)

## 🎯 Features

### Authentication & Authorization

- JWT token-based authentication using Laravel Sanctum
- Secure password hashing with bcrypt
- Role-Based Access Control (RBAC) with 3 roles: Admin, Seller, Customer
- Protected API endpoints with middleware
- Session management for web interface

### Inventory Management

- Product listing with stock tracking
- Create new products (Admin only)
- Process sales transactions with stock validation
- Prevent overselling (stock cannot go negative)
- Real-time inventory updates
- Transaction history

### User Management

- View all users (Admin only)
- Assign and change user roles (Admin only)
- Role visibility and permissions
- User authentication tracking

### Beautiful Interface

- Responsive Bootstrap 5 design
- Admin dashboard with statistics
- Intuitive product and user management pages
- Real-time notifications
- Modal dialogs for forms
- Mobile-friendly navigation

## 🚀 Quick Start

### Prerequisites

- PHP 8.2+
- MySQL 5.7+
- Composer
- Git (optional)

### Installation

```bash
# 1. Navigate to project directory
cd c:\Users\ASUS\Documents\project-php\laravel\testLsp

# 2. Install dependencies
composer install

# 3. Setup environment
copy .env.example .env
php artisan key:generate

# 4. Configure database (edit .env)
DB_DATABASE=testlsp
DB_USERNAME=root
DB_PASSWORD=

# 5. Run migrations
php artisan migrate

# 6. Seed demo data
php artisan demo:seed

# 7. Start development server
php artisan serve
```

### Access Application

**Web UI**: http://localhost:8000  
**Login Page**: http://localhost:8000/login

### Demo Credentials

```
Admin User:
  Email: admin@example.com
  Password: password

Seller User:
  Email: seller@example.com
  Password: password

Customer User:
  Email: customer@example.com
  Password: password
```

## 📋 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php          # Authentication logic
│   │   ├── ProductController.php       # Product management
│   │   └── UserController.php          # User management
│   └── Middleware/
│       ├── CheckRole.php               # Single role verification
│       ├── CheckRoles.php              # Multiple role verification
│       └── AdminMiddleware.php          # Admin-only access
├── Models/
│   ├── User.php                        # User model with relationships
│   ├── Role.php                        # Role model
│   ├── Product.php                     # Product model
│   └── SalesTransaction.php            # Transaction model
└── Console/Commands/
    └── SeedDemoData.php                # Custom seeding command

database/
├── migrations/
│   ├── create_roles_table.php
│   ├── add_role_id_to_users_table.php
│   ├── create_products_table.php
│   └── create_sales_transactions_table.php
└── seeders/
    ├── RoleSeeder.php
    └── DatabaseSeeder.php

resources/views/
├── auth/login.blade.php                # Login page
├── layouts/app.blade.php               # Main layout
├── dashboard.blade.php                 # Dashboard page
├── inventory.blade.php                 # Inventory page
└── users.blade.php                     # User management page

routes/
├── api.php                             # API endpoints
└── web.php                             # Web routes
```

## 🔗 API Endpoints

### Authentication

- `POST /api/auth/login` - User login
- `GET /api/auth/me` - Get current user
- `POST /api/auth/logout` - Logout user

### Products

- `GET /api/products` - List all products (authenticated users)
- `POST /api/products` - Create product (Admin only)
- `POST /api/products/{id}/sell` - Sell product (Admin & Seller)

### Users

- `GET /api/users` - List all users (Admin only)
- `PUT /api/users/{id}/change-role` - Change user role (Admin only)

## 👥 Role Permissions

### Admin

- ✅ Full access to all features
- ✅ Create and manage products
- ✅ Process sales
- ✅ Manage user roles
- ✅ View all users

### Seller

- ✅ View products
- ✅ Process sales
- ❌ Create products
- ❌ Manage users

### Customer

- ✅ View products
- ❌ Process sales
- ❌ Create products
- ❌ Manage users

## 📊 Database Schema

### Roles Table

```sql
id | name | description | timestamps
```

### Users Table

```sql
id | name | email | password | role_id | timestamps
```

### Products Table

```sql
id | name | description | price | stock | timestamps
```

### Sales Transactions Table

```sql
id | user_id | product_id | quantity | total_price | timestamps
```

## 🔒 Security Features

- Password hashing with bcrypt
- CSRF protection on forms
- SQL injection prevention via Eloquent ORM
- XSS protection in Blade templates
- JWT token-based API authentication
- Authorization middleware on protected routes
- Secure session handling

## 📖 Documentation

- [**QUICK_START.md**](QUICK_START.md) - 30-second setup guide
- [**SETUP_GUIDE.md**](SETUP_GUIDE.md) - Detailed installation instructions
- [**API_TESTING.md**](API_TESTING.md) - API testing guide with examples
- [**IMPLEMENTATION_SUMMARY.md**](IMPLEMENTATION_SUMMARY.md) - Complete feature list

## 🧪 Testing

### API Testing

```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Get products
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer YOUR_TOKEN"
```

See [API_TESTING.md](API_TESTING.md) for complete testing guide.

### Frontend Testing

1. Login with demo credentials
2. Navigate through pages
3. Test role-based permissions
4. Verify stock validation
5. Test product and user management

## 🛠️ Technologies Used

- **Framework**: Laravel 12
- **Database**: MySQL
- **Authentication**: Laravel Sanctum (JWT)
- **Frontend**: Bootstrap 5
- **Scripting**: Vanilla JavaScript
- **ORM**: Eloquent
- **PHP**: 8.2+

## 📝 Features Implemented

- [x] User authentication with JWT
- [x] Role-based access control
- [x] Product inventory management
- [x] Sales transaction processing
- [x] Stock validation
- [x] User role management
- [x] RESTful API
- [x] Responsive web interface
- [x] Form validation
- [x] Error handling
- [x] Database seeding
- [x] Comprehensive documentation

## 🚀 Deployment

For production deployment:

1. Set `.env` to production mode
2. Generate new application key
3. Run migrations on production database
4. Configure proper environment variables
5. Setup proper security headers
6. Enable HTTPS
7. Configure CORS for API
8. Setup proper logging

## 📦 Dependencies

- laravel/framework: ^12.0
- laravel/sanctum: ^4.2
- laravel/tinker: ^2.10
- PHP: ^8.2

## 💡 Tips & Tricks

- Use `php artisan tinker` for interactive database manipulation
- Check `storage/logs/` for application logs
- Use `php artisan migrate:fresh --seed` to reset database
- Use `php artisan demo:seed` to populate demo data only

## 🐛 Troubleshooting

### Port 8000 already in use

```bash
php artisan serve --port=8001
```

### Database migration errors

```bash
php artisan migrate:fresh --seed
```

### Permission denied on Linux/Mac

```bash
chmod -R 755 storage bootstrap/cache
```

## 📄 License

This project is open source and available under the MIT License.

## 👨‍💻 Support

For issues or questions:

1. Check the documentation files
2. Review error messages in logs
3. Ensure all prerequisites are installed
4. Verify database configuration

## 🎓 Learning Resources

This project demonstrates:

- Laravel best practices
- Eloquent ORM and relationships
- Middleware for authorization
- RESTful API design
- JWT authentication
- Role-based access control
- Bootstrap responsive design
- Form validation and error handling

---

**Status**: ✅ Production Ready  
**Last Updated**: January 20, 2026  
**Framework**: Laravel 12  
**PHP**: 8.2+
