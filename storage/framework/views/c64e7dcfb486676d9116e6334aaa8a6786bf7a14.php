<?php $__env->startSection('title', 'Notifikasi - CarWash Connect'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-bell"></i> Notifikasi
    </h2>
    
    <?php if($notifications->isEmpty()): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Belum ada notifikasi.
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card mb-3 <?php echo e($notification->is_read ? '' : 'border-primary'); ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="card-title <?php echo e($notification->is_read ? '' : 'fw-bold'); ?>">
                                    <?php echo e($notification->title); ?>

                                    <?php if(!$notification->is_read): ?>
                                        <span class="badge bg-primary ms-2">Baru</span>
                                    <?php endif; ?>
                                </h6>
                                <p class="card-text"><?php echo e($notification->message); ?></p>
                                <small class="text-muted"><?php echo e($notification->created_at->diffForHumans()); ?></small>
                            </div>
                            <div class="btn-group">
                                <?php if(!$notification->is_read): ?>
                                    <button class="btn btn-sm btn-outline-primary" onclick="markAsRead(<?php echo e($notification->id); ?>)">
                                        Tandai Dibaca
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteNotification(<?php echo e($notification->id); ?>)">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
async function markAsRead(notificationId) {
    try {
        await fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        location.reload();
    } catch (error) {
        console.error('Error:', error);
    }
}

async function deleteNotification(notificationId) {
    if (!confirm('Hapus notifikasi ini?')) return;
    
    try {
        await fetch(`/notifications/${notificationId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        location.reload();
    } catch (error) {
        console.error('Error:', error);
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.carwash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/notifications/index.blade.php ENDPATH**/ ?>