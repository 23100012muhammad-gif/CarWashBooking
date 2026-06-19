

<?php $__env->startSection('title', 'Admin - Diskon'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Manajemen Diskon</h2>
        <a href="<?php echo e(route('admin.discounts.create')); ?>" class="btn btn-primary">Tambah Diskon</a>
    </div>

    <?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Layanan</th>
                    <th>Kode</th>
                    <th>Persentase</th>
                    <th>Deskripsi</th>
                    <th>Berlaku Sampai</th>
                    <th>Status</th>
                    <th>Harga Akhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $discounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $disc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($disc->name); ?></td>
                    <td>
                        <?php if($disc->service): ?>
                            <span class="badge bg-primary"><?php echo e($disc->service->name); ?></span>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($disc->code); ?></td>
                    <td><?php echo e($disc->percent); ?>%</td>
                    <td><?php echo e($disc->description); ?></td>
                    <td><?php echo e($disc->expires_at->format('d M Y H:i')); ?></td>
                    <td>
                        <?php if($disc->active && $disc->expires_at->isFuture()): ?>
                            <span class="badge bg-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Tidak Aktif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php ($price = optional($disc->service)->price ?? 0); ?>
                        <?php ($final = max(0, $price - floor($price * ($disc->percent / 100)))); ?>
                        Rp <?php echo e(number_format($final, 0, ',', '.')); ?>

                    </td>
                    <td class="text-nowrap">
                        <a href="<?php echo e(route('admin.discounts.edit', $disc)); ?>" class="btn btn-sm btn-warning">Edit</a>
                        <form action="<?php echo e(route('admin.discounts.destroy', $disc)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus diskon ini?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" class="text-center">Belum ada diskon</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin_master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/admin/discounts/index.blade.php ENDPATH**/ ?>