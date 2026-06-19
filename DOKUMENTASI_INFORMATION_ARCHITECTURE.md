# CarWash Connect - Dokumentasi Information Architecture

## 1. OVERVIEW SISTEM

**CarWash Connect** adalah sistem web booking layanan cuci mobil dengan fitur:
- Pemesanan online dengan sistem slot waktu
- Sistem pembayaran dan verifikasi otomatis
- Manajemen jadwal oleh admin
- Notifikasi real-time untuk customer
- Tracking status pesanan

---

## 2. SITE MAP (Information Architecture)

```
CarWash Connect
├── PUBLIC AREA
│   ├── Home (/)
│   ├── Layanan (/layanan)
│   └── Authentication
│       ├── Login (/login) - Custom Bootstrap UI
│       └── Register (/register) - Custom Bootstrap UI
│
├── CUSTOMER AREA (Login Required)
│   ├── Pemesanan (/pesan/create)
│   ├── Status Pesanan (/status-pesanan)
│   ├── Riwayat (/riwayat)
│   │   └── Modal Detail Pesanan
│   ├── Profil (/profil)
│   │   ├── Edit Profil
│   │   └── Logout
│   ├── Notifikasi (/notifications)
│   │   ├── Bell Icon (Real-time)
│   │   ├── Dropdown Preview
│   │   ├── Mark as Read
│   │   └── Delete Notifications
│   └── Pembayaran
│       ├── Konfirmasi (/payment/confirmation/{id})
│       ├── Bank Transfer (/payment/bank-transfer/{id})
│       ├── E-Wallet (/payment/ewallet/{id})
│       └── Status (/payment/status/{id})
│
└── ADMIN AREA (/admin)
    ├── Login (/admin/login)
    ├── Dashboard (/admin/dashboard)
    ├── Profil Admin (/admin/profile)
    │   ├── Edit Admin Profile
    │   ├── Cek Halaman User
    │   └── Logout Admin
    ├── Verifikasi (/admin/verifications)
    │   ├── Tab: Verifikasi Pembayaran
    │   │   ├── Modal Lihat Bukti
    │   │   ├── Approve/Reject
    │   │   └── Auto-notify Customer
    │   └── Tab: Pengajuan Refund
    │       ├── Approve/Reject
    │       └── Auto-notify Customer
    ├── Kelola Proses (/admin/orders)
    │   └── Update Status → Auto-notify Customer
    ├── Jadwal & Slot (/admin/jadwal-slot)
    │   ├── Tab: Hari Operasional
    │   └── Tab: Kelola Slot
    │       ├── Date Range Input
    │       ├── Multiple Time Slots
    │       ├── Edit/Delete Individual Slots
    │       └── Generate Otomatis
    ├── Layanan (/admin/services)
    │   ├── Daftar Layanan
    │   ├── Tambah Layanan
    │   └── Edit Layanan
    └── Diskon (/admin/discounts)
        ├── Daftar Diskon
        ├── Tambah Diskon
        └── Edit Diskon
```

---

## 3. USER PERSONAS & ROLES

### 3.1 Customer (Pelanggan)
- **Goal**: Booking layanan cuci mobil dengan mudah dan mendapat update real-time
- **Pain Points**: Antrian panjang, tidak tahu jadwal kosong, tidak ada informasi progress
- **Needs**: Booking online, pilih waktu, tracking status, notifikasi update

### 3.2 Admin
- **Goal**: Mengelola operasional cuci mobil secara efisien
- **Pain Points**: Verifikasi manual, jadwal bentrok, komunikasi dengan customer
- **Needs**: Dashboard monitoring, verifikasi cepat, kelola jadwal, notifikasi otomatis

---

##  USER FLOWS

###  Customer Booking Flow

```
START → Home Page
  ↓
Login/Register (Custom Bootstrap UI dengan Glass Morphism)
  ↓
Form Pemesanan (Single Page)
  ├── Pilih Layanan (Dropdown)
  ├── Pilih Diskon (Dropdown, opsional)
  ├── Lihat Ringkasan Harga (Auto-calculate)
  ├── Pilih Jadwal & Waktu (Dropdown: "6 Jan 2025 (Senin) - 08:00-10:00 (3 slot)")
  └── Input Data (plat nomor, nama, HP)
  ↓
Halaman Pembayaran
  ├── Pilih Metode (Bank Transfer/E-Wallet)
  ├── Upload Bukti Transfer
  └── Tunggu Verifikasi
  ↓
Notifikasi Real-time (Bell Icon dengan Badge)
  ├── "Pembayaran Diverifikasi" → Status: Terkonfirmasi
  ├── "Status: Proses" → Mobil sedang dicuci
  └── "Status: Selesai" → Pesanan selesai
  ↓
Riwayat → Modal Detail & Modal Bukti Pembayaran
END
```

