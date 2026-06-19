<?php $__env->startSection('title', 'Status Pesanan - Car Wash Booking'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-clock-history"></i> Status Pesanan Aktif
    </h2>
    
    <?php if($activeOrders->isEmpty()): ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> Tidak ada pesanan aktif saat ini.
        </div>
        <a href="<?php echo e(route('booking.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Buat Pesanan Baru
        </a>
    <?php else: ?>
        <div class="row">
            <?php $__currentLoopData = $activeOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-6 mb-4">
                <?php echo $__env->make('partials.status_card', ['order' => $order], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
        <div class="alert alert-warning mt-4" role="alert">
            <i class="bi bi-exclamation-triangle"></i> 
            <strong>Catatan:</strong> Halaman ini akan diperbarui otomatis. Silakan refresh untuk melihat status terkini.
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.carwash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/status.blade.php ENDPATH**/ ?>