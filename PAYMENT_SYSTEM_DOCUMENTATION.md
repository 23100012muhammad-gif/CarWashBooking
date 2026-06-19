# Sistem Pembayaran CarWash Connect

## Overview
Sistem pembayaran telah diimplementasikan dengan fitur lengkap untuk menangani proses pembayaran dari booking hingga verifikasi admin.

## Fitur yang Diimplementasikan

### 1. Flow Booking yang Diperbarui
- ✅ Setelah user mengisi form pesan dan klik kirim, data booking disimpan dengan status "Pending Pembayaran"
- ✅ Redirect ke halaman konfirmasi pembayaran dengan rincian pesanan lengkap

### 2. Halaman Konfirmasi Pembayaran
- ✅ Menampilkan rincian pesanan (layanan, tanggal, slot, harga akhir)
- ✅ Opsi metode pembayaran (Transfer Bank & E-wallet)
- ✅ Informasi diskon jika ada

### 3. Metode Pembayaran Transfer Bank
- ✅ Menampilkan nomor rekening dan instruksi transfer
- ✅ Fitur upload bukti transfer untuk verifikasi admin
- ✅ Validasi file upload (JPG, PNG, PDF, maksimal 2MB)
- ✅ Catatan tambahan dari customer

### 4. Metode Pembayaran E-wallet
- ✅ Integrasi payment gateway pihak ketiga (simulasi)
- ✅ QR code pembayaran dan link pembayaran aktif
- ✅ Sandbox/test mode untuk fase pengujian
- ✅ Callback API untuk update status transaksi otomatis
- ✅ Auto-refresh status pembayaran setiap 30 detik

### 5. Verifikasi dan Notifikasi
- ✅ Admin menerima notifikasi saat ada bukti pembayaran masuk
- ✅ Halaman admin untuk verifikasi pembayaran
- ✅ Setelah verifikasi berhasil, status booking diubah menjadi "Terkonfirmasi"
- ✅ Notifikasi ke user saat status pembayaran berhasil/gagal

### 6. Halaman Riwayat Booking yang Diperbarui
- ✅ Kolom status pembayaran (Pending, Lunas, Gagal)
- ✅ Detail pesanan dengan metode pembayaran dan status
- ✅ Opsi cetak bukti pembayaran
- ✅ Preview bukti pembayaran yang sudah diverifikasi
- ✅ Status otomatis untuk metode e-wallet

### 7. Sistem Notifikasi
- ✅ Notifikasi real-time untuk admin dan user
- ✅ Notifikasi saat bukti pembayaran diupload
- ✅ Notifikasi saat pembayaran diverifikasi/ditolak
- ✅ Counter notifikasi yang belum dibaca

## File yang Dibuat/Dimodifikasi

### Database Migrations
- `2025_01_21_000002_add_payment_fields_to_orders_table.php`
- `2025_01_21_000003_create_payment_methods_table.php`
- `2025_01_21_000004_create_notifications_table.php`

### Models
- `app/Models/PaymentMethod.php`
- `app/Models/Notification.php`
- `app/Models/Order.php` (diperbarui)

### Controllers
- `app/Http/Controllers/PaymentController.php`
- `app/Http/Controllers/NotificationController.php`
- `app/Http/Controllers/BookingController.php` (diperbarui)
- `app/Http/Controllers/AdminController.php` (diperbarui)

### Services
- `app/Services/NotificationService.php`

### Views
- `resources/views/payment/confirmation.blade.php`
- `resources/views/payment/bank-transfer.blade.php`
- `resources/views/payment/ewallet.blade.php`
- `resources/views/payment/status.blade.php`
- `resources/views/admin/pending-payments.blade.php`
- `resources/views/history.blade.php` (diperbarui)
- `resources/views/admin/dashboard.blade.php` (diperbarui)

### Routes
- Routes pembayaran ditambahkan di `routes/web.php`

### Seeders
- `database/seeders/PaymentMethodSeeder.php`

## Cara Menjalankan

