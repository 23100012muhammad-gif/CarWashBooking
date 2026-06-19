

<?php $__env->startSection('title', 'Status Pembayaran - Car Wash Booking'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-check-circle"></i> Status Pembayaran
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Order Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-receipt"></i> Rincian Pesanan
                            </h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nomor Pesanan:</strong></td>
                                    <td>#<?php echo e($order->id); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Layanan:</strong></td>
                                    <td><?php echo e($order->service_type); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal & Waktu:</strong></td>
                                    <td><?php echo e(\Carbon\Carbon::parse($order->booking_date)->format('d M Y, H:i')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Nomor Antrian:</strong></td>
                                    <td><?php echo e($order->queue_number); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Total Pembayaran:</strong></td>
                                    <td class="h6 text-primary">Rp <?php echo e(number_format($order->final_price, 0, ',', '.')); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-credit-card"></i> Status Pembayaran
                            </h5>
                            
                            <?php if($order->isPaymentVerified()): ?>
                                <div class="alert alert-success">
                                    <h6 class="alert-heading">
                                        <i class="bi bi-check-circle-fill"></i> Pembayaran Berhasil
                                    </h6>
                                    <p class="mb-0">
                                        Pembayaran Anda telah diverifikasi dan pesanan telah dikonfirmasi.
                                        Silakan datang sesuai jadwal yang telah ditentukan.
                                    </p>
                                </div>
                                
                                <?php if($order->payment_verified_at): ?>
                                <p class="mb-1"><strong>Waktu Verifikasi:</strong></p>
                                <p class="mb-1"><?php echo e(\Carbon\Carbon::parse($order->payment_verified_at)->format('d M Y, H:i')); ?></p>
                                <?php endif; ?>
                                
                                <?php if($order->verifier): ?>
                                <p class="mb-1"><strong>Diverifikasi oleh:</strong></p>
                                <p class="mb-0"><?php echo e($order->verifier->name); ?></p>
                                <?php endif; ?>
                                
                            <?php elseif($order->isPaymentFailed()): ?>
                                <div class="alert alert-danger">
                                    <h6 class="alert-heading">
                                        <i class="bi bi-x-circle-fill"></i> Pembayaran Gagal
                                    </h6>
                                    <p class="mb-0">
                                        Pembayaran Anda tidak dapat diproses. Silakan coba lagi atau gunakan metode pembayaran lain.
                                    </p>
                                </div>
                                
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading">
                                        <i class="bi bi-clock-fill"></i> Menunggu Pembayaran
                                    </h6>
                                    <p class="mb-0">
                                        Pembayaran Anda sedang diproses. Silakan tunggu konfirmasi dari tim kami.
                                    </p>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mt-3">
                                <span class="badge bg-<?php echo e($order->getPaymentStatusClass()); ?> fs-6">
                                    <?php echo e($order->getPaymentStatusLabel()); ?>

                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Details -->
                    <?php if($order->payment_method): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-wallet2"></i> Metode Pembayaran
                            </h5>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Metode:</strong></p>
                                            <p class="mb-1">
                                                <?php if($order->payment_method === 'bank_transfer'): ?>
                                                    <i class="bi bi-bank text-primary"></i> Transfer Bank
                                                <?php else: ?>
                                                    <i class="bi bi-phone text-success"></i> E-Wallet
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <?php if($order->payment_reference): ?>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Referensi:</strong></p>
                                            <p class="mb-1"><?php echo e($order->payment_reference); ?></p>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if($order->hasPaymentProof()): ?>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <p class="mb-1"><strong>Bukti Pembayaran:</strong></p>
                                            <a href="<?php echo e(Storage::url($order->payment_proof)); ?>" 
                                               class="btn btn-outline-primary btn-sm" target="_blank">
                                                <i class="bi bi-eye"></i> Lihat Bukti
                                            </a>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if($order->payment_notes): ?>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <p class="mb-1"><strong>Catatan:</strong></p>
                                            <p class="mb-0"><?php echo e($order->payment_notes); ?></p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                        <a href="<?php echo e(route('booking.history')); ?>" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-clock-history"></i> Lihat Riwayat
                        </a>
                        
                        <?php if($order->isPaymentVerified()): ?>
                            <a href="<?php echo e(route('booking.status')); ?>" 
                               class="btn btn-primary">
                                <i class="bi bi-list-check"></i> Status Antrian
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('payment.status', $order->id)); ?>" 
                               class="btn btn-primary">
                                <i class="bi bi-arrow-clockwise"></i> Refresh Status
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(!$order->isPaymentVerified() && !$order->isPaymentFailed()): ?>
<script>
// Auto-refresh every 30 seconds for pending payments
setTimeout(function() {
    window.location.reload();
}, 30000);
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.carwash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/payment/status.blade.php ENDPATH**/ ?>