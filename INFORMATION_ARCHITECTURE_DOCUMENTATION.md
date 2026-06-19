# CarWash Connect - Information Architecture & User Flow Documentation

## 1. OVERVIEW SISTEM

**CarWash Connect** adalah sistem web booking layanan cuci mobil dengan fitur:
- Pemesanan online dengan slot waktu
- Sistem pembayaran dan verifikasi
- Manajemen jadwal oleh admin
- Tracking status pesanan real-time

---

## 2. SITE MAP (Information Architecture)

```
CarWash Connect
├── PUBLIC AREA
│   ├── Home (/)
│   ├── Layanan (/layanan)
│   ├── Pemesanan (/pesan/create)
│   ├── Status Pesanan (/status-pesanan)
│   ├── Riwayat (/riwayat)
│   └── Pembayaran
│       ├── Konfirmasi (/payment/confirmation/{id})
│       ├── Bank Transfer (/payment/bank-transfer/{id})
│       ├── E-Wallet (/payment/ewallet/{id})
│       └── Status (/payment/status/{id})
│
├── USER AREA (Auth Required)
│   ├── Login (/login) - Custom Bootstrap Design
│   ├── Register (/register) - Custom Bootstrap Design
│   ├── Profil (/profil)
│   │   ├── Edit Profile
│   │   └── Logout (dalam profil)
│   └── Notifikasi (/notifications)
│       ├── Real-time Notification Bell
│       ├── Dropdown Preview (5 recent)
│       ├── Mark as Read
│       └── Delete Notifications
│
└── ADMIN AREA (/admin)
    ├── Login (/admin/login)
    ├── Dashboard (/admin/dashboard)
    ├── Profil Admin (/admin/profile)
    │   ├── Edit Admin Profile
    │   ├── Logout Admin
    │   └── Cek Halaman User
    ├── Verifikasi (/admin/verifications)
    │   ├── Verifikasi Pembayaran (Modal View)
    │   └── Pengajuan Refund
    ├── Kelola Proses (/admin/orders)
    ├── Jadwal & Slot (/admin/jadwal-slot)
    │   ├── Hari Operasional
    │   ├── Kelola Slot (Edit/Delete)
    │   ├── Date Range Creation
    │   └── Generate Otomatis
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
- **Goal**: Booking layanan cuci mobil dengan mudah
- **Pain Points**: Antrian panjang, tidak tahu jadwal kosong
- **Needs**: Booking online, pilih waktu, tracking status

### 3.2 Admin
- **Goal**: Mengelola operasional cuci mobil efisien
- **Pain Points**: Verifikasi manual, jadwal bentrok
- **Needs**: Dashboard monitoring, verifikasi cepat, kelola jadwal

---

## 4. USER FLOWS

### 4.1 Customer Booking Flow

```
START → Home Page
  ↓
Login/Register (Custom Bootstrap UI)
  ↓
Pilih "Pesan Sekarang" atau "Layanan"
  ↓
Form Pemesanan (Single Page)
  ├── Pilih Layanan (Dropdown)
  ├── Pilih Diskon (Dropdown, opsional)
  ├── Lihat Ringkasan Harga (Auto-calculate)
  ├── Pilih Jadwal & Waktu (Dropdown: "Tanggal - Waktu (X slot)")
  └── Input Data (plat, nama, HP)
  ↓
Halaman Pembayaran
  ├── Pilih Metode (Bank Transfer/E-Wallet)
  ├── Upload Bukti Transfer
  └── Tunggu Verifikasi
  ↓
Notifikasi Real-time (Bell Icon)
  ├── "Pembayaran Diverifikasi"
  ├── "Status: Proses"
  └── "Status: Selesai"
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
├── Verifikasi (dari Dashboard atau Navbar)
│   ├── Tab: Verifikasi Pembayaran
│   │   ├── Lihat Bukti (Modal Popup)
│   │   ├── Approve → Auto-notify Customer
│   │   └── Reject → Auto-notify Customer
│   └── Tab: Pengajuan Refund
│       ├── Approve → Auto-notify Customer
│       └── Reject → Auto-notify Customer
├── Kelola Proses
│   └── Update Status → Auto-notify Customer
│       ├── Menunggu → "Pesanan dalam antrian"
│       ├── Proses → "Mobil sedang dicuci"
│       └── Selesai → "Pesanan selesai"
├── Jadwal & Slot
│   ├── Tab: Hari Operasional (Set jam buka/tutup)
│   └── Tab: Kelola Slot
│       ├── Input Date Range (Mulai-Selesai)
│       ├── Input Multiple Time Slots
│       ├── View: Daftar Jadwal dengan Edit/Delete
│       └── Generate Otomatis
└── Layanan & Diskon
    ├── CRUD Layanan
    └── CRUD Diskon
