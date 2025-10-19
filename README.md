# Car Wash Booking System

Aplikasi web Laravel 8.x untuk sistem pemesanan jasa cuci mobil dengan antrian otomatis.

## Fitur Utama

### Interface Pengguna
- **Home**: Halaman utama dengan informasi layanan
- **Layanan**: Daftar 4 jenis layanan cuci mobil dengan harga transparan
- **Pemesanan**: Form booking dengan nomor antrean otomatis
- **Status Pesanan**: Melihat status pesanan aktif
- **Riwayat**: Riwayat pesanan yang telah selesai
- **Profil**: Manajemen profil pengguna

### Admin Panel
- **Dashboard**: Ringkasan statistik pesanan
- **Kelola Pesanan**: Update status pesanan (Menunggu → Proses → Selesai)

## Teknologi

- **Laravel**: 8.x
- **PHP**: 8.1 (using PHP 7.4-compatible syntax only)
- **Database**: SQLite (default)
- **Frontend**: Bootstrap 5, Blade Templates
- **Icons**: Bootstrap Icons

**Note**: While originally designed for PHP 7.4, this application runs on PHP 8.1 (Replit's available version) but uses strictly PHP 7.4-compatible syntax throughout - no PHP 8.0+ specific features are used.

## Jenis Layanan

1. **Cuci Luar** - Rp 35.000
2. **Cuci Dalam** - Rp 50.000
3. **Cuci Full** - Rp 75.000
4. **Salon Mobil** - Rp 150.000

## Setup Instructions

### 1. Install Dependencies
```bash
composer install
```

### 2. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Setup Database
```bash
touch database/database.sqlite
php artisan migrate
php artisan db:seed
```

### 4. Run Development Server
```bash
php artisan serve --host=0.0.0.0 --port=5000
```

## Struktur Database

### Table: services
- id (bigint)
- name (string)
- description (text)
- price (integer)
- timestamps

### Table: orders
- id (bigint)
- user_id (integer, nullable)
- service_type (string)
- booking_date (datetime)
- license_plate (string)
- queue_number (integer)
- status (string: 'Menunggu', 'Proses', 'Selesai')
- timestamps

## Routes

### User Routes
- `GET /` - Home
- `GET /layanan` - Services list
- `GET /pesan/create` - Booking form
- `POST /pesan/store` - Create booking
- `GET /status-pesanan` - Active orders
- `GET /riwayat` - Order history
- `GET /profil` - User profile

### Admin Routes
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/orders` - Manage orders
- `POST /admin/orders/{id}/update` - Update order status

## PHP 7.4 Compatibility

Semua kode ditulis dengan sintaks PHP 7.4 murni:
- Tidak menggunakan named arguments (PHP 8.0+)
- Tidak menggunakan constructor property promotion (PHP 8.0+)
- Tidak menggunakan union types (PHP 8.0+)
- Tidak menggunakan nullsafe operator (PHP 8.0+)
- Tidak menggunakan match expression (PHP 8.0+)

## Credits

Developed for car wash service management with automated queue system.
