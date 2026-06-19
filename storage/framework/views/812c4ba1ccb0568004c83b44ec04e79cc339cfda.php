

<?php $__env->startSection('title', 'Edit Diskon'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2 class="mb-4">Edit Diskon</h2>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('admin.discounts.update', $discount)); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="mb-3">
            <label class="form-label">Nama Diskon</label>
            <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $discount->name)); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Layanan</label>
            <select name="service_id" class="form-select" required>
                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($service->id); ?>" <?php echo e(old('service_id', $discount->service_id) == $service->id ? 'selected' : ''); ?>>
                        <?php echo e($service->name); ?> - Rp <?php echo e(number_format($service->price, 0, ',', '.')); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Kode</label>
            <input type="text" name="code" class="form-control" value="<?php echo e(old('code', $discount->code)); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Persentase (%)</label>
            <input type="number" name="percent" class="form-control" value="<?php echo e(old('percent', $discount->percent)); ?>" min="1" max="100" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <input type="text" name="description" class="form-control" value="<?php echo e(old('description', $discount->description)); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Berlaku Sampai</label>
            <input type="datetime-local" name="expires_at" class="form-control" value="<?php echo e(old('expires_at', $discount->expires_at->format('Y-m-d\TH:i'))); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="active" class="form-select" required>
                <option value="1" <?php echo e(old('active', $discount->active) ? 'selected' : ''); ?>>Aktif</option>
                <option value="0" <?php echo e(old('active', $discount->active) ? '' : 'selected'); ?>>Nonaktif</option>
            </select>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo e(route('admin.discounts.index')); ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin_master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/admin/discounts/edit.blade.php ENDPATH**/ ?>