### 4.2 Admin Management Flow

```
START → Admin Login
  ↓
Dashboard → Overview Statistik + Quick Actions
  ↓
Profile Dropdown (Avatar + Name)
├── Profil Admin
├── Cek Halaman User
└── Logout Admin
  ↓
Pilih Menu:
├── Verifikasi (dari Dashboard Quick Action atau Navbar)
│   ├── Tab: Verifikasi Pembayaran
│   │   ├── Lihat Bukti (Modal Popup)
│   │   ├── Approve → Auto-notify Customer "Pembayaran Diverifikasi"
│   │   └── Reject → Auto-notify Customer "Pembayaran Ditolak"
│   └── Tab: Pengajuan Refund
│       ├── Approve → Auto-notify Customer "Refund Disetujui"
│       └── Reject → Auto-notify Customer "Refund Ditolak"
├── Kelola Proses
│   └── Update Status → Auto-notify Customer
│       ├── Menunggu → "Pesanan dalam antrian"
│       ├── Proses → "Mobil sedang dicuci"
│       └── Selesai → "Pesanan selesai"
├── Jadwal & Slot
│   ├── Tab: Hari Operasional (Set jam buka/tutup per hari)
│   └── Tab: Kelola Slot
│       ├── Input Date Range (Tanggal Mulai - Selesai)
│       ├── Input Multiple Time Slots (08:00-10:00, 10:00-12:00)
│       ├── View: Daftar Jadwal dengan tombol Edit/Delete
│       └── Generate Otomatis berdasarkan hari operasional
└── Layanan & Diskon
    ├── CRUD Layanan (nama, deskripsi, harga)
    └── CRUD Diskon (nama, persentase, tanggal expired)
END
```

---

## 5. TASK FLOWS

### 5.1 Task: "Customer Booking Layanan Cuci Mobil"

**Actor**: Customer  
**Goal**: Membuat pesanan cuci mobil dan mendapat konfirmasi  
**Precondition**: Customer memiliki akun atau akan register  
**Postcondition**: Pesanan dibuat, customer mendapat notifikasi real-time  

**Steps**:
1. Akses website → Home page
2. Klik "Pesan Sekarang" → Redirect ke login jika belum login
3. Login/Register → Custom Bootstrap UI dengan gradient background
4. Form pemesanan (single page):
   - Pilih jenis layanan → Dropdown dengan harga
   - Pilih diskon (opsional) → Dropdown conditional
   - Lihat ringkasan harga → Auto-calculate dengan diskon
   - Pilih jadwal & waktu → Dropdown "Tanggal (Hari) - Waktu (X slot tersisa)"
   - Input plat nomor → Required field
   - Input nama & HP → Optional fields
5. Klik "Buat Pesanan" → Redirect ke halaman pembayaran
6. Pilih metode pembayaran → Bank Transfer/E-Wallet
7. Upload bukti transfer → File upload dengan validasi
8. Tunggu verifikasi → Status "Pending Pembayaran"
9. Terima notifikasi real-time → Bell icon dengan badge counter
10. Cek riwayat → Modal detail pesanan dan modal bukti pembayaran

**Alternative Flows**:
- Jika slot penuh → Pilih waktu lain dari dropdown
- Jika tidak ada jadwal → Pesan "Admin belum buat jadwal"
- Jika upload gagal → Error message dengan retry option

### 5.2 Task: "Admin Verifikasi Pembayaran"

**Actor**: Admin  
**Goal**: Memverifikasi bukti pembayaran dan notify customer  
**Precondition**: Customer sudah upload bukti pembayaran  
**Postcondition**: Pembayaran terverifikasi, customer dapat notifikasi  

