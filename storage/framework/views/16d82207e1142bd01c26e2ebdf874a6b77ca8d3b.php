

<?php $__env->startSection('title', 'Transfer Bank - Car Wash Booking'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-bank"></i> Instruksi Transfer Bank
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Order Summary -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle"></i> Rincian Pembayaran
                        </h6>
                        <p class="mb-0">
                            <strong>Nomor Pesanan:</strong> #<?php echo e($order->id); ?> | 
                            <strong>Total:</strong> Rp <?php echo e(number_format($order->final_price, 0, ',', '.')); ?>

                        </p>
                    </div>

                    <!-- Bank Account Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-credit-card-2-front"></i> Rekening Tujuan
                            </h5>
                        </div>
                        
                        <?php $__currentLoopData = $bankAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-4 mb-3">
                            <div class="card border-primary">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary"><?php echo e($bank['bank']); ?></h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1"><strong>No. Rekening:</strong></p>
                                    <p class="h6 text-primary"><?php echo e($bank['account_number']); ?></p>
                                    
                                    <p class="mb-1"><strong>Atas Nama:</strong></p>
                                    <p class="mb-1"><?php echo e($bank['account_name']); ?></p>
                                    
                                    <p class="mb-1"><strong>Cabang:</strong></p>
                                    <p class="mb-0"><?php echo e($bank['branch']); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <!-- Transfer Instructions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-list-ol"></i> Langkah-langkah Transfer
                            </h5>
                            <div class="card">
                                <div class="card-body">
                                    <ol class="mb-0">
                                        <li>Transfer sejumlah <strong>Rp <?php echo e(number_format($order->final_price, 0, ',', '.')); ?></strong> ke salah satu rekening di atas</li>
                                        <li>Gunakan nomor pesanan <strong>#<?php echo e($order->id); ?></strong> sebagai keterangan transfer</li>
                                        <li>Screenshot atau foto bukti transfer</li>
                                        <li>Upload bukti transfer di form di bawah ini</li>
                                        <li>Tunggu konfirmasi dari tim kami (maksimal 1x24 jam)</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Payment Proof -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-cloud-upload"></i> Upload Bukti Transfer
                            </h5>
                            
                            <form action="<?php echo e(route('payment.upload-proof', $order->id)); ?>" method="POST" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="payment_proof" class="form-label">
                                                <strong>Bukti Transfer</strong> <span class="text-danger">*</span>
                                            </label>
                                            <input type="file" class="form-control <?php $__errorArgs = ['payment_proof'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="payment_proof" name="payment_proof" 
                                                   accept="image/*,.pdf" required>
                                            <div class="form-text">
                                                Format yang diterima: JPG, PNG, PDF (Maksimal 2MB)
                                            </div>
                                            <?php $__errorArgs = ['payment_proof'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="mb-3">
                                            <label for="payment_notes" class="form-label">
                                                <strong>Catatan Tambahan</strong>
                                            </label>
                                            <textarea class="form-control <?php $__errorArgs = ['payment_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                      id="payment_notes" name="payment_notes" rows="3" 
                                                      placeholder="Contoh: Transfer dari rekening sendiri, jam transfer, dll."></textarea>
                                            <?php $__errorArgs = ['payment_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <a href="<?php echo e(route('payment.confirmation', $order->id)); ?>" 
                                               class="btn btn-outline-secondary me-md-2">
                                                <i class="bi bi-arrow-left"></i> Kembali
                                            </a>
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-cloud-upload"></i> Upload Bukti Transfer
                                            </button>
                                        </div>
                                    </div>
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


<?php echo $__env->make('layouts.carwash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/payment/bank-transfer.blade.php ENDPATH**/ ?>