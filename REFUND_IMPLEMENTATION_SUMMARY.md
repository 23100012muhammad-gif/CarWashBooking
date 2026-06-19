# Refund and History Management Implementation

## Overview
This document outlines the implementation of refund functionality and history management for the Car Wash Booking System.

## Database Changes Required

### 1. Add Refund Fields to Orders Table
Run the following SQL commands to add refund-related fields:

```sql
ALTER TABLE orders ADD COLUMN refund_reason TEXT;
ALTER TABLE orders ADD COLUMN refund_requested_at DATETIME;
ALTER TABLE orders ADD COLUMN refund_processed_at DATETIME;
ALTER TABLE orders ADD COLUMN refund_processed_by INTEGER;
ALTER TABLE orders ADD COLUMN refund_notes TEXT;
ALTER TABLE orders ADD COLUMN hidden_from_user BOOLEAN DEFAULT 0;
```

## Files Modified

### 1. Order Model (`app/Models/Order.php`)
- Added refund-related fields to fillable array
- Added casts for datetime fields
- Added relationship for refund processor
- Added helper methods:
  - `canRequestRefund()` - Check if order can request refund
  - `canDeleteFromHistory()` - Check if order can be deleted from user history
  - `hasRefundRequest()` - Check if refund has been requested
  - `isRefundProcessed()` - Check if refund has been processed

### 2. BookingController (`app/Http/Controllers/BookingController.php`)
- Modified `history()` method to filter out hidden orders
- Modified `deleteOrder()` method to hide orders instead of deleting
- Added `requestRefund()` method for handling refund requests

### 3. AdminController (`app/Http/Controllers/AdminController.php`)
- Modified `pendingPayments()` to include refund requests
- Modified `deleteOrder()` to allow deletion of completed/refunded orders
- Added `processRefund()` method for admin refund processing
- Updated dashboard to include refund requests count

### 4. Routes (`routes/web.php`)
- Added route for user refund requests: `POST /riwayat/{order}/refund`
- Added route for admin refund processing: `POST /refund/{order}/process`

### 5. History View (`resources/views/history.blade.php`)
- Added "Ajukan Refund" button for eligible orders
- Added "Hapus Riwayat" button for completed orders
- Added refund request modal
- Updated delete confirmation message

### 6. Admin Pending Payments View (`resources/views/admin/pending-payments.blade.php`)
- Added refund requests section
- Added refund processing modal
- Removed delete button from payment verification section

### 7. Admin Orders View (`resources/views/admin/orders.blade.php`)
- Updated delete button visibility conditions
- Added "Refund" and "Batal" status options
- Added status badges for refund/cancelled orders

## Functionality Overview

### User Features
1. **Refund Request**: Users can request refunds for orders with "Menunggu Pembayaran" status
2. **History Management**: Users can hide completed orders from their history view
3. **Status Tracking**: Clear visibility of order and payment status

### Admin Features
1. **Refund Processing**: Admins can approve/reject refund requests
2. **Order Management**: Enhanced order management with refund status tracking
3. **Data Integrity**: Orders are hidden from user view but preserved in backend

### Security Features
1. **Data Preservation**: Orders are never actually deleted, only hidden from user view
2. **Admin Control**: Only admins can process refunds and permanently delete orders
3. **Status Validation**: Proper validation of order status before allowing actions

## Status Flow
1. **Pending Pembayaran** → User can request refund
2. **Refund Requested** → Admin can approve/reject
3. **Refund Approved** → Status becomes "Refund", user can hide from history
4. **Refund Rejected** → Status becomes "Batal", user can hide from history
5. **Terkonfirmasi/Selesai** → User can hide from history directly

## Implementation Notes
- All code uses PHP 7.4 compatible syntax
- Database changes preserve existing data
- User interface is intuitive with clear action buttons
- Admin interface provides comprehensive refund management
- Backend maintains data integrity while providing user privacy