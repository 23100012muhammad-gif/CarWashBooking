<?php $__env->startSection('title', 'Verifikasi Pembayaran & Refund - Admin Panel'); ?>

<?php
use Illuminate\Support\Facades\Storage;
?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-shield-check"></i> Verifikasi Pembayaran & Refund
    </h2>
    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-4" id="verificationTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab">
                <i class="bi bi-credit-card-2-back"></i> Verifikasi Pembayaran 
                <?php if($pendingOrders->count() > 0): ?>
                    <span class="badge bg-danger"><?php echo e($pendingOrders->count()); ?></span>
                <?php endif; ?>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="refund-tab" data-bs-toggle="tab" data-bs-target="#refund" type="button" role="tab">
                <i class="bi bi-arrow-return-left"></i> Pengajuan Refund
                <?php if($refundRequests->count() > 0): ?>
                    <span class="badge bg-warning"><?php echo e($refundRequests->count()); ?></span>
                <?php endif; ?>
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="verificationTabsContent">
        <!-- Verifikasi Pembayaran -->
        <div class="tab-pane fade show active" id="payment" role="tabpanel">
            <?php if($pendingOrders->isEmpty()): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Tidak ada pembayaran yang perlu diverifikasi.
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No. Pesanan</th>
                                        <th>Layanan</th>
                                        <th>Plat Nomor</th>
                                        <th>Total Bayar</th>
                                        <th>Bukti Bayar</th>
                                        <th>Tanggal Upload</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $pendingOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <strong class="text-primary">#<?php echo e($order->id); ?></strong>
                                            <?php if($order->refund_requested_at): ?>
                                                <br><small class="badge bg-warning text-dark">Mengajukan Refund</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($order->service_type); ?></td>
                                        <td><?php echo e($order->license_plate); ?></td>
                                        <td>
                                            <?php if($order->discount_percent > 0): ?>
                                                <small class="text-muted text-decoration-line-through">Rp <?php echo e(number_format($order->original_price, 0, ',', '.')); ?></small><br>
                                                <strong class="text-success">Rp <?php echo e(number_format($order->final_price, 0, ',', '.')); ?></strong>
                                                <small class="text-success">(<?php echo e($order->discount_percent); ?>% OFF)</small>
                                            <?php else: ?>
                                                <strong>Rp <?php echo e(number_format($order->final_price, 0, ',', '.')); ?></strong>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="showPaymentProof('<?php echo e(route('admin.payment-proof', basename($order->payment_proof))); ?>')">
                                                <i class="bi bi-eye"></i> Lihat Bukti
                                            </button>
                                        </td>
                                        <td><?php echo e($order->updated_at->format('d M Y H:i')); ?></td>
                                        <td>
                                            <?php if($order->refund_requested_at): ?>
                                                <span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Lihat Tab Refund</span>
                                            <?php else: ?>
                                                <form action="<?php echo e(route('admin.payment.verify', $order->id)); ?>" method="POST" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Verifikasi pembayaran ini?')">
                                                        <i class="bi bi-check-lg"></i> Verifikasi
                                                    </button>
                                                </form>
                                                <form action="<?php echo e(route('admin.payment.verify', $order->id)); ?>" method="POST" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tolak pembayaran ini?')">
                                                        <i class="bi bi-x-lg"></i> Tolak
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pengajuan Refund -->
        <div class="tab-pane fade" id="refund" role="tabpanel">
            <?php if($refundRequests->isEmpty()): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Tidak ada pengajuan refund.
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No. Pesanan</th>
                                        <th>Layanan</th>
                                        <th>Total Bayar</th>
                                        <th>Alasan Refund</th>
                                        <th>Bukti Bayar</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $refundRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><strong class="text-primary">#<?php echo e($order->id); ?></strong></td>
                                        <td><?php echo e($order->service_type); ?></td>
                                        <td><strong>Rp <?php echo e(number_format($order->final_price, 0, ',', '.')); ?></strong></td>
                                        <td><?php echo e($order->refund_reason); ?></td>
                                        <td>
                                            <?php if($order->payment_proof): ?>
                                                <button class="btn btn-sm btn-outline-primary" onclick="showPaymentProof('<?php echo e(route('admin.payment-proof', basename($order->payment_proof))); ?>')">
                                                    <i class="bi bi-eye"></i> Lihat Bukti
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">Tidak ada</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($order->refund_requested_at->format('d M Y H:i')); ?></td>
                                        <td>
                                            <form action="<?php echo e(route('admin.refund.process', $order->id)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Setujui refund ini?')">
                                                    <i class="bi bi-check-lg"></i> Setujui
                                                </button>
                                            </form>
                                            <form action="<?php echo e(route('admin.refund.process', $order->id)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tolak refund ini?')">
                                                    <i class="bi bi-x-lg"></i> Tolak
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Bukti Pembayaran -->
<div class="modal fade" id="paymentProofModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="paymentProofImage" src="" class="img-fluid" style="max-height: 500px;">
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function showPaymentProof(imageUrl) {
    document.getElementById('paymentProofImage').src = imageUrl;
    new bootstrap.Modal(document.getElementById('paymentProofModal')).show();
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin_master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/admin/verifications.blade.php ENDPATH**/ ?>