END
```

---

## 5. TASK FLOWS

### 5.1 Task: "Booking Layanan Cuci Mobil"

**Actor**: Customer  
**Goal**: Membuat pesanan cuci mobil  
**Precondition**: -  
**Postcondition**: Pesanan dibuat, menunggu pembayaran  

**Steps**:
1. Akses website → Home page
2. Klik "Pesan Sekarang" → Form pemesanan
3. Pilih jenis layanan → Dropdown layanan
4. Pilih diskon (opsional) → Dropdown diskon
5. Lihat ringkasan harga → Auto-calculate
6. Pilih jadwal & waktu → Dropdown dengan format "Tanggal - Waktu (X slot)"
7. Input plat nomor → Text field (required)
8. Input nama & HP → Text field (optional)
9. Klik "Buat Pesanan" → Redirect ke pembayaran
10. Pilih metode pembayaran → Bank Transfer/E-Wallet
11. Upload bukti transfer → File upload
12. Pesanan selesai dibuat → Status "Pending Pembayaran"

**Alternative Flows**:
- Jika slot penuh → Pilih waktu lain
- Jika tidak ada jadwal → Admin belum buat jadwal

### 5.2 Task: "Verifikasi Pembayaran"

**Actor**: Admin  
**Goal**: Memverifikasi bukti pembayaran customer  
**Precondition**: Customer sudah upload bukti  
**Postcondition**: Pembayaran terverifikasi/ditolak  

**Steps**:
1. Login admin → Dashboard
2. Klik "Verifikasi" di navbar → Halaman verifikasi
3. Tab "Verifikasi Pembayaran" → List pending payments
4. Klik "Lihat Bukti" → Modal popup gambar
5. Periksa bukti transfer → Visual validation
6. Klik "Verifikasi" atau "Tolak" → Konfirmasi
7. Status berubah → "Terkonfirmasi" atau "Ditolak"
8. Customer dapat notifikasi → Auto-notification

### 5.3 Task: "Membuat Jadwal Slot"

**Actor**: Admin  
**Goal**: Membuat jadwal booking untuk customer  
**Precondition**: Login sebagai admin  
**Postcondition**: Jadwal tersedia untuk booking  

**Steps**:
1. Menu "Jadwal & Slot" → Halaman kelola jadwal
2. Tab "Hari Operasional" → Set jam buka/tutup
3. Tab "Kelola Slot" → Form buat jadwal
4. Input tanggal mulai & selesai → Date range picker
5. Input kapasitas per slot → Number field
6. Input waktu slot → Text field (format: 08:00-10:00)
7. Klik "Tambah Waktu" → Multiple time slots
8. Klik "Buat Jadwal" → Generate untuk semua tanggal
9. Jadwal muncul di daftar → Dapat diedit/hapus

---

## 6. FUNCTIONAL REQUIREMENTS

### 6.1 Customer Features
- **Authentication**: Custom login/register dengan Bootstrap design
- **Booking System**: Pilih layanan, jadwal dropdown, input data
- **Payment**: Upload bukti, modal view, tracking status
- **History**: Lihat riwayat, detail modal, bukti pembayaran modal, refund
- **Notifications**: Real-time bell icon, dropdown preview, mark read, delete
- **Profile Management**: Edit profil, logout dalam dropdown

### 6.2 Admin Features
- **Dashboard**: Statistik, quick actions, link ke verifikasi
- **Profile Management**: Admin profile, edit, logout dalam dropdown
- **Verification**: Modal view bukti pembayaran, approve/reject pembayaran & refund
- **Order Management**: Update status pesanan dengan auto-notification
- **Schedule Management**: CRUD jadwal & slot, date range, edit/delete individual slots
- **Service Management**: CRUD layanan & diskon
- **Navigation**: Profile dropdown, cek halaman user

---

## 7. TECHNICAL ARCHITECTURE

### 7.1 Frontend
- **Framework**: Laravel Blade Templates
- **CSS**: Bootstrap 5
- **Icons**: Bootstrap Icons
- **JavaScript**: Vanilla JS + Bootstrap JS

### 7.2 Backend
- **Framework**: Laravel 8.x
- **Database**: SQLite
- **Authentication**: Laravel Breeze
- **File Storage**: Local storage dengan symbolic link

### 7.3 Database Schema
```
users (id, name, email, password, is_admin, created_at, updated_at)
services (id, name, description, price, duration)
discounts (id, name, service_id, percent, expires_at, active)
booking_slots (id, tanggal, jam_mulai, jam_selesai, kapasitas, terisi, status, created_by)
orders (id, user_id, service_type, booking_date, license_plate, queue_number, status, payment_status, payment_proof, original_price, final_price, discount_percent, payment_method, payment_verified_at)
operational_days (id, hari, status_operasional, jam_buka, jam_tutup, created_by)
notifications (id, type, user_id, title, message, data, is_read, read_at, created_at, updated_at)
payment_methods (id, name, type, active)
```

---

## 8. UI/UX DESIGN PRINCIPLES

### 8.1 Usability
- **Simple Navigation**: Max 3 clicks to complete task
- **Clear CTAs**: Prominent buttons dengan action words
- **Responsive Design**: Mobile-first approach
- **Feedback**: Loading states, success/error messages

### 8.2 Accessibility
- **Color Contrast**: Bootstrap color system
- **Icons**: Meaningful icons dengan text labels
- **Forms**: Clear labels, validation messages
- **Modal**: Keyboard navigation support

### 8.3 Performance
- **Fast Loading**: Minimal external dependencies
- **Efficient Queries**: Optimized database calls
- **Image Optimization**: Proper file handling
- **Caching**: Laravel built-in caching

---

## 9. USER JOURNEY MAPPING

### 9.1 Customer Journey: First Time Booking
```
Awareness → Interest → Consideration → Purchase → Retention
    ↓         ↓           ↓            ↓          ↓
  Home     Layanan    Form Booking   Payment   Riwayat
   ↓         ↓           ↓            ↓          ↓
