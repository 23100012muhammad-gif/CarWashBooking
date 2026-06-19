<?php $__env->startSection('title', 'Buat Pesanan - Car Wash Booking'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar-plus"></i> Form Pemesanan Layanan
                    </h4>
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
                    
                    <form action="<?php echo e(route('booking.store')); ?>" method="POST" id="booking-form">
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-3">
                            <label for="service_id" class="form-label">Jenis Layanan</label>
                            <select class="form-select" id="service_id" name="service_id" required>
                                <option value="">Pilih Layanan...</option>
                                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($service->id); ?>" data-price="<?php echo e($service->price); ?>" <?php echo e($selectedService && $selectedService->id == $service->id ? 'selected' : ''); ?>>
                                        <?php echo e($service->name); ?> - Rp <?php echo e(number_format($service->price, 0, ',', '.')); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        
                        <!-- Diskon -->
                        <div class="mb-3" id="discount-section" style="display: none;">
                            <label for="discount_id" class="form-label">Pilih Diskon (Opsional)</label>
                            <select class="form-select" id="discount_id" name="discount_id">
                                <option value="">Tidak ada diskon</option>
                            </select>
                        </div>
                        
                        <!-- Price Summary -->
                        <div class="mb-3" id="price-summary" style="display: none;">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Ringkasan Harga</h6>
                                    <div class="d-flex justify-content-between">
                                        <span>Harga Layanan:</span>
                                        <span id="original-price">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between" id="discount-row" style="display: none;">
                                        <span>Diskon:</span>
                                        <span id="discount-amount" class="text-success">- Rp 0</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Total Bayar:</span>
                                        <span id="final-price">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pilih Jadwal & Slot -->
                        <div class="mb-3">
                            <label for="slot_select" class="form-label">Pilih Jadwal & Waktu</label>
                            <select class="form-select" id="slot_select" name="selected_slot_id" required>
                                <option value="">Pilih layanan terlebih dahulu...</option>
                            </select>
                            <small class="text-muted">Pilih tanggal dan waktu yang tersedia</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="license_plate" class="form-label">Nomor Plat Kendaraan</label>
                            <input type="text" class="form-control" id="license_plate" name="license_plate" 
                                   placeholder="Contoh: B 1234 XYZ" required>
                        </div>
                        
                        <!-- Customer Info -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label">Nama (Opsional)</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                       placeholder="Nama Anda">
                            </div>
                            <div class="col-md-6">
                                <label for="customer_phone" class="form-label">No. HP (Opsional)</label>
                                <input type="text" class="form-control" id="customer_phone" name="customer_phone" 
                                       placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Buat Pesanan
                            </button>
                            <a href="<?php echo e(route('services')); ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceSelect = document.getElementById('service_id');
    const discountSelect = document.getElementById('discount_id');
    const slotSelect = document.getElementById('slot_select');
    
    serviceSelect.addEventListener('change', function() {
        loadDiscounts();
        updatePriceSummary();
        loadScheduleSlots();
    });
    
    discountSelect.addEventListener('change', updatePriceSummary);
    
    async function loadDiscounts() {
        const serviceId = serviceSelect.value;
        if (!serviceId) {
            document.getElementById('discount-section').style.display = 'none';
            return;
        }
        
        try {
            const response = await fetch(`/api/discounts?service_id=${serviceId}`);
            const data = await response.json();
            
            discountSelect.innerHTML = '<option value="">Tidak ada diskon</option>';
            
            if (data.length > 0) {
                data.forEach(discount => {
                    discountSelect.innerHTML += `<option value="${discount.id}" data-percent="${discount.percent}">${discount.name} (${discount.percent}%)</option>`;
                });
                document.getElementById('discount-section').style.display = 'block';
            }
        } catch (error) {
            console.error('Failed to load discounts:', error);
        }
    }
    
    function updatePriceSummary() {
        if (!serviceSelect.value) {
            document.getElementById('price-summary').style.display = 'none';
            return;
        }
        
        const originalPrice = parseInt(serviceSelect.options[serviceSelect.selectedIndex].dataset.price || 0);
        const discountPercent = parseInt(discountSelect.options[discountSelect.selectedIndex]?.dataset.percent || 0);
        
        const discountAmount = Math.floor(originalPrice * (discountPercent / 100));
        const finalPrice = originalPrice - discountAmount;
        
        document.getElementById('original-price').textContent = 'Rp ' + originalPrice.toLocaleString('id-ID');
        document.getElementById('discount-amount').textContent = '- Rp ' + discountAmount.toLocaleString('id-ID');
        document.getElementById('final-price').textContent = 'Rp ' + finalPrice.toLocaleString('id-ID');
        
        document.getElementById('discount-row').style.display = discountPercent > 0 ? 'flex' : 'none';
        document.getElementById('price-summary').style.display = 'block';
    }
    
    async function loadScheduleSlots() {
        const serviceId = serviceSelect.value;
        if (!serviceId) {
            slotSelect.innerHTML = '<option value="">Pilih layanan terlebih dahulu...</option>';
            return;
        }
        
        slotSelect.innerHTML = '<option value="">Memuat jadwal...</option>';
        
        try {
            const response = await fetch('/api/available-schedules');
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            slotSelect.innerHTML = '<option value="">Pilih jadwal & waktu...</option>';
            
            if (data.status === 'success' && data.data && data.data.length > 0) {
                data.data.forEach(schedule => {
                    schedule.slots.forEach(slot => {
                        if (slot.tersedia > 0) {
                            const optionText = `${schedule.tanggal_formatted} (${schedule.hari}) - ${slot.jam_mulai}-${slot.jam_selesai} (${slot.tersedia} slot)`;
                            slotSelect.innerHTML += `<option value="${slot.id}">${optionText}</option>`;
                        }
                    });
                });
                
                if (slotSelect.options.length === 1) {
                    slotSelect.innerHTML += '<option value="" disabled>Tidak ada slot tersedia</option>';
                }
            } else {
                slotSelect.innerHTML += '<option value="" disabled>Belum ada jadwal tersedia</option>';
            }
        } catch (error) {
            console.error('Error loading schedules:', error);
            slotSelect.innerHTML = '<option value="" disabled>Error memuat jadwal</option>';
        }
    }
    

    
    // Auto-fill dari URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const serviceId = urlParams.get('service_id');
    const discountId = urlParams.get('discount_id');
    
    if (serviceId) {
        serviceSelect.value = serviceId;
        loadDiscounts().then(() => {
            if (discountId) {
                discountSelect.value = discountId;
            }
            updatePriceSummary();
            loadScheduleSlots();
        });
    }
});
</script>

<style>
.slot-card.selected {
    background-color: #e7f1ff;
}
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.carwash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/booking_form_simple.blade.php ENDPATH**/ ?>