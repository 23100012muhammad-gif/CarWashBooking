Tentu, berikut terjemahan bahasa Indonesianya:

# Instruksi Penyiapan untuk Sistem Email CarWashConnect

-----

## Penyiapan Sistem Email (Google Apps Script)

1.  Buat **proyek Google Apps Script** baru

2.  Salin konten dari `resources/google-apps-script/email_sender.gs` ke dalam proyek

3.  **Deploy** sebagai **web app**:

      * Klik "**Deploy**" \> "**New deployment**" (Penerapan baru)
      * Pilih "**Web app**"
      * Atur "**Execute as**" (Jalankan sebagai) ke akun Anda
      * Atur "**Who has access**" (Siapa yang memiliki akses) ke "**Anyone**" (Siapa saja)
      * Klik "**Deploy**"
      * Salin **URL deployment** (URL penerapan)

4.  **Atur Properti Script**:

      * Di editor Apps Script, buka "**Project Settings**" (Pengaturan Proyek)
      * Klik tab "**Script Properties**" (Properti Script)
      * Tambahkan properti baru:
          * Nama: `APP_SECRET`
          * Nilai: [buat string acak yang aman]

-----

## Penyiapan Environment Laravel

Tambahkan variabel-variabel ini ke file `.env` Anda:

```env
GAS_SCRIPT_URL=https://script.google.com/macros/s/YOUR_SCRIPT_ID/exec
APP_SECRET=010603

QUEUE_CONNECTION=database
BOOKING_MAX_DAYS_AHEAD=14
BOOKING_DEFAULT_RANGE_DAYS=7
```

-----

## Penyiapan Database

Jalankan migrasi:

```bash
php artisan migrate
```

-----

## Penyiapan Queue (Antrian)

1.  Buat tabel antrian:

<!-- end list -->

```bash
php artisan queue:table
php artisan migrate
```

2.  Mulai *worker* antrian:

<!-- end list -->

```bash
php artisan queue:work --tries=3
```

-----

## Pengujian Integrasi Email

Anda dapat menguji integrasi email menggunakan *tinker*:

```php
$notification = \App\Models\Notification::create([
    'user_id' => 1, // Ganti dengan id user yang sudah ada
    'judul' => 'Test Email',
    'pesan' => 'Ini adalah email percobaan',
    'jenis' => 'email',
    'status' => 'belum'
]);

\App\Jobs\SendEmailNotification::dispatch($notification);
```

-----

## Catatan Keamanan

1.  Jaga kerahasiaan `APP_SECRET` Anda dan jangan pernah *commit* ke kontrol versi
2.  Google Apps Script hanya akan menerima permintaan dengan **token rahasia yang benar**
3.  Semua email dikirim melalui **Gmail**, mematuhi kuota dan batasannya

-----

## Pemecahan Masalah (*Troubleshooting*)

1.  Periksa `notifications.meta` untuk **log kesalahan terperinci**
2.  Notifikasi yang gagal akan **dicoba ulang secara otomatis hingga 3 kali**
3.  Setelah 3 kegagalan, admin akan menerima **notifikasi dalam aplikasi** (*in-app notification*)
4.  Periksa **log Laravel** untuk masalah *worker* antrian

-----
