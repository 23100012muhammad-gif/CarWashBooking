<?php $__env->startSection('title', 'Kelola Pesanan - Admin Panel'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-clipboard-check"></i> Kelola Pesanan
    </h2>
    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if($orders->isEmpty()): ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> Belum ada pesanan yang masuk.
        </div>
    <?php else: ?>
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Layanan</th>
                                <th>Plat Nomor</th>
                                <th>Tanggal Booking</th>
                                <th>Status Pesanan</th>
                                <th>Status Proses</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><strong class="text-primary">#<?php echo e($order->id); ?></strong></td>
                                <td><?php echo e($order->service_type); ?></td>
                                <td><?php echo e($order->license_plate); ?></td>
                                <td><?php echo e(\Carbon\Carbon::parse($order->booking_date)->format('d M Y H:i')); ?></td>
                                <td>
                                    <?php if($order->payment_status == 'verified'): ?>
                                        <span class="badge bg-success">Terkonfirmasi</span>
                                    <?php elseif($order->payment_status == 'pending'): ?>
                                        <span class="badge bg-warning text-dark">Pending Pembayaran</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Ditolak</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($order->status == 'Menunggu'): ?>
                                        <span class="badge bg-warning text-dark"><?php echo e($order->status); ?></span>
                                    <?php elseif($order->status == 'Proses'): ?>
                                        <span class="badge bg-info"><?php echo e($order->status); ?></span>
                                    <?php elseif($order->status == 'Selesai'): ?>
                                        <span class="badge bg-success"><?php echo e($order->status); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo e($order->status); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($order->final_price): ?>
                                        <span class="text-primary"><strong>Rp <?php echo e(number_format($order->final_price, 0, ',', '.')); ?></strong></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <?php if($order->payment_status == 'verified'): ?>
                                            <form action="<?php echo e(route('admin.orders.update', $order->id)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 120px;">
                                                    <option value="Menunggu" <?php echo e($order->status == 'Menunggu' ? 'selected' : ''); ?>>Menunggu</option>
                                                    <option value="Proses" <?php echo e($order->status == 'Proses' ? 'selected' : ''); ?>>Proses</option>
                                                    <option value="Selesai" <?php echo e($order->status == 'Selesai' ? 'selected' : ''); ?>>Selesai</option>
                                                </select>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">Menunggu Verifikasi</span>
                                        <?php endif; ?>
                                        
                                        <?php if(in_array($order->status, ['Terkonfirmasi', 'Selesai', 'Refund', 'Batal'])): ?>
                                            <button type="button" class="btn btn-danger btn-sm ms-2" 
                                                    onclick="deleteOrder(<?php echo e($order->id); ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info mt-4" role="alert">
            <i class="bi bi-info-circle"></i> 
            <strong>Petunjuk:</strong> Pilih status baru pada dropdown untuk mengubah status pesanan secara otomatis.
        </div>
    <?php endif; ?>
</div>

<script>
function deleteOrder(orderId) {
    if (confirm('Apakah Anda yakin ingin menghapus pesanan ini? Tindakan ini tidak dapat dibatalkan.')) {
        // Create a form to submit DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route("admin.orders.delete", ":id")); ?>'.replace(':id', orderId);
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        form.appendChild(csrfToken);
        
        // Add method spoofing
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin_master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/admin/orders.blade.php ENDPATH**/ ?>