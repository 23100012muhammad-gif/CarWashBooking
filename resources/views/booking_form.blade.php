@extends('layouts.carwash')

@section('title', 'Buat Pesanan - Car Wash Booking')

@section('content')
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
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('booking.store') }}" method="POST" id="booking-form">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="service_id" class="form-label">Jenis Layanan</label>
                            <select class="form-select" id="service_id" name="service_id" required>
                                <option value="">Pilih Layanan...</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" data-price="{{ $service->price }}" {{ $selectedService && $selectedService->id == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Diskon -->
                        <div class="mb-3" id="discount-section" style="display: none;">
                            <label for="discount_id" class="form-label">Pilih Diskon untuk Layanan Ini (Opsional)</label>
                            <select class="form-select" id="discount_id" name="discount_id">
                                <option value="">Tidak ada diskon</option>
                            </select>
                            <small class="text-muted">Diskon hanya berlaku untuk layanan yang sedang dipilih</small>
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
                        
                        <!-- Date Picker -->
                        <div class="mb-3">
                            <label for="booking_date" class="form-label">Tanggal Booking</label>
                            <input type="date" class="form-control" id="booking_date" name="booking_date" required 
                                   min="{{ date('Y-m-d') }}" max="{{ date('Y-m-d', strtotime('+14 days')) }}">
                            <small class="text-muted">Pilih tanggal dalam 14 hari ke depan</small>
                        </div>

                        <!-- Time Slots -->
                        <div class="mb-3" id="time-slots-section" style="display: none;">
                            <label for="slot_select" class="form-label">Pilih Jadwal & Waktu</label>
                            <select class="form-select" id="slot_select" name="selected_slot_time" required>
                                <option value="">Pilih waktu yang tersedia...</option>
                            </select>
                            <small class="text-muted">Pilih tanggal terlebih dahulu untuk melihat waktu yang tersedia</small>
                        </div>
                        
                        <!-- Booking Info -->
                        <div class="mb-3" id="booking-info" style="display: none;">
                            <div class="alert alert-info">
                                <h6><i class="bi bi-info-circle"></i> Informasi Booking</h6>
                                <div id="booking-details"></div>
                            </div>
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
                            <a href="{{ route('services') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .time-slot {
        cursor: pointer;
        transition: all 0.2s;
    }
    .time-slot:hover {
        transform: translateY(-2px);
    }
    .time-slot.selected {
        border-color: #0d6efd;
        background-color: #e7f1ff;
    }
    .time-slot.booked {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceSelect = document.getElementById('service_id');
    const bookingDate = document.getElementById('booking_date');
    const timeSlotsSection = document.getElementById('time-slots-section');
    const slotSelect = document.getElementById('slot_select');
    const bookingInfo = document.getElementById('booking-info');
    const bookingDetails = document.getElementById('booking-details');
    
    // Load time slots when date changes
    bookingDate.addEventListener('change', function() {
        if (this.value && serviceSelect.value) {
            loadTimeSlots(this.value);
        }
    });
    
    // Load time slots when service changes
    serviceSelect.addEventListener('change', function() {
        // Reset diskon saat layanan berubah
        document.getElementById('discount_id').value = '';
        
        if (this.value && bookingDate.value) {
            loadTimeSlots(bookingDate.value);
        }
        loadDiscounts();
        updatePriceSummary();
    });
    
    // Update price when discount changes
    document.getElementById('discount_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const discountServiceId = selectedOption.dataset.serviceId;
        const currentServiceId = serviceSelect.value;
        
        // Validasi bahwa diskon sesuai dengan layanan yang dipilih
        if (this.value && discountServiceId && discountServiceId !== currentServiceId) {
            alert('Diskon ini tidak berlaku untuk layanan yang dipilih!');
            this.value = '';
            return;
        }
        
        updatePriceSummary();
    });
    
    function loadDiscounts() {
        return new Promise(async (resolve) => {
        const serviceId = serviceSelect.value;
        if (!serviceId) {
            document.getElementById('discount-section').style.display = 'none';
            return;
        }
        
        try {
            const response = await fetch(`/api/discounts?service_id=${serviceId}`);
            const data = await response.json();
            
            const discountSelect = document.getElementById('discount_id');
            discountSelect.innerHTML = '<option value="">Tidak ada diskon</option>';
            
            if (data.length > 0) {
                data.forEach(discount => {
                    discountSelect.innerHTML += `<option value="${discount.id}" data-percent="${discount.percent}" data-service-id="${discount.service_id}">${discount.name} (${discount.percent}%)</option>`;
                });
                document.getElementById('discount-section').style.display = 'block';
            } else {
                document.getElementById('discount-section').style.display = 'none';
            }
            resolve();
        } catch (error) {
            console.error('Failed to load discounts:', error);
            resolve();
        }
        });
    }
    
    function updatePriceSummary() {
        const serviceSelect = document.getElementById('service_id');
        const discountSelect = document.getElementById('discount_id');
        
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
        
        if (discountPercent > 0) {
            document.getElementById('discount-row').style.display = 'flex';
        } else {
            document.getElementById('discount-row').style.display = 'none';
        }
        
        document.getElementById('price-summary').style.display = 'block';
    }
    
    async function loadTimeSlots(date) {
        const serviceId = serviceSelect.value;
        if (!serviceId || !date) return;
        
        try {
            const response = await fetch(`/api/slots-for-date?service_id=${serviceId}&date=${date}`);
            const data = await response.json();
            
            slotSelect.innerHTML = '<option value="">Pilih waktu yang tersedia...</option>';
            
            if (data.status === 'success' && data.data.length > 0) {
                data.data.forEach(slot => {
                    const isAvailable = slot.status === 'tersedia' && slot.tersedia > 0;
                    if (isAvailable) {
                        const optionText = `${slot.jam_mulai} - ${slot.jam_selesai} (${slot.tersedia} slot tersisa)`;
                        const optionValue = `${slot.jam_mulai} - ${slot.jam_selesai}`;
                        slotSelect.innerHTML += `<option value="${optionValue}" data-tersedia="${slot.tersedia}">${optionText}</option>`;
                    }
                });
                
                if (slotSelect.options.length === 1) {
                    slotSelect.innerHTML += '<option value="" disabled>Tidak ada slot tersedia</option>';
                }
            } else {
                slotSelect.innerHTML += '<option value="" disabled>Tidak ada slot tersedia untuk tanggal ini</option>';
            }
            
            timeSlotsSection.style.display = 'block';
        } catch (error) {
            console.error('Failed to load time slots:', error);
            slotSelect.innerHTML = '<option value="" disabled>Gagal memuat slot waktu</option>';
            timeSlotsSection.style.display = 'block';
        }
    }
    
    // Handle slot selection
    slotSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            const tersedia = selectedOption.dataset.tersedia;
            
            // Show booking info
            const service = serviceSelect.options[serviceSelect.selectedIndex].text;
            const date = new Date(bookingDate.value).toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            bookingDetails.innerHTML = `
                <div><strong>Layanan:</strong> ${service}</div>
                <div><strong>Tanggal:</strong> ${date}</div>
                <div><strong>Waktu:</strong> ${this.value}</div>
                <div><strong>Slot tersisa:</strong> ${tersedia}</div>
            `;
            
            bookingInfo.style.display = 'block';
        } else {
            bookingInfo.style.display = 'none';
        }
    });
    
    // Auto-fill dari URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const serviceId = urlParams.get('service_id');
    const discountId = urlParams.get('discount_id');
    
    if (serviceId) {
        serviceSelect.value = serviceId;
        loadDiscounts().then(() => {
            if (discountId) {
                document.getElementById('discount_id').value = discountId;
            }
            updatePriceSummary();
        });
    }
    
    // If service is pre-selected, trigger change
    if (serviceSelect.value && bookingDate.value) {
        loadTimeSlots(bookingDate.value);
    }
});
</script>
@endpush
