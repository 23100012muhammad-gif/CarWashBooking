<?php $__env->startSection('title', 'Dashboard - Admin Panel'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-speedometer2"></i> Dashboard Admin
    </h2>
    
    <!-- Statistik Pesanan Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center mb-3">
                <h3 class="text-primary mb-0 me-3">
                    <i class="bi bi-graph-up"></i> Statistik Pesanan
                </h3>
                <hr class="flex-grow-1 text-primary">
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-md-6">
            <div class="card text-white bg-primary h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1">Total Pesanan</h6>
                            <h2 class="mb-0"><?php echo e($totalOrders); ?></h2>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-cart-fill fs-1 opacity-75"></i>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <small class="opacity-75">Semua pesanan yang masuk</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card text-white bg-warning h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1">Menunggu</h6>
                            <h2 class="mb-0"><?php echo e($waitingOrders); ?></h2>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-hourglass-split fs-1 opacity-75"></i>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <small class="opacity-75">Pesanan dalam antrian</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card text-white bg-info h-100">
                <div class="card-body-row d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1">Dalam Proses</h6>
                            <h2 class="mb-0"><?php echo e($processingOrders); ?></h2>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-gear-fill fs-1 opacity-75"></i>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <small class="opacity-75">Sedang dicuci</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card text-white bg-success h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1">Selesai</h6>
                            <h2 class="mb-0"><?php echo e($completedOrders); ?></h2>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle-fill fs-1 opacity-75"></i>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <small class="opacity-75">Cucian selesai</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Pembayaran Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center mb-3">
                <h3 class="text-success mb-0 me-3">
                    <i class="bi bi-credit-card-2-front"></i> Status Pembayaran
                </h3>
                <hr class="flex-grow-1 text-success">
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-6 col-md-12">
            <div class="card text-white bg-warning h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1">Menunggu Verifikasi</h6>
                            <h2 class="mb-0"><?php echo e($pendingPayments); ?></h2>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-credit-card fs-1 opacity-75"></i>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <small class="opacity-75">Pembayaran perlu diverifikasi</small>
                        <div class="mt-2">
                            <a href="<?php echo e(route('admin.verifications')); ?>" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-right"></i> Verifikasi Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 col-md-12">
            <div class="card text-white bg-success h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1">Pembayaran Terverifikasi</h6>
                            <h2 class="mb-0"><?php echo e($verifiedPayments); ?></h2>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle fs-1 opacity-75"></i>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <small class="opacity-75">Pembayaran sudah dikonfirmasi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center mb-3">
                <h3 class="text-dark mb-0 me-3">
                    <i class="bi bi-lightning-fill"></i> Aksi Cepat
                </h3>
                <hr class="flex-grow-1 text-dark">
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4 col-md-6">
            <div class="card border-primary h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-list-ul text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title text-primary">Kelola Pesanan</h5>
                    <p class="card-text text-muted">Lihat dan kelola semua pesanan yang masuk</p>
                    <a href="<?php echo e(route('admin.orders')); ?>" class="btn btn-primary">
                        <i class="bi bi-arrow-right"></i> Kelola Pesanan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-warning h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-credit-card-check text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title text-warning">Verifikasi Pembayaran</h5>
                    <p class="card-text text-muted">Verifikasi pembayaran yang menunggu konfirmasi</p>
                    <a href="<?php echo e(route('admin.verifications')); ?>" class="btn btn-warning">
                        <i class="bi bi-arrow-right"></i> Verifikasi Pembayaran
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-info h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-gear text-info" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title text-info">Kelola Layanan</h5>
                    <p class="card-text text-muted">Tambah atau edit layanan cuci mobil</p>
                    <a href="<?php echo e(route('admin.services.index')); ?>" class="btn btn-info">
                        <i class="bi bi-arrow-right"></i> Kelola Layanan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card shadow mt-5">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="bi bi-info-circle"></i> Ringkasan Sistem
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <p class="mb-2">Selamat datang di panel admin CarWash Connect!</p>
                    <p class="mb-0 text-muted">Gunakan menu navigasi atau tombol aksi cepat di atas untuk mengelola sistem.</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex flex-column align-items-end">
                        <small class="text-muted">Total Pesanan Hari Ini</small>
                        <h4 class="text-primary mb-0"><?php echo e($totalOrders); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin_master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>