**Steps**:
1. Login admin → Dashboard
2. Klik "Verifikasi Sekarang" di card kuning atau navbar "Verifikasi"
3. Tab "Verifikasi Pembayaran" → List pending payments dalam tabel
4. Klik "Lihat Bukti" → Modal popup menampilkan gambar bukti
5. Periksa bukti transfer → Visual validation dalam modal
6. Klik "Verifikasi" atau "Tolak" → Konfirmasi action
7. Status berubah otomatis → "Terkonfirmasi" atau "Ditolak"
8. System auto-notify customer → Notifikasi real-time ke bell icon customer
9. Customer dapat notifikasi → "Pembayaran Diverifikasi" atau "Pembayaran Ditolak"

### 5.3 Task: "Admin Membuat Jadwal Slot"

**Actor**: Admin  
**Goal**: Membuat jadwal booking untuk beberapa hari sekaligus  
**Precondition**: Login sebagai admin  
**Postcondition**: Jadwal tersedia untuk customer booking  

**Steps**:
1. Menu "Jadwal & Slot" → Halaman kelola jadwal
2. Tab "Kelola Slot" → Form buat jadwal baru
3. Input tanggal mulai & selesai → Date range picker (contoh: 6-7 Jan)
4. Input kapasitas per slot → Number field (default: 4)
5. Input waktu slot pertama → Text field (format: 08:00-10:00)
6. Klik "Tambah Waktu" → Tambah multiple time slots
7. Input waktu slot tambahan → 10:00-12:00, 13:00-15:00, dst
8. Klik "Buat Jadwal" → Generate untuk semua tanggal dalam range
9. Jadwal muncul di daftar → Tabel dengan kolom tanggal, hari, jumlah slot
10. Aksi tersedia → Edit individual slot, Delete slot, Toggle aktif/nonaktif

**Alternative Flows**:
- Jika slot sudah ada → Konfirmasi replace atau skip
- Jika ada booking → Tidak bisa delete slot yang sudah terisi

---

## 6. FUNCTIONAL REQUIREMENTS

### 6.1 Customer Features
- **Authentication**: Custom login/register dengan Bootstrap glass morphism design
- **Booking System**: Single page form dengan dropdown jadwal & waktu
- **Payment**: Upload bukti dengan modal view, tracking status
- **History**: Modal detail pesanan, modal bukti pembayaran, request refund
- **Notifications**: Real-time bell icon, badge counter, dropdown preview, mark read, delete
- **Profile Management**: Edit profil, logout dalam dropdown dengan avatar

### 6.2 Admin Features
- **Dashboard**: Statistik cards, quick actions dengan link ke verifikasi
- **Profile Management**: Admin profile page, dropdown navigation, cek halaman user
- **Verification**: Tab-based interface, modal view bukti pembayaran, approve/reject dengan auto-notification
- **Order Management**: Update status pesanan dengan auto-notification ke customer
- **Schedule Management**: Date range creation, multiple time slots, edit/delete individual slots, hari operasional
- **Service Management**: CRUD layanan dengan harga dan deskripsi
- **Discount Management**: CRUD diskon dengan persentase dan expired date

---

## 7. TECHNICAL ARCHITECTURE

### 7.1 Frontend
- **Framework**: Laravel Blade Templates
- **CSS**: Bootstrap 5 dengan custom styling
- **Icons**: Bootstrap Icons
- **JavaScript**: Vanilla JS untuk real-time notifications dan modal interactions

### 7.2 Backend
- **Framework**: Laravel 8.x
- **Database**: SQLite
- **Authentication**: Laravel Breeze dengan custom UI
- **File Storage**: Local storage dengan symbolic link untuk payment proofs

### 7.3 Database Schema
```
users (id, name, email, password, is_admin, created_at, updated_at)
services (id, name, description, price, duration)
discounts (id, name, service_id, percent, expires_at, active)
booking_slots (id, tanggal, jam_mulai, jam_selesai, kapasitas, terisi, status, created_by)
orders (id, user_id, service_type, booking_date, license_plate, queue_number, status, payment_status, payment_proof, original_price, final_price, discount_percent, payment_method)
operational_days (id, hari, status_operasional, jam_buka, jam_tutup, created_by)
notifications (id, type, user_id, title, message, data, is_read, read_at, created_at, updated_at)
payment_methods (id, name, type, active)
```

---

## 8. NOTIFICATION SYSTEM