### 1. Jalankan Migrations
```bash
php artisan migrate --path=database/migrations/2025_01_21_000002_add_payment_fields_to_orders_table.php
php artisan migrate --path=database/migrations/2025_01_21_000003_create_payment_methods_table.php
php artisan migrate --path=database/migrations/2025_01_21_000004_create_notifications_table.php
```

### 2. Jalankan Seeder
```bash
php artisan db:seed --class=PaymentMethodSeeder
```

### 3. Pastikan Storage Link
```bash
php artisan storage:link
```

## Testing Flow

### 1. Test Booking Flow
1. Akses `/pesan/create`
2. Isi form booking
3. Submit form
4. Pastikan redirect ke halaman konfirmasi pembayaran

### 2. Test Transfer Bank
1. Pilih metode Transfer Bank
2. Upload bukti transfer
3. Cek di admin panel `/admin/pending-payments`
4. Verifikasi pembayaran
5. Cek status di halaman riwayat

### 3. Test E-wallet
1. Pilih metode E-wallet
2. Simulasi pembayaran
3. Cek status otomatis

### 4. Test Admin Verification
1. Login sebagai admin
2. Akses `/admin/pending-payments`
3. Verifikasi atau tolak pembayaran
4. Cek notifikasi

## Konfigurasi Produksi

### 1. Payment Gateway
Untuk produksi, ganti simulasi e-wallet dengan payment gateway nyata seperti:
- Midtrans
- Xendit
- Doku

### 2. File Storage
Pastikan konfigurasi storage untuk menyimpan bukti transfer:
```php
// config/filesystems.php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

### 3. Security
- Validasi file upload yang ketat
- Enkripsi data sensitif
- Rate limiting untuk upload

## API Endpoints

### Payment
- `GET /payment/confirmation/{order}` - Halaman konfirmasi pembayaran
- `POST /payment/process/{order}` - Proses pemilihan metode pembayaran
- `GET /payment/bank-transfer/{order}` - Halaman transfer bank
- `POST /payment/upload-proof/{order}` - Upload bukti transfer
- `GET /payment/ewallet/{order}` - Halaman e-wallet
- `GET /payment/status/{order}` - Cek status pembayaran

### Admin
- `GET /admin/pending-payments` - Halaman verifikasi admin
- `POST /admin/payment/verify/{order}` - Verifikasi pembayaran

### Notifications
- `GET /notifications` - Daftar notifikasi user
- `POST /notifications/{notification}/read` - Tandai notifikasi sebagai dibaca
- `GET /api/notifications/unread-count` - Jumlah notifikasi belum dibaca

## Status Pembayaran

- `pending` - Menunggu pembayaran
- `verified` - Pembayaran terverifikasi/lunas
- `failed` - Pembayaran gagal/ditolak

## Status Pesanan

- `Pending Pembayaran` - Menunggu pembayaran
- `Terkonfirmasi` - Pembayaran terverifikasi
- `Menunggu` - Menunggu antrian
- `Proses` - Sedang dicuci
- `Selesai` - Selesai

## Troubleshooting

### 1. Migration Error
Jika ada error PHP version, jalankan migration secara manual atau update PHP ke versi 8.1+

### 2. Storage Error
Pastikan folder `storage/app/public` dapat ditulis dan jalankan `php artisan storage:link`

### 3. Permission Error
Pastikan folder storage memiliki permission yang tepat:
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

## Kesimpulan

Sistem pembayaran telah diimplementasikan dengan lengkap sesuai dengan requirements:
- ✅ Flow booking dengan status "pending pembayaran"
- ✅ Halaman konfirmasi pembayaran
- ✅ Metode transfer bank dengan upload bukti
- ✅ Metode e-wallet dengan payment gateway
- ✅ Sistem verifikasi admin
- ✅ Halaman riwayat yang diperbarui
- ✅ Sistem notifikasi real-time
- ✅ Fitur cetak bukti pembayaran

Sistem siap untuk testing dan dapat dikonfigurasi untuk produksi dengan mengganti simulasi payment gateway dengan gateway nyata.

