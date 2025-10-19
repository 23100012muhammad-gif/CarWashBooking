@extends('layouts.app')

@section('title', 'Layanan - Car Wash Booking')

@section('content')
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-list-check"></i> Daftar Layanan Kami
    </h2>
    
    <div class="row">
        @foreach($services as $service)
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary">
                        <i class="bi bi-check-circle"></i> {{ $service->name }}
                    </h5>
                    <p class="card-text">{{ $service->description }}</p>
                    <h4 class="text-success">Rp {{ number_format($service->price, 0, ',', '.') }}</h4>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('booking.create') }}" class="btn btn-primary">
                        <i class="bi bi-cart-plus"></i> Pesan Layanan Ini
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="alert alert-info mt-4" role="alert">
        <i class="bi bi-info-circle"></i> 
        <strong>Informasi:</strong> Semua layanan dilakukan oleh tenaga profesional dengan peralatan modern dan ramah lingkungan.
    </div>
</div>
@endsection
