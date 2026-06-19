<?php $__env->startSection('title', 'Profil Admin - Admin Panel'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Profile Header -->
            <div class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    <div class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; font-size: 3rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h3 class="mb-1"><?php echo e(Auth::user()->name); ?></h3>
                    <p class="text-muted mb-0"><?php echo e(Auth::user()->email); ?></p>
                    <small class="text-muted">Administrator sejak <?php echo e(Auth::user()->created_at->format('M Y')); ?></small>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square"></i> Edit Profil Admin
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo e(route('admin.profile.update')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo e(Auth::user()->name); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo e(Auth::user()->email); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-dark w-100">
                                    <i class="bi bi-save"></i> Simpan Perubahan
                                </button>
                            </div>
                            <div class="col-md-6">
                                <form method="POST" action="<?php echo e(route('admin.logout')); ?>" class="d-inline w-100">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-box-arrow-right"></i> Logout Admin
                                    </button>
                                </form>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin_master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/admin/profile.blade.php ENDPATH**/ ?>