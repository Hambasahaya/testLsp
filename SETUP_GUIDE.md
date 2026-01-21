# Inventory Management System

Aplikasi web untuk manajemen inventaris dan pengguna dengan fitur autentikasi JWT dan role-based access control.

## Fitur Utama

### 1. **Sistem Autentikasi**

- Login dengan email dan password
- JWT Token untuk API authentication
- Session management untuk web interface

### 2. **Sistem Inventaris**

- Lihat daftar produk (semua pengguna terautentikasi)
- Tambah produk baru (Admin)
- Proses transaksi penjualan (Admin & Seller)
- Validasi stok otomatis

### 3. **Manajemen Pengguna**

- Lihat daftar pengguna (Admin)
- Ubah role pengguna (Admin)
- Role: Admin, Seller, Pelanggan

## Teknologi yang Digunakan

- **Backend**: Laravel 12
- **Database**: MySQL
- **Authentication**: Laravel Sanctum (JWT)
- **Frontend**: Bootstrap 5
- **JavaScript**: Vanilla JS dengan Fetch API

## Struktur Database

### Tabel Roles

```
- id (PK)
- name (Admin, Seller, Pelanggan)
- description
- timestamps
```

### Tabel Users

```
- id (PK)
- name
- email (unique)
- password
- role_id (FK to roles)
- email_verified_at
- remember_token
- timestamps
```

### Tabel Products

```
- id (PK)
- name
- description
- price (decimal)
- stock (integer)
- timestamps
```

### Tabel Sales Transactions

```
- id (PK)
- user_id (FK to users)
- product_id (FK to products)
- quantity (integer)
- total_price (decimal)
- timestamps
```

## Setup Aplikasi

### 1. Instalasi Dependensi

```bash
composer install
npm install
```

### 2. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan atur database credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=testlsp
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Jalankan Migrasi

```bash
php artisan migrate
```

### 4. Seed Database

```bash
php artisan db:seed
```

### 5. Build Assets

```bash
npm run build
```

### 6. Jalankan Aplikasi

```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## Default Credentials

| Email                | Password | Role      |
| -------------------- | -------- | --------- |
| admin@example.com    | password | Admin     |
| seller@example.com   | password | Seller    |
| customer@example.com | password | Pelanggan |

## API Endpoints

### Authentication

- `POST /api/auth/login` - Login pengguna
- `POST /api/auth/logout` - Logout pengguna
- `GET /api/auth/me` - Dapatkan data pengguna saat ini

### Products

- `GET /api/products` - Lihat semua produk (Authenticated)
- `POST /api/products` - Tambah produk baru (Admin)
- `POST /api/products/{id}/sell` - Jual produk (Admin & Seller)

### Users

- `GET /api/users` - Lihat semua pengguna (Admin)
- `PUT /api/users/{id}/change-role` - Ubah role pengguna (Admin)

## Role-Based Access Control

### Admin

- Akses penuh ke semua endpoint
- Dapat membuat, membaca, dan menghapus produk
- Dapat mengelola user dan role
- Dapat melakukan transaksi penjualan

### Seller

- Dapat melihat daftar produk
- Dapat melakukan transaksi penjualan
- Tidak dapat membuat produk baru

### Pelanggan (Customer)

- Hanya dapat melihat daftar produk
- Tidak dapat melakukan transaksi penjualan
- Akses terbatas

## Menu Aplikasi

### 1. Dashboard

- Tampilan ringkas dengan statistik
- Total produk
- Total stok
- Role pengguna saat ini

### 2. Inventaris

- Tabel daftar produk dengan detail lengkap
- Tombol "Tambah Produk" untuk Admin
- Tombol "Jual" untuk Admin dan Seller
- Validasi stok pada saat penjualan

### 3. Manajemen User (Admin Only)

- Tabel daftar pengguna
- Dropdown untuk mengubah role
- Tombol "Simpan" untuk menyimpan perubahan

## Validasi dan Error Handling

### Stok Produk

- Sistem tidak memungkinkan penjualan jika stok kurang
- Pesan error ditampilkan ketika user mencoba menjual lebih dari stok tersedia

### Authorization

- Setiap endpoint dilindungi dengan middleware authentication
- Role-based access control diterapkan pada endpoint yang memerlukan

### Input Validation

- Semua input divalidasi di server
- Pesan error yang jelas ditampilkan ke user

## Struktur Folder

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── ProductController.php
│   │   │   └── UserController.php
│   │   └── Middleware/
│   │       ├── CheckRole.php
│   │       ├── CheckRoles.php
│   │       └── AdminMiddleware.php
│   └── Models/
│       ├── Role.php
│       ├── User.php
│       ├── Product.php
│       └── SalesTransaction.php
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   ├── api.php
│   └── web.php
├── resources/
│   ├── views/
│   │   ├── auth/
│   │   │   └── login.blade.php
│   │   ├── layouts/
│   │   │   └── app.blade.php
│   │   ├── dashboard.blade.php
│   │   ├── inventory.blade.php
│   │   └── users.blade.php
│   ├── css/
│   └── js/
└── public/
    └── index.php
```

## Troubleshooting

### Database Error

Pastikan MySQL berjalan dan database `testlsp` telah dibuat:

```bash
mysql -u root -e "CREATE DATABASE testlsp;"
```

### Migration Error

Jalankan migration dengan force:

```bash
php artisan migrate:fresh --seed
```

### Port 8000 Already in Use

Gunakan port lain:

```bash
php artisan serve --port=8001
```

## Fitur Keamanan

- Semua password di-hash menggunakan bcrypt
- CSRF protection untuk form
- SQL injection protection dengan Eloquent ORM
- Session security dengan secure cookies
- Authorization middleware untuk setiap endpoint

## Pengembangan Lebih Lanjut

Fitur yang dapat ditambahkan:

- Export laporan penjualan (Excel/PDF)
- Dashboard analytics dengan chart
- Notifikasi email untuk transaksi
- Pagination untuk tabel
- Search dan filter produk
- Histori penjualan detail
- Role custom
- Permission-based access control

## License

MIT License
