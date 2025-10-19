<?php $__env->startSection('title', 'Buat Pesanan - Car Wash Booking'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar-plus"></i> Form Pemesanan Layanan
                    </h4>
                </div>
                <div class="card-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo e(route('booking.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-3">
                            <label for="service_type" class="form-label">Jenis Layanan</label>
                            <select class="form-select" id="service_type" name="service_type" required>
                                <option value="">Pilih Layanan...</option>
                                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($service->name); ?>">
                                        <?php echo e($service->name); ?> - Rp <?php echo e(number_format($service->price, 0, ',', '.')); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="license_plate" class="form-label">Nomor Plat Kendaraan</label>
                            <input type="text" class="form-control" id="license_plate" name="license_plate" 
                                   placeholder="Contoh: B 1234 XYZ" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="booking_date" class="form-label">Tanggal & Waktu Booking</label>
                            <input type="datetime-local" class="form-control" id="booking_date" name="booking_date" required>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Buat Pesanan
                            </button>
                            <a href="<?php echo e(route('services')); ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/runner/workspace/resources/views/booking_form.blade.php ENDPATH**/ ?>