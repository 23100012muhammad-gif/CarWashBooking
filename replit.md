# Car Wash Booking System - Replit Documentation

## Overview

This is a web-based car wash booking system built with Laravel 8.x, specifically designed for PHP 7.4 compatibility. The application enables customers to book car wash services with automatic queue number generation and allows administrators to manage and track order statuses through a dedicated admin panel.

The system offers four distinct car wash services ranging from basic exterior washing to premium salon services, with transparent pricing and real-time order tracking capabilities.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Framework & Core Technology
- **Framework**: Laravel 8.x (PHP 7.4/8.1 compatible)
- **PHP Version**: 8.1 (using PHP 7.4-compatible syntax, no PHP 8.0+ specific features)
- **Database**: SQLite (default, file-based for simplicity)
- **Template Engine**: Blade (Laravel's native templating)
- **Frontend Framework**: Bootstrap 5 for responsive UI components

**Rationale**: Laravel 8.x was chosen as it supports both PHP 7.4 and 8.1. While PHP 7.4 was the original target, the Replit environment only provides PHP 8.1+, so we use PHP 8.1 with strict adherence to PHP 7.4-compatible syntax (no named arguments, constructor property promotion, union types, nullsafe operator, or match expressions). SQLite provides a lightweight, zero-configuration database solution ideal for small to medium-scale car wash operations without requiring separate database server setup.

### Application Structure

#### MVC Pattern
- **Models**: `Service` (car wash service types), `Order` (customer bookings)
- **Controllers**: 
  - `BookingController` - Handles customer booking operations
  - `UserController` - Manages user profile operations
  - `AdminController` - Manages admin dashboard and order processing
- **Views**: Blade templates organized into user-facing pages and admin panel layouts

**Rationale**: Standard Laravel MVC architecture provides clear separation of concerns and follows framework conventions for maintainability.

#### Database Schema Design

**Services Table**:
- Stores predefined car wash service types
- Fields: `name`, `description`, `price`
- Seeded with 4 core services: Cuci Luar (Rp 35,000), Cuci Dalam (Rp 50,000), Cuci Full (Rp 75,000), Salon Mobil (Rp 150,000)

**Orders Table**:
- Tracks all customer bookings
- Fields: `user_id`, `service_type`, `booking_date`, `license_plate`, `queue_number`, `status`
- Status workflow: 'Menunggu' (Waiting) → 'Proses' (Processing) → 'Selesai' (Completed)
- `queue_number` is auto-generated to manage customer flow

**Rationale**: Simple relational structure with automatic queue management eliminates manual queue tracking and ensures fair service ordering.

### Routing Architecture

**User Routes** (Public-facing):
- `/` - Home page
- `/layanan` - Service listing with pricing
- `/pesan/create` - Booking form
- `/pesan/store` - Process booking submission
- `/status-pesanan` - Active order status
- `/riwayat` - Order history (completed orders)
- `/profil` - User profile management

**Admin Routes** (Prefix: `/admin`):
- `/admin/dashboard` - Statistical overview
- `/admin/orders` - Order management (sorted by queue_number)
- `/admin/orders/{id}/update` - Status update endpoint

**Rationale**: Clear route separation between user and admin interfaces with RESTful naming conventions. Admin routes are prefixed for easy middleware protection and logical organization.

### Frontend Architecture

**Layout System**:
- `layouts/app.blade.php` - Main user layout with Bootstrap CDN
- `layouts/admin_master.blade.php` - Admin panel layout

**Reusable Components** (Partials):
- `partials/navbar.blade.php` - Navigation bar (Bootstrap navbar classes)
- `partials/status_card.blade.php` - Status display component (Bootstrap card)
- `partials/footer.blade.php` - Footer component

**Page Views**:
- `home.blade.php` - Landing page
- `layanan.blade.php` - Service catalog (Bootstrap table/card layout)
- `booking_form.blade.php` - Booking form (Bootstrap form-control classes)
- `status.blade.php` - Order status display
- `admin/orders.blade.php` - Admin order management interface

**Rationale**: Component-based Blade structure promotes code reusability. Bootstrap 5 provides responsive, mobile-first styling out of the box with minimal custom CSS required.

### Key Business Logic

**Queue Number Generation**:
- Automatic sequential numbering system
- Implemented in `BookingController@store`
- Ensures orderly service delivery and prevents queue conflicts

**Status Management**:
- Three-state workflow prevents status regression
- Admin-controlled progression through booking lifecycle
- Provides transparency to customers on service progress

**Rationale**: Automated queue management reduces human error and provides structured workflow. The three-state status system is simple enough for small operations while providing adequate tracking granularity.

## External Dependencies

### PHP Dependencies (via Composer)
- **laravel/framework**: ^8.83 - Core framework
- **doctrine/inflector**: String manipulation for pluralization
- **dragonmantank/cron-expression**: Scheduled task parsing
- **egulias/email-validator**: Email validation
- **league/commonmark**: Markdown parsing
- **league/flysystem**: Filesystem abstraction
- **monolog/monolog**: Logging library
- **nesbot/carbon**: DateTime manipulation
- **opis/closure**: Closure serialization
- **ramsey/uuid**: UUID generation
- **symfony/console**: CLI component
- **symfony/http-foundation**: HTTP abstraction
- **vlucas/phpdotenv**: Environment variable management

All dependencies are locked to versions compatible with PHP 7.4 to maintain system compatibility.

### Frontend Dependencies (CDN)
- **Bootstrap 5**: CSS framework for responsive design and UI components
- **Bootstrap Icons**: Icon library for visual elements

**Rationale**: CDN delivery reduces application bundle size and leverages browser caching. Bootstrap Icons integrate seamlessly with Bootstrap's design system.

### Development Tools
- **Composer**: PHP dependency management
- **SQLite**: Embedded database (no external database server required)
- **Artisan**: Laravel's command-line interface for migrations, seeding, and development tasks

**Note**: The application uses SQLite by default for simplicity, but the database layer is abstracted through Laravel's Eloquent ORM, making it straightforward to migrate to MySQL or PostgreSQL if scaling requirements change.