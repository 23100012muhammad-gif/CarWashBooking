

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Buat Pesanan Baru</h1>

        <!-- Step Progress -->
        <div class="flex justify-between mb-8">
            <div class="flex-1 text-center">
                <div class="w-8 h-8 mx-auto rounded-full bg-blue-500 text-white flex items-center justify-center mb-2 step-1">1</div>
                <div class="text-sm">Pilih Jadwal</div>
            </div>
            <div class="flex-1 text-center">
                <div class="w-8 h-8 mx-auto rounded-full bg-gray-300 text-white flex items-center justify-center mb-2 step-2">2</div>
                <div class="text-sm">Detail Pesanan</div>
            </div>
            <div class="flex-1 text-center">
                <div class="w-8 h-8 mx-auto rounded-full bg-gray-300 text-white flex items-center justify-center mb-2 step-3">3</div>
                <div class="text-sm">Pembayaran</div>
            </div>
        </div>

        <?php if($errors->any()): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                <ul class="list-disc list-inside">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Booking Form with Steps -->
        <div class="bg-white rounded-lg shadow p-6">
            <form id="booking-form" class="space-y-6">
                <?php echo csrf_field(); ?>
                <!-- Step 1: Date & Time Selection -->
                <div id="step-1">
                    <h2 class="text-lg font-semibold mb-4">Pilih Tanggal dan Waktu</h2>
                    
                    <!-- Date Picker -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Tanggal</label>
                        <input type="text" id="booking-date" 
                               class="w-full border rounded px-3 py-2" 
                               placeholder="Pilih tanggal" readonly>
                        <p class="text-sm text-gray-500 mt-1">
                            * Hanya menampilkan tanggal yang tersedia dalam 7 hari ke depan
                        </p>
                    </div>

                    <!-- Time Slots -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Waktu</label>
                        <div id="time-slots" class="grid grid-cols-3 gap-2">
                            <!-- Will be populated by JavaScript -->
                        </div>
                        <input type="hidden" id="booking_slot_id" name="booking_slot_id">
                    </div>
                </div>

                <!-- Step 2: Order Details -->
                <div id="step-2" class="hidden">
                    <h2 class="text-lg font-semibold mb-4">Detail Pesanan</h2>
                    
                    <!-- Service Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Pilih Layanan</label>
                        <select name="layanan_id" class="w-full border rounded px-3 py-2">
                            <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($service->id); ?>" data-price="<?php echo e($service->price); ?>">
                                <?php echo e($service->name); ?> - Rp <?php echo e(number_format($service->price, 0, ',', '.')); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Vehicle Details -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Nomor Plat</label>
                        <input type="text" name="plat_nomor" 
                               class="w-full border rounded px-3 py-2"
                               placeholder="Contoh: B 1234 ABC"
                               value="<?php echo e(old('plat_nomor')); ?>">
                    </div>

                    <!-- Discount Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Kode Promo (opsional)</label>
                        <input type="text" name="kode_promo" 
                               class="w-full border rounded px-3 py-2"
                               placeholder="Masukkan kode promo">
                        <div id="promo-info" class="mt-2 text-sm"></div>
                    </div>
                </div>

                <!-- Step 3: Payment -->
                <div id="step-3" class="hidden">
                    <h2 class="text-lg font-semibold mb-4">Pembayaran</h2>
                    
                    <!-- Price Summary -->
                    <div class="bg-gray-50 p-4 rounded mb-6">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Harga Layanan</span>
                                <span id="base-price">Rp 0</span>
                            </div>
                            <div class="flex justify-between text-green-600">
                                <span>Diskon</span>
                                <span id="discount-amount">- Rp 0</span>
                            </div>
                            <div class="flex justify-between font-bold pt-2 border-t">
                                <span>Total</span>
                                <span id="final-price">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Metode Pembayaran</label>
                        <select name="payment_method" class="w-full border rounded px-3 py-2">
                            <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($method->id); ?>"><?php echo e($method->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between pt-4">
                    <button type="button" id="prev-step" 
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded hidden">
                        Kembali
                    </button>
                    <button type="button" id="next-step" 
                            class="bg-blue-500 text-white px-4 py-2 rounded">
                        Lanjut
                    </button>
                    <button type="submit" id="submit-booking" 
                            class="bg-green-500 text-white px-4 py-2 rounded hidden">
                        Buat Pesanan
                    </button>
                </div>
            </form>
        </div>
</div>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="<?php echo e(asset('js/booking.js')); ?>"></script>
<script>
let currentStep = 1;
const totalSteps = 3;

function updateStepIndicators() {
    for (let i = 1; i <= totalSteps; i++) {
        const indicator = document.querySelector(`.step-${i}`);
        if (i === currentStep) {
            indicator.classList.remove('bg-gray-300');
            indicator.classList.add('bg-blue-500');
        } else if (i < currentStep) {
            indicator.classList.remove('bg-gray-300');
            indicator.classList.add('bg-green-500');
        } else {
            indicator.classList.remove('bg-blue-500', 'bg-green-500');
            indicator.classList.add('bg-gray-300');
        }
    }
}

function showStep(step) {
    document.querySelectorAll('[id^="step-"]').forEach(el => {
        el.classList.add('hidden');
    });
    document.getElementById(`step-${step}`).classList.remove('hidden');

    const prevButton = document.getElementById('prev-step');
    const nextButton = document.getElementById('next-step');
    const submitButton = document.getElementById('submit-booking');

    prevButton.classList.toggle('hidden', step === 1);
    nextButton.classList.toggle('hidden', step === totalSteps);
    submitButton.classList.toggle('hidden', step !== totalSteps);

    currentStep = step;
    updateStepIndicators();
}

function validateStep(step) {
    if (step === 1) {
        const slotId = document.getElementById('booking_slot_id').value;
        if (!slotId) {
            showAlert('error', 'Silakan pilih waktu booking terlebih dahulu');
            return false;
        }
    } else if (step === 2) {
        const platNomor = document.querySelector('[name="plat_nomor"]').value;
        if (!platNomor) {
            showAlert('error', 'Silakan masukkan nomor plat kendaraan');
            return false;
        }
    }
    return true;
}

// Fungsi untuk menampilkan alert
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = type === 'error' 
        ? 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'
        : 'bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4';
    alertDiv.textContent = message;

    const form = document.getElementById('booking-form');
    form.insertBefore(alertDiv, form.firstChild);

    setTimeout(() => alertDiv.remove(), 5000);
}

// Event Listeners
document.getElementById('next-step').addEventListener('click', () => {
    if (validateStep(currentStep)) {
        showStep(currentStep + 1);
    }
});

document.getElementById('prev-step').addEventListener('click', () => {
    showStep(currentStep - 1);
});

document.getElementById('booking-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!validateStep(currentStep)) return;

    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('/booking', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(Object.fromEntries(formData))
        });

        const data = await response.json();
        
        if (data.status === 'success') {
            window.location.href = `/booking/${data.data.id}/payment`;
        } else {
            showAlert('error', data.message || 'Gagal membuat pesanan');
        }
    } catch (error) {
        console.error('Failed to create booking:', error);
        showAlert('error', 'Gagal membuat pesanan');
    }
});

// Initialize
showStep(1);
</script>
<?php $__env->stopPush(); ?>
</script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/booking/create.blade.php ENDPATH**/ ?>