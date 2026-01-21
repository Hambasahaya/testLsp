# Aplikasi Manajemen Inventaris dan Penjualan

Aplikasi Laravel untuk mengelola produk, pengguna, dan transaksi penjualan dengan sistem role-based access control.

## Daftar Isi

- [Instalasi](#instalasi)
- [Setup Database](#setup-database)
- [Fitur yang Telah Selesai](#fitur-yang-telah-selesai)

---

## Instalasi

### Prasyarat

- PHP 8.1 atau lebih tinggi
- Composer
- MySQL

### Langkah-Langkah Instalasi

1. **Clone atau Ekstrak Proyek**

    ```bash
    cd path/to/project
    ```

2. **Install Dependensi PHP**

    ```bash
    composer install
    ```

3. **Install Dependensi Node.js**

    ```bash
    npm install
    ```

4. **Copy File Konfigurasi**

    ```bash
    cp .env.example .env
    ```

5. **Generate Application Key**

    ```bash
    php artisan key:generate
    ```

6. **Konfigurasi Database**

7. **Jalankan Migrasi Database**

    ```bash
    php artisan migrate
    ```

8. **Seed Data Awal (Opsional)**

    ```bash
    php artisan db:seed
    ```

9. **Jalankan Server Lokal**
    ```bash
    php artisan serve
    ```
    Aplikasi akan berjalan di `http://localhost:8000`

---

## Setup Database

### Konfigurasi File `.env`

Edit file `.env` di root project dengan konfigurasi database Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=password_anda
```

### Membuat Database

1. **Menggunakan MySQL CLI**

    ```bash
    mysql -u root -p
    CREATE DATABASE nama_database_anda;
    EXIT;
    ```

2. **Menjalankan Migrasi**

    ```bash
    php artisan migrate
    ```

    Migrasi yang akan dijalankan:
    - Membuat tabel `users` dengan field email, password, dan role
    - Membuat tabel `roles` untuk manajemen role/hak akses
    - Membuat tabel `products` untuk data produk
    - Membuat tabel `sales_transactions` untuk catatan penjualan
    - Tabel pendukung: cache, jobs, personal access tokens

3. **Seeding Data Awal (Opsional)**
    ```bash
    php artisan db:seed
    ```
    Ini akan membuat:
    - Role dasar
    - User contoh untuk testing

---

## Fitur yang Telah Selesai

### 1. **Manajemen Pengguna (User Management)**

- Registrasi dan login pengguna
- Sistem role-based access control (RBAC)
- Profile pengguna dengan role assignment
- Autentikasi menggunakan Laravel Sanctum untuk API

### 2. **Manajemen Role dan Permissions**

- Tabel roles untuk mendefinisikan berbagai peran pengguna
- Relasi antara users dan roles
- Model Role dengan relationship ke User

### 3. **Manajemen Produk**

- CRUD (Create, Read, Update, Delete) untuk produk
- Menyimpan informasi produk lengkap
- Model Product dengan fitur inventory

### 4. **Transaksi Penjualan (Sales Transactions)**

- Pencatatan setiap transaksi penjualan
- Relasi antara transaksi dengan produk dan pengguna
- Model SalesTransaction untuk tracking penjualan

### 5. **Antarmuka Web (Views)**

- Dashboard untuk overview aplikasi
- Halaman Inventory untuk manajemen produk
- Halaman Users untuk manajemen pengguna
- Halaman login
- Halaman Dashboard
- Layout responsif dengan Blade templating

### 6. **Autentikasi dan Keamanan**

- Authentication menggunakan Laravel auth
- Personal Access Tokens untuk API
- Middleware untuk proteksi route

### 7. **Database Structure**

- Migrasi terstruktur untuk pembuatan tabel
- Factory untuk testing data generation
- Seeder untuk data awal

### 8. **API Endpoints**

- Route API terpisah di `routes/api.php`
- Support untuk berbagai operasi CRUD melalui REST API

---

## Testing

Jalankan test suite dengan PHPUnit:

```bash
php artisan test
```

Atau jalankan test tertentu:

```bash
php artisan test tests/Feature/ExampleTest.php
```

---

## Troubleshooting

### Error: "No application encryption key has been specified"

```bash
php artisan key:generate
```

### Error: Database Connection Refused

- Pastikan MySQL/database sudah berjalan
- Verifikasi konfigurasi di file `.env`

### Error: Class not found

```bash
composer dump-autoload
```

---