### 8.1 Real-time Notifications
- **Bell Icon**: Badge counter untuk unread notifications
- **Auto-refresh**: JavaScript polling setiap 30 detik
- **Dropdown Preview**: 5 notifikasi terbaru dengan scroll
- **Click Actions**: Mark as read, delete, view all

### 8.2 Notification Triggers
- **Payment Verified**: Admin approve → "Pembayaran telah diverifikasi"
- **Payment Rejected**: Admin reject → "Pembayaran ditolak, upload ulang"
- **Status Update**: Admin ubah status → "Pesanan sedang diproses/selesai"
- **Refund Approved**: Admin setujui → "Refund disetujui, dana dikembalikan"
- **Refund Rejected**: Admin tolak → "Refund ditolak + alasan"

### 8.3 Notification Management
- **Privacy**: Hanya customer pemilik pesanan yang dapat notifikasi
- **Persistence**: Tersimpan di database dengan timestamp
- **User Control**: Customer bisa mark as read dan delete notifikasi
- **Responsive**: Dropdown dan halaman notifications mobile-friendly

---

## 9. UI/UX DESIGN PRINCIPLES

### 9.1 Authentication Design
- **Glass Morphism**: Transparent card dengan blur effect
- **Gradient Background**: Blue-purple gradient untuk visual appeal
- **Bootstrap 5**: Consistent form styling dengan icons
- **Responsive**: Mobile-first design approach

### 9.2 Navigation Design
- **Profile Dropdown**: Avatar circle dengan nama user
- **Notification Bell**: Badge counter dengan smooth animations
- **Modal Interactions**: Fade in/out untuk payment proof dan notifications
- **Tab Navigation**: Clear separation untuk admin features

### 9.3 Usability Principles
- **Simple Navigation**: Max 3 clicks untuk complete task
- **Clear CTAs**: Action buttons dengan descriptive text
- **Real-time Feedback**: Loading states dan success/error messages
- **Consistent Styling**: Bootstrap color system dan spacing

---

## 10. USER JOURNEY MAPPING

### 10.1 Customer Journey: First Time Booking
```
Awareness → Interest → Registration → Booking → Payment → Tracking → Completion
    ↓         ↓           ↓           ↓         ↓         ↓          ↓
  Home     Layanan    Register    Form      Upload    Notif     Riwayat
   ↓         ↓           ↓        Booking    Bukti     Bell       Modal
"Butuh    "Lihat     "Daftar    "Pilih     "Bayar    "Cek      "Lihat
 cuci     harga &     akun       waktu      via       status    detail
 mobil"   layanan"    baru"      cocok"     bank"     real-time" pesanan"
```

### 10.2 Admin Journey: Daily Operations
```
Login → Dashboard → Verify → Update → Schedule → Monitor
  ↓        ↓         ↓        ↓        ↓         ↓
Admin    Stats     Modal    Status   Date      Real-time
Panel    Cards     Bukti    Orders   Range     Updates
  ↓        ↓         ↓        ↓        ↓         ↓
"Cek     "Lihat    "Approve "Update  "Buat     "Monitor
 sistem   metrics   bayar    ke       jadwal    customer
 hari     harian"   customer" Selesai" minggu"   satisfaction"
 ini"
```

---

## 11. WIREFRAME STRUCTURE

### 11.1 Customer Pages
```
LOGIN/REGISTER PAGE
├── Glass Morphism Card
├── Gradient Background
├── Form dengan Icons
└── Navigation Links

BOOKING FORM
├── Service Dropdown
├── Discount Dropdown (conditional)
├── Price Summary Card
├── Schedule Dropdown (formatted)
├── Customer Info Fields
└── Submit Button

NOTIFICATIONS
├── Bell Icon dengan Badge
├── Dropdown Preview (5 items)
├── Full Page View
└── Management Actions
```

### 11.2 Admin Pages
```
DASHBOARD
├── Profile Dropdown
├── Statistics Cards (4 metrics)
├── Payment Status Cards
├── Quick Actions
└── System Summary

VERIFICATION
├── Tab Navigation
├── Pending Payments Table
├── Modal Bukti Pembayaran
├── Action Buttons
└── Auto-notification System

SCHEDULE MANAGEMENT
├── Tab Navigation (Operational/Slots)
├── Date Range Form
├── Multiple Time Slots Input
├── Schedule List dengan Actions
└── Edit/Delete Individual Slots
```

