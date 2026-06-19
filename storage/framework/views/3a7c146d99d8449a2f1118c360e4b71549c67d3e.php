<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Car Wash Booking'); ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo e(route('home')); ?>">
                <i class="bi bi-car-front"></i> CarWash Connect
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('home')); ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('services')); ?>">Layanan</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('booking.status')); ?>">Status</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('booking.history')); ?>">Riwayat</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if(auth()->guard()->check()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notif-count" style="display: none;">0</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" style="width: 300px; max-height: 400px; overflow-y: auto;">
                                <li><h6 class="dropdown-header">Notifikasi</h6></li>
                                <div id="notification-list">
                                    <li><span class="dropdown-item-text text-muted">Memuat notifikasi...</span></li>
                                </div>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center" href="<?php echo e(route('notifications.index')); ?>">Lihat Semua</a></li>
                            </ul>
                        </li>
                        <?php if(Auth::user()->is_admin || in_array(Auth::user()->email, ['admin@carwash.com', 'admin@example.com'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('admin.dashboard')); ?>">
                                <i class="bi bi-gear"></i> Admin
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="d-inline-flex align-items-center">
                                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <?php echo e(Auth::user()->name); ?>

                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo e(route('profile')); ?>">
                                    <i class="bi bi-person-circle"></i> Profil Saya
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('login')); ?>">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('register')); ?>">
                                <i class="bi bi-person-plus"></i> Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <main class="py-4">
        <?php if(session('success')): ?>
            <div class="container">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if(session('error')): ?>
            <div class="container">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        <?php endif; ?>
        
        <?php echo $__env->yieldContent('content'); ?>
    </main>
    
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2024 CarWash Connect. All rights reserved.</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if(auth()->guard()->check()): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        loadNotifications();
        setInterval(loadNotifications, 30000); // Check every 30 seconds
    });
    
    async function loadNotifications() {
        try {
            const response = await fetch('/api/notifications/unread-count');
            const data = await response.json();
            
            const countBadge = document.getElementById('notif-count');
            if (data.count > 0) {
                countBadge.textContent = data.count;
                countBadge.style.display = 'block';
            } else {
                countBadge.style.display = 'none';
            }
            
            // Load notification list when dropdown is opened
            document.getElementById('notificationDropdown').addEventListener('click', loadNotificationList);
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }
    
    async function loadNotificationList() {
        try {
            const response = await fetch('/api/notifications/recent');
            const data = await response.json();
            
            const notificationList = document.getElementById('notification-list');
            
            if (data.notifications.length === 0) {
                notificationList.innerHTML = '<li><span class="dropdown-item-text text-muted">Tidak ada notifikasi</span></li>';
                return;
            }
            
            notificationList.innerHTML = data.notifications.map(notif => `
                <li>
                    <a class="dropdown-item ${notif.is_read ? '' : 'fw-bold'}" href="/notifications" onclick="markAsRead(${notif.id})">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${notif.title}</h6>
                                <p class="mb-1 small">${notif.message}</p>
                                <small class="text-muted">${notif.created_at}</small>
                            </div>
                            ${!notif.is_read ? '<span class="badge bg-primary rounded-pill">Baru</span>' : ''}
                        </div>
                    </a>
                </li>
            `).join('');
        } catch (error) {
            console.error('Error loading notification list:', error);
            document.getElementById('notification-list').innerHTML = '<li><span class="dropdown-item-text text-danger">Error memuat notifikasi</span></li>';
        }
    }
    
    async function markAsRead(notificationId) {
        try {
            await fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            loadNotifications();
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }
    </script>
    <?php endif; ?>
    
    <?php echo $__env->yieldPushContent('styles'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/layouts/carwash.blade.php ENDPATH**/ ?>