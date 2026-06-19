

<?php $__env->startSection('title', 'Edit Layanan'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2 class="mb-4">Edit Layanan</h2>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('admin.services.update', $service)); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="mb-3">
            <label class="form-label">Nama Layanan</label>
            <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $service->name)); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="4" required><?php echo e(old('description', $service->description)); ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Harga (Rp)</label>
            <input type="number" name="price" class="form-control" value="<?php echo e(old('price', $service->price)); ?>" min="0" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Durasi (menit) - opsional</label>
            <input type="number" name="duration" class="form-control" value="<?php echo e(old('duration', $service->duration)); ?>" min="0">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo e(route('admin.services.index')); ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin_master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/admin/services/edit.blade.php ENDPATH**/ ?>