---

## 12. INTERACTION DESIGN

### 12.1 Micro-interactions
- **Button Hover**: Color change dengan smooth transition
- **Form Validation**: Real-time feedback dengan error messages
- **Loading States**: Spinner dengan descriptive text
- **Success Actions**: Green checkmark dengan fade animation
- **Notification Bell**: Badge bounce animation untuk new notifications
- **Modal Animations**: Smooth fade in/out transitions
- **Profile Dropdown**: Smooth toggle dengan avatar highlight

### 12.2 Navigation Patterns
- **Tab Navigation**: Clear active states untuk admin features
- **Dropdown Menus**: Hover states dan smooth animations
- **Modal Dialogs**: Backdrop blur untuk focus
- **Real-time Updates**: Smooth counter updates dan badge animations

---

## 13. CONTENT STRATEGY

### 13.1 Tone of Voice
- **Friendly**: Casual namun profesional dalam komunikasi
- **Clear**: Bahasa sederhana, hindari jargon teknis
- **Helpful**: Guidance dan tips di setiap langkah
- **Trustworthy**: Transparansi dalam harga dan proses

### 13.2 Microcopy Examples
- **CTA Buttons**: "Pesan Sekarang", "Lihat Bukti", "Verifikasi Pembayaran"
- **Status Labels**: "Menunggu", "Proses", "Selesai", "Terkonfirmasi"
- **Help Text**: "Pilih tanggal dalam 14 hari ke depan", "Format: 08:00-10:00"
- **Error Messages**: "Slot yang dipilih sudah penuh", "File harus berformat JPG/PNG"
- **Notifications**: "Pembayaran telah diverifikasi", "Pesanan sedang diproses"
- **Success Messages**: "Jadwal berhasil dibuat", "Profil berhasil diperbarui"

---

## 14. METRICS & KPIs

### 14.1 User Experience Metrics
- **Task Completion Rate**: Persentase booking yang berhasil diselesaikan
- **Time to Complete**: Rata-rata waktu untuk menyelesaikan booking
- **Error Rate**: Jumlah failed form submissions
- **User Satisfaction**: Feedback rating setelah booking
- **Notification Engagement**: Click-through rate pada notifications

### 14.2 Business Metrics
- **Conversion Rate**: Visitors yang menjadi paying customers
- **Payment Verification Time**: Efisiensi admin dalam verifikasi
- **Slot Utilization**: Optimalisasi kapasitas booking slots
- **Customer Retention**: Repeat bookings dari existing customers
- **Admin Productivity**: Tasks completed per session

### 14.3 Technical Metrics
- **Page Load Time**: Kecepatan loading halaman
- **API Response Time**: Kecepatan real-time notifications
- **Error Rate**: System errors dan downtime
- **Mobile Usage**: Persentase akses dari mobile devices

---

## 15. IMPLEMENTATION SUMMARY

### 15.1 Key Features Implemented
- **Custom Authentication UI**: Bootstrap 5 dengan glass morphism design
- **Real-time Notification System**: Bell icon dengan auto-refresh setiap 30 detik
- **Modal-based Interactions**: Payment proof viewing, notification management
- **Enhanced Profile Management**: Dropdown navigation dengan avatar
- **Advanced Schedule Management**: Date range creation, individual slot editing
- **Responsive Design**: Mobile-first approach dengan Bootstrap grid

### 15.2 Technical Achievements
- **Single Table Authentication**: Users table dengan role-based access (is_admin)
- **Secure File Upload**: Payment proof dengan protected access routes
- **Real-time Updates**: JavaScript polling untuk notification updates
- **Modal Management**: Bootstrap 5 modal system untuk seamless interactions
- **Form Validation**: Client-side dan server-side validation
- **Database Optimization**: Efficient queries dengan proper relationships

---

Dokumentasi ini siap digunakan untuk:
1. **Site Map Creation**: Struktur navigasi yang comprehensive
2. **User Flow Diagrams**: Visual representation dari actual implemented flows
3. **Wireframe Development**: Layout berdasarkan real functional requirements
4. **Prototype Testing**: Validation dengan implemented user journey
5. **UI Design Guidelines**: Design principles yang sudah diterapkan
6. **Technical Documentation**: Architecture dan implementation details