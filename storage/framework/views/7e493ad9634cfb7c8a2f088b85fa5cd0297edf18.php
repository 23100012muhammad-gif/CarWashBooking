<?php $__env->startSection('title', 'Home - Car Wash Booking'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="jumbotron bg-light p-5 rounded-3 mb-4">
        <h1 class="display-4">Selamat Datang di Car Wash Booking</h1>
        <p class="lead">Layanan cuci mobil profesional dengan sistem antrean online yang mudah dan efisien.</p>
        <hr class="my-4">
        <p>Pesan layanan cuci mobil Anda sekarang dan dapatkan nomor antrean otomatis!</p>
        <a class="btn btn-primary btn-lg" href="<?php echo e(route('booking.create')); ?>" role="button">
            <i class="bi bi-calendar-check"></i> Pesan Sekarang
        </a>
        <a class="btn btn-outline-primary btn-lg" href="<?php echo e(route('services')); ?>" role="button">
            <i class="bi bi-list-ul"></i> Lihat Layanan
        </a>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-clock-history display-4 text-primary"></i>
                    <h5 class="card-title mt-3">Hemat Waktu</h5>
                    <p class="card-text">Sistem antrean online memudahkan Anda mengetahui waktu tunggu</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-star-fill display-4 text-warning"></i>
                    <h5 class="card-title mt-3">Layanan Berkualitas</h5>
                    <p class="card-text">Tenaga profesional dan peralatan modern untuk hasil terbaik</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cash-coin display-4 text-success"></i>
                    <h5 class="card-title mt-3">Harga Terjangkau</h5>
                    <p class="card-text">Berbagai paket layanan dengan harga yang kompetitif</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/runner/workspace/resources/views/home.blade.php ENDPATH**/ ?>