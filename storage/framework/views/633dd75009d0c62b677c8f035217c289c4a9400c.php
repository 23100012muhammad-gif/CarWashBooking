<?php $__env->startSection('title', 'Riwayat Booking - Car Wash Booking'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-journal-text"></i> Riwayat Booking
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
            <i class="bi bi-info-circle"></i> Belum ada riwayat booking.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Jenis Layanan</th>
                        <th>Plat Nomor</th>
                        <th>Tanggal Booking</th>
                        <th>Status Pembayaran</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><strong>#<?php echo e($order->id); ?></strong></td>
                        <td><?php echo e($order->service_type); ?></td>
                        <td><?php echo e($order->license_plate); ?></td>
                        <td><?php echo e(\Carbon\Carbon::parse($order->booking_date)->format('d M Y H:i')); ?></td>
                        <td>
                            <span class="badge bg-<?php echo e($order->getPaymentStatusClass()); ?>">
                                <?php echo e($order->getPaymentStatusLabel()); ?>

                            </span>
                        </td>
                        <td>Rp <?php echo e(number_format($order->final_price, 0, ',', '.')); ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                        data-bs-toggle="modal" data-bs-target="#orderModal<?php echo e($order->id); ?>">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                                <?php if($order->isPaymentVerified()): ?>
                                    <button type="button" class="btn btn-outline-success btn-sm" 
                                            onclick="printReceipt(<?php echo e($order->id); ?>)">
                                        <i class="bi bi-printer"></i> Cetak
                                    </button>
                                <?php endif; ?>
                                <?php if($order->canRequestRefund()): ?>
                                    <button type="button" class="btn btn-outline-warning btn-sm" 
                                            data-bs-toggle="modal" data-bs-target="#refundModal<?php echo e($order->id); ?>">
                                        <i class="bi bi-arrow-return-left"></i> Ajukan Refund
                                    </button>
                                <?php endif; ?>
                                <?php if($order->canDeleteFromHistory()): ?>
                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                            onclick="deleteOrder(<?php echo e($order->id); ?>)" title="Hapus dari riwayat">
                                        <i class="bi bi-trash"></i> Hapus Riwayat
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Order Detail Modals -->
<?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="orderModal<?php echo e($order->id); ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-receipt"></i> Detail Pesanan #<?php echo e($order->id); ?>

                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Informasi Pesanan</h6>
                        <table class="table table-borderless table-sm">
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
                            <tr>
                                <td><strong>Status Pesanan:</strong></td>
                                <td>
                                    <span class="badge bg-<?php echo e($order->status === 'Selesai' ? 'success' : ($order->status === 'Terkonfirmasi' ? 'primary' : 'warning')); ?>">
                                        <?php echo e($order->status); ?>

                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Informasi Pembayaran</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td><strong>Harga Awal:</strong></td>
                                <td>Rp <?php echo e(number_format($order->original_price, 0, ',', '.')); ?></td>
                            </tr>
                            <?php if($order->discount_percent > 0): ?>
                            <tr>
                                <td><strong>Diskon:</strong></td>
                                <td class="text-success"><?php echo e($order->discount_percent); ?>% (<?php echo e($order->discount_name); ?>)</td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td><strong>Total:</strong></td>
                                <td class="h6 text-primary">Rp <?php echo e(number_format($order->final_price, 0, ',', '.')); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status Pembayaran:</strong></td>
                                <td>
                                    <span class="badge bg-<?php echo e($order->getPaymentStatusClass()); ?>">
                                        <?php echo e($order->getPaymentStatusLabel()); ?>

                                    </span>
                                </td>
                            </tr>
                            <?php if($order->payment_method): ?>
                            <tr>
                                <td><strong>Metode Pembayaran:</strong></td>
                                <td>
                                    <?php if($order->payment_method === 'bank_transfer'): ?>
                                        <i class="bi bi-bank text-primary"></i> Transfer Bank
                                    <?php else: ?>
                                        <i class="bi bi-phone text-success"></i> E-Wallet
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php if($order->payment_verified_at): ?>
                            <tr>
                                <td><strong>Waktu Verifikasi:</strong></td>
                                <td><?php echo e(\Carbon\Carbon::parse($order->payment_verified_at)->format('d M Y, H:i')); ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
                
                <?php if($order->hasPaymentProof()): ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-primary">Bukti Pembayaran</h6>
                        <button class="btn btn-outline-primary btn-sm" 
                                onclick="showPaymentProof('<?php echo e(route('payment-proof', basename($order->payment_proof))); ?>')">
                            <i class="bi bi-eye"></i> Lihat Bukti Transfer
                        </button>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if($order->payment_notes): ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-primary">Catatan</h6>
                        <p class="mb-0"><?php echo e($order->payment_notes); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <?php if($order->isPaymentVerified()): ?>
                    <button type="button" class="btn btn-success" onclick="printReceipt(<?php echo e($order->id); ?>)">
                        <i class="bi bi-printer"></i> Cetak Bukti
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- Refund Request Modals -->
<?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php if($order->canRequestRefund()): ?>
<div class="modal fade" id="refundModal<?php echo e($order->id); ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-return-left"></i> Ajukan Refund - Pesanan #<?php echo e($order->id); ?>

                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('booking.refund', $order->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <p><strong>Layanan:</strong> <?php echo e($order->service_type); ?></p>
                        <p><strong>Total:</strong> Rp <?php echo e(number_format($order->final_price, 0, ',', '.')); ?></p>
                        <p><strong>Plat Kendaraan:</strong> <?php echo e($order->license_plate); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="refund_reason_<?php echo e($order->id); ?>" class="form-label">Alasan Refund <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="refund_reason_<?php echo e($order->id); ?>" 
                                  name="refund_reason" rows="4" required
                                  placeholder="Jelaskan alasan Anda mengajukan refund..."></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        Bukti transfer yang sudah Anda upload akan digunakan untuk proses refund.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-arrow-return-left"></i> Ajukan Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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

<script>
function showPaymentProof(imageUrl) {
    document.getElementById('paymentProofImage').src = imageUrl;
    new bootstrap.Modal(document.getElementById('paymentProofModal')).show();
}

function printReceipt(orderId) {
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    const order = <?php echo json_encode($orders->keyBy('id'), 15, 512) ?>;
    const orderData = order[orderId];
    
    if (!orderData) return;
    
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Bukti Pembayaran - Pesanan #${orderData.id}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
                .content { margin-bottom: 20px; }
                .row { display: flex; justify-content: space-between; margin-bottom: 5px; }
                .label { font-weight: bold; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
                @media  print { body { margin: 0; } }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>CarWash Connect</h2>
                <h3>Bukti Pembayaran</h3>
            </div>
            
            <div class="content">
                <div class="row">
                    <span class="label">Nomor Pesanan:</span>
                    <span>#${orderData.id}</span>
                </div>
                <div class="row">
                    <span class="label">Layanan:</span>
                    <span>${orderData.service_type}</span>
                </div>
                <div class="row">
                    <span class="label">Tanggal:</span>
                    <span>${new Date(orderData.booking_date).toLocaleDateString('id-ID')}</span>
                </div>
                <div class="row">
                    <span class="label">Waktu:</span>
                    <span>${new Date(orderData.booking_date).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}</span>
                </div>
                <div class="row">
                    <span class="label">Nomor Antrian:</span>
                    <span>${orderData.queue_number}</span>
                </div>
                <div class="row">
                    <span class="label">Plat Kendaraan:</span>
                    <span>${orderData.license_plate}</span>
                </div>
                <div class="row">
                    <span class="label">Harga:</span>
                    <span>Rp ${orderData.final_price.toLocaleString('id-ID')}</span>
                </div>
                <div class="row">
                    <span class="label">Status Pembayaran:</span>
                    <span>Lunas</span>
                </div>
                <div class="row">
                    <span class="label">Waktu Verifikasi:</span>
                    <span>${orderData.payment_verified_at ? new Date(orderData.payment_verified_at).toLocaleString('id-ID') : '-'}</span>
                </div>
            </div>
            
            <div class="footer">
                <p>Terima kasih telah menggunakan layanan CarWash Connect</p>
                <p>Dicetak pada: ${new Date().toLocaleString('id-ID')}</p>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}

function deleteOrder(orderId) {
    if (confirm('Apakah Anda yakin ingin menghapus pesanan ini dari riwayat? Pesanan akan disembunyikan dari tampilan Anda.')) {
        // Create a form to submit DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route("booking.delete", ":id")); ?>'.replace(':id', orderId);
        
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

<?php echo $__env->make('layouts.carwash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/history.blade.php ENDPATH**/ ?>