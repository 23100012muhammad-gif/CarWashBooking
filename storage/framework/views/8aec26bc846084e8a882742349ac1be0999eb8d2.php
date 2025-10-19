<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?php echo e(route('home')); ?>">
            <i class="bi bi-car-front-fill"></i> Car Wash Booking
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('home')); ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('services')); ?>">Layanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('booking.create')); ?>">Pesan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('booking.status')); ?>">Status Pesanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('booking.history')); ?>">Riwayat</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('profile')); ?>">Profil</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php /**PATH /home/runner/workspace/resources/views/partials/navbar.blade.php ENDPATH**/ ?>