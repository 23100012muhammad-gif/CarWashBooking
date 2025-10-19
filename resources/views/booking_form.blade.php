@extends('layouts.app')

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
                    
                    <form action="{{ route('booking.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="service_type" class="form-label">Jenis Layanan</label>
                            <select class="form-select" id="service_type" name="service_type" required>
                                <option value="">Pilih Layanan...</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->name }}">
                                        {{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="license_plate" class="form-label">Nomor Plat Kendaraan</label>
                            <input type="text" class="form-control" id="license_plate" name="license_plate" 
                                   placeholder="Contoh: B 1234 XYZ" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="booking_date" class="form-label">Tanggal & Waktu Booking</label>
                            <input type="datetime-local" class="form-control" id="booking_date" name="booking_date" required>
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