"Butuh    "Lihat     "Pilih waktu   "Upload   "Booking
 cuci     harga &     yang cocok"   bukti"    lagi"
 mobil"   layanan"
```

### 9.2 Admin Journey: Daily Operations
```
Morning Check → Verify Payments → Manage Orders → Update Schedule
      ↓              ↓               ↓              ↓
   Dashboard    Verifikasi      Kelola Proses   Jadwal & Slot
      ↓              ↓               ↓              ↓
  "Cek status   "Approve       "Update ke      "Buat jadwal
   hari ini"     pembayaran"    Proses/Selesai"  besok"
```

---

## 10. WIREFRAME STRUCTURE

### 10.1 Customer Pages
```
HOME
├── Header (Logo, Navigation)
├── Hero Section (CTA: Pesan Sekarang)
├── Services Preview
└── Footer

BOOKING FORM
├── Service Selection (Dropdown)
├── Discount Selection (Conditional)
├── Price Summary (Auto-calculate)
├── Schedule Selection (Dropdown)
├── Customer Info (Form fields)
└── Submit Button

PAYMENT
├── Order Summary
├── Payment Method Selection
├── Upload Proof Section
└── Confirmation
```

### 10.2 Admin Pages
```
DASHBOARD
├── Statistics Cards (4 metrics)
├── Payment Status Cards
├── Quick Actions (3 buttons)
└── System Summary

VERIFICATION
├── Tab Navigation (Payment/Refund)
├── Pending List (Table)
├── Action Buttons (Approve/Reject)
└── Payment Proof Modal

SCHEDULE MANAGEMENT
├── Tab Navigation (Operational/Slots)
├── Operational Hours Form
├── Schedule Creation Form
└── Schedule List with Actions
```

---

## 11. INTERACTION DESIGN

### 11.1 Micro-interactions
- **Button Hover**: Color change + slight scale
- **Form Validation**: Real-time feedback
- **Loading States**: Spinner + descriptive text
- **Success Actions**: Green checkmark + message
- **Notification Bell**: Badge counter + dropdown animation
- **Modal Animations**: Fade in/out transitions
- **Profile Dropdown**: Smooth toggle with avatar

### 11.2 Navigation Patterns
- **Breadcrumbs**: Admin pages untuk context
- **Tab Navigation**: Grouping related content
- **Dropdown Menus**: Space-efficient selections + profile management
- **Modal Dialogs**: Non-disruptive actions (payment proof, notifications)
- **Real-time Updates**: Auto-refresh notification count

---

## 12. NOTIFICATION SYSTEM

### 12.1 Real-time Notifications
- **Bell Icon**: Badge counter untuk unread notifications
- **Dropdown Preview**: 5 notifikasi terbaru
- **Auto-refresh**: Check setiap 30 detik
- **Click Actions**: Mark as read, delete, view all

### 12.2 Notification Triggers
- **Payment Verified**: Admin verifikasi → Customer notified
- **Payment Rejected**: Admin tolak → Customer notified
- **Status Update**: Admin ubah status → Customer notified
- **Refund Approved**: Admin setujui → Customer notified
- **Refund Rejected**: Admin tolak → Customer notified

### 12.3 Notification Management
- **Privacy**: Hanya customer pemilik pesanan yang dapat notifikasi
- **Persistence**: Tersimpan di database
- **User Control**: Customer bisa hapus notifikasi
- **Responsive**: Modal view untuk mobile

---

## 13. ENHANCED USER EXPERIENCE

### 13.1 Authentication Flow
```
Custom Login/Register Pages
├── Glass Morphism Design
├── Gradient Background
├── Bootstrap 5 Styling
├── Icon Integration
└── Responsive Layout
```

### 13.2 Profile Management
```
Customer Profile
├── Avatar Circle
├── Profile Dropdown
├── Edit Information
└── Logout Integration

