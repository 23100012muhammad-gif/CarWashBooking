<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">
            <i class="bi bi-info-circle"></i> Status Pesanan
        </h5>
        <div class="row mt-3">
            <div class="col-md-6">
                <p class="mb-2"><strong>Nomor Antrean:</strong></p>
                <h2 class="text-primary"><?php echo e($order->queue_number); ?></h2>
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Status:</strong></p>
                <?php if($order->status == 'Menunggu'): ?>
                    <span class="badge bg-warning text-dark fs-6"><?php echo e($order->status); ?></span>
                <?php elseif($order->status == 'Proses'): ?>
                    <span class="badge bg-info fs-6"><?php echo e($order->status); ?></span>
                <?php else: ?>
                    <span class="badge bg-success fs-6"><?php echo e($order->status); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <hr>
        <div class="mt-3">
            <p class="mb-1"><strong>Jenis Layanan:</strong> <?php echo e($order->service_type); ?></p>
            <p class="mb-1"><strong>Plat Nomor:</strong> <?php echo e($order->license_plate); ?></p>
            <p class="mb-1"><strong>Tanggal Booking:</strong> <?php echo e($order->booking_date->format('d M Y H:i')); ?></p>
            <?php if($order->final_price): ?>
                <div class="mt-2 p-2 bg-light rounded">
                    <p class="mb-1"><strong>Harga:</strong></p>
                    <?php if($order->discount_percent > 0): ?>
                        <p class="mb-1">
                            <small class="text-muted">Normal: <span class="text-decoration-line-through">Rp <?php echo e(number_format($order->original_price, 0, ',', '.')); ?></span></small><br>
                            <strong class="text-success">Setelah diskon: Rp <?php echo e(number_format($order->final_price, 0, ',', '.')); ?></strong><br>
                            <small class="text-success">Diskon: <?php echo e($order->discount_percent); ?>% (<?php echo e($order->discount_name); ?>)</small>
                        </p>
                    <?php else: ?>
                        <p class="mb-1"><strong>Rp <?php echo e(number_format($order->final_price, 0, ',', '.')); ?></strong></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/partials/status_card.blade.php ENDPATH**/ ?>