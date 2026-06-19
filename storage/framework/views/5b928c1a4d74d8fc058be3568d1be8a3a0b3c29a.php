

<?php $__env->startSection('title', 'Layanan - Car Wash Booking'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-list-check"></i> Daftar Layanan Kami
    </h2>

    <?php if(isset($activeDiscounts) && $activeDiscounts->count()): ?>
    <div class="alert alert-success">
        <strong>Promo Aktif:</strong>
        <?php $__currentLoopData = $activeDiscounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $disc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span class="badge bg-success me-2"><?php echo e($disc->code); ?> - <?php echo e($disc->percent); ?>% (s.d. <?php echo e($disc->expires_at->format('d M Y')); ?>)</span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

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
                    
                    <?php
                        $serviceDiscounts = $activeDiscounts->where('service_id', $service->id);
                    ?>
                    
                    <?php if($serviceDiscounts->count()): ?>
                        <div class="mt-3">
                            <h6 class="text-warning">
                                <i class="bi bi-percent"></i> Diskon Tersedia:
                            </h6>
                            <?php $__currentLoopData = $serviceDiscounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $discount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $originalPrice = $service->price;
                                    $discountAmount = floor($originalPrice * ($discount->percent / 100));
                                    $finalPrice = max(0, $originalPrice - $discountAmount);
                                ?>
                                <div class="alert alert-warning py-2 mb-2">
                                    <strong><?php echo e($discount->name); ?></strong> - <?php echo e($discount->percent); ?>% OFF<br>
                                    <small><?php echo e($discount->description); ?></small><br>
                                    <div class="mt-1">
                                        <small class="text-muted">Normal: <span class="text-decoration-line-through">Rp <?php echo e(number_format($originalPrice, 0, ',', '.')); ?></span></small><br>
                                        <strong class="text-success">Setelah diskon: Rp <?php echo e(number_format($finalPrice, 0, ',', '.')); ?></strong><br>
                                        <small class="text-success">Hemat: Rp <?php echo e(number_format($discountAmount, 0, ',', '.')); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="<?php echo e(route('booking.create', ['service_id' => $service->id])); ?>" class="btn btn-primary">
                        <i class="bi bi-cart-plus"></i> Pesan Layanan Ini
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.carwash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/services.blade.php ENDPATH**/ ?>