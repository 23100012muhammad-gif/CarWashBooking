<?php $__env->startSection('title', 'Layanan - Car Wash Booking'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-list-check"></i> Daftar Layanan Kami
    </h2>
    
    <div class="row">
        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary">
                        <i class="bi bi-check-circle"></i> <?php echo e($service->name); ?>

                    </h5>
                    <p class="card-text"><?php echo e($service->description); ?></p>
                    <h4 class="text-success">Rp <?php echo e(number_format($service->price, 0, ',', '.')); ?></h4>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="<?php echo e(route('booking.create', ['service_id' => $service->id])); ?>" class="btn btn-primary">
                        <i class="bi bi-cart-plus"></i> Pesan Layanan Ini
                    </a>
                    
                    <?php if($service->discounts->where('active', true)->where('expires_at', '>=', now())->count() > 0): ?>
                        <?php $__currentLoopData = $service->discounts->where('active', true)->where('expires_at', '>=', now()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $discount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('booking.create', ['service_id' => $service->id, 'discount_id' => $discount->id])); ?>" class="btn btn-success btn-sm mt-2 d-block">
                                <i class="bi bi-percent"></i> <?php echo e($discount->name); ?> (<?php echo e($discount->percent); ?>% OFF)
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    
    <div class="alert alert-info mt-4" role="alert">
        <i class="bi bi-info-circle"></i> 
        <strong>Informasi:</strong> Semua layanan dilakukan oleh tenaga profesional dengan peralatan modern dan ramah lingkungan.
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.carwash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/layanan.blade.php ENDPATH**/ ?>