<?php $__env->startSection('title', 'Dashboard - Admin Panel'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-speedometer2"></i> Dashboard Admin
    </h2>
    
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Pesanan</h6>
                            <h2 class="mb-0"><?php echo e($totalOrders); ?></h2>
                        </div>
                        <i class="bi bi-cart-fill display-4"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Menunggu</h6>
                            <h2 class="mb-0"><?php echo e($waitingOrders); ?></h2>
                        </div>
                        <i class="bi bi-hourglass-split display-4"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Dalam Proses</h6>
                            <h2 class="mb-0"><?php echo e($processingOrders); ?></h2>
                        </div>
                        <i class="bi bi-gear-fill display-4"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Selesai</h6>
                            <h2 class="mb-0"><?php echo e($completedOrders); ?></h2>
                        </div>
                        <i class="bi bi-check-circle-fill display-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mt-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="bi bi-graph-up"></i> Ringkasan
            </h5>
        </div>
        <div class="card-body">
            <p class="mb-0">Selamat datang di panel admin. Gunakan menu navigasi untuk mengelola pesanan.</p>
            <hr>
            <a href="<?php echo e(route('admin.orders')); ?>" class="btn btn-primary">
                <i class="bi bi-list-ul"></i> Kelola Pesanan
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin_master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/runner/workspace/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>