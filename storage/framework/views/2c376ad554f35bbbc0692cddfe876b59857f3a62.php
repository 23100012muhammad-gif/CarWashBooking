

<?php $__env->startSection('title', 'Konfirmasi Pembayaran - Car Wash Booking'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-credit-card"></i> Konfirmasi Pembayaran
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Order Details -->
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
                                    <td><strong>Plat Kendaraan:</strong></td>
                                    <td><?php echo e($order->license_plate); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-cash-stack"></i> Rincian Pembayaran
                            </h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Harga Awal:</strong></td>
                                    <td>Rp <?php echo e(number_format($order->original_price ?? 0, 0, ',', '.')); ?></td>
                                </tr>
                                <?php if(($order->discount_percent ?? 0) > 0): ?>
                                <tr>
                                    <td><strong>Diskon (<?php echo e($order->discount_percent); ?>%):</strong></td>
                                    <td class="text-success">- Rp <?php echo e(number_format(($order->original_price ?? 0) * ($order->discount_percent ?? 0) / 100, 0, ',', '.')); ?></td>
                                </tr>
                                <?php if($order->discount_name): ?>
                                <tr>
                                    <td><strong>Nama Diskon:</strong></td>
                                    <td><span class="badge bg-success"><?php echo e($order->discount_name); ?></span></td>
                                </tr>
                                <?php endif; ?>
                                <?php endif; ?>
                                <tr class="border-top">
                                    <td><strong>Total Pembayaran:</strong></td>
                                    <td class="h5 text-primary"><strong>Rp <?php echo e(number_format($order->final_price ?? $order->original_price ?? 0, 0, ',', '.')); ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-wallet2"></i> Pilih Metode Pembayaran
                            </h5>
                            
                            <form action="<?php echo e(route('payment.process', $order->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                
                                <div class="row">
                                    <!-- Bank Transfer -->
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <i class="bi bi-bank text-primary" style="font-size: 3rem;"></i>
                                                <h5 class="card-title mt-3">Transfer Bank</h5>
                                                <p class="card-text text-muted">
                                                    Transfer ke rekening bank kami dan upload bukti transfer
                                                </p>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_method" 
                                                           id="bank_transfer" value="bank_transfer" required>
                                                    <label class="form-check-label" for="bank_transfer">
                                                        Pilih Transfer Bank
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- E-Wallet -->
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <i class="bi bi-phone text-success" style="font-size: 3rem;"></i>
                                                <h5 class="card-title mt-3">E-Wallet</h5>
                                                <p class="card-text text-muted">
                                                    Bayar dengan GoPay, OVO, atau Dana secara instan
                                                </p>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_method" 
                                                           id="ewallet" value="ewallet" required>
                                                    <label class="form-check-label" for="ewallet">
                                                        Pilih E-Wallet
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Reference (Optional) -->
                                <div class="mb-3">
                                    <label for="payment_reference" class="form-label">Referensi Pembayaran (Opsional)</label>
                                    <input type="text" class="form-control" id="payment_reference" 
                                           name="payment_reference" placeholder="Contoh: Transfer dari rekening sendiri">
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="<?php echo e(route('home')); ?>" class="btn btn-outline-secondary me-md-2">
                                        <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-arrow-right"></i> Lanjutkan Pembayaran
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.carwash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/payment/confirmation.blade.php ENDPATH**/ ?>