Admin Profile
├── Dark Theme Avatar
├── Admin Profile Page
├── Edit Admin Info
└── Navigation Integration
```

### 13.3 Modal Interactions
```
Payment Proof Modal
├── Image Display
├── Responsive Sizing
├── Close Animation
└── No Tab Opening

Notification Modal
├── Real-time Loading
├── Mark as Read
├── Delete Function
└── Smooth Transitions
```

---

## 14. CONTENT STRATEGY

### 14.1 Tone of Voice
- **Friendly**: Casual tapi profesional
- **Clear**: Bahasa sederhana, tidak teknis
- **Helpful**: Guidance di setiap step
- **Trustworthy**: Transparansi harga & proses

### 14.2 Microcopy Examples
- **CTA Buttons**: "Pesan Sekarang", "Lihat Bukti", "Verifikasi"
- **Status Labels**: "Menunggu", "Proses", "Selesai"
- **Help Text**: "Pilih tanggal dalam 14 hari ke depan"
- **Error Messages**: "Slot yang dipilih sudah penuh"
- **Notifications**: "Pembayaran telah diverifikasi", "Pesanan sedang diproses"

---

## 15. METRICS & KPIs

### 15.1 User Experience Metrics
- **Task Completion Rate**: % successful bookings
- **Time to Complete**: Average booking duration
- **Error Rate**: Failed form submissions
- **User Satisfaction**: Post-booking feedback
- **Notification Engagement**: Click-through rate

### 15.2 Business Metrics
- **Conversion Rate**: Visitors → Bookings
- **Payment Verification Time**: Admin efficiency
- **Slot Utilization**: Capacity optimization
- **Customer Retention**: Repeat bookings
- **Admin Productivity**: Tasks completed per sessioneckmark + message
- **Modal Animations**: Fade in/out transitions

### 11.2 Navigation Patterns
- **Breadcrumbs**: Admin pages untuk context
- **Tab Navigation**: Grouping related content
- **Dropdown Menus**: Space-efficient selections
- **Modal Dialogs**: Non-disruptive actions

---

## 12. CONTENT STRATEGY

### 12.1 Tone of Voice
- **Friendly**: Casual tapi profesional
- **Clear**: Bahasa sederhana, tidak teknis
- **Helpful**: Guidance di setiap step
- **Trustworthy**: Transparansi harga & proses

### 12.2 Microcopy Examples
- **CTA Buttons**: "Pesan Sekarang", "Lihat Bukti", "Verifikasi"
- **Status Labels**: "Menunggu", "Proses", "Selesai"
- **Help Text**: "Pilih tanggal dalam 14 hari ke depan"
- **Error Messages**: "Slot yang dipilih sudah penuh"

---

## 13. METRICS & KPIs

### 13.1 User Experience Metrics
- **Task Completion Rate**: % successful bookings
- **Time to Complete**: Average booking duration
- **Error Rate**: Failed form submissions
- **User Satisfaction**: Post-booking feedback

### 13.2 Business Metrics
- **Conversion Rate**: Visitors → Bookings
- **Payment Verification Time**: Admin efficiency
- **Slot Utilization**: Capacity optimization
- **Customer Retention**: Repeat bookings

---

## 16. IMPLEMENTATION HIGHLIGHTS

### 16.1 Recent Enhancements
- **Custom Authentication UI**: Bootstrap 5 dengan glass morphism
- **Real-time Notification System**: Bell icon dengan auto-refresh
- **Modal-based Interactions**: Payment proof, notifications
- **Enhanced Profile Management**: Dropdown navigation
- **Admin Schedule Management**: Date range, edit/delete slots
- **Responsive Design**: Mobile-first approach

### 16.2 Technical Achievements
- **Single Table Authentication**: Users dengan role-based access
- **File Upload System**: Payment proof dengan secure access
- **Real-time Updates**: JavaScript polling untuk notifications
- **Modal Management**: Bootstrap 5 modal system
- **Form Validation**: Client-side dan server-side
- **Database Optimization**: Efficient queries dengan proper indexing

---

Dokumentasi ini dapat digunakan untuk:
1. **Site Map Creation**: Struktur navigasi yang jelas dengan fitur terbaru
2. **User Flow Diagrams**: Visual representation dari task flows yang sudah enhanced
3. **Wireframe Development**: Layout berdasarkan functional requirements yang updated
4. **Prototype Testing**: Validation dengan user journey mapping yang comprehensive
5. **UI Design**: Menggunakan design principles dan interaction patterns yang sudah implemented
6. **Notification System Design**: Real-time communication flow
7. **Authentication Flow**: Custom login/register experience
8. **Modal Interaction Design**: Non-disruptive user actions