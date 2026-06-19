@extends('layouts.carwash')

@section('title', 'Home - Car Wash Booking')

@section('content')
<div class="container">
    <div class="jumbotron bg-light p-5 rounded-3 mb-4">
        <h1 class="display-4">Selamat Datang di Car Wash Booking</h1>
        <p class="lead">Layanan cuci mobil profesional dengan sistem antrean online yang mudah dan efisien.</p>
        <hr class="my-4">
        <p>Pesan layanan cuci mobil Anda sekarang dan dapatkan nomor antrean otomatis!</p>
        <a class="btn btn-primary btn-lg" href="{{ route('booking.create') }}" role="button">
            <i class="bi bi-calendar-check"></i> Pesan Sekarang
        </a>
        <a class="btn btn-outline-primary btn-lg" href="{{ route('services') }}" role="button">
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

    @isset($services)
    <h3 class="mt-4 mb-3">Layanan Populer</h3>
    <div class="row">
        @foreach($services as $service)
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">{{ $service->name }}</h5>
                    <p class="card-text">{{ $service->description }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-success fw-bold">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                        <a class="btn btn-sm btn-primary" href="{{ route('booking.create', ['service_id' => $service->id]) }}">Pilih</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endisset

    @isset($activeDiscounts)
    @if($activeDiscounts->count())
    <h3 class="mt-4 mb-3">Promo Diskon Tersedia</h3>
    <div class="row">
        @foreach($activeDiscounts as $discount)
        <div class="col-md-4 mb-3">
            <div class="card border-success h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-percent"></i> {{ $discount->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <h4 class="text-success">{{ $discount->percent }}% OFF</h4>
                    @if($discount->service)
                        @php
                            $originalPrice = $discount->service->price;
                            $discountAmount = floor($originalPrice * ($discount->percent / 100));
                            $finalPrice = max(0, $originalPrice - $discountAmount);
                        @endphp
                        <p class="card-text">
                            <strong>Untuk layanan:</strong> {{ $discount->service->name }}<br>
                            <div class="mt-2">
                                <small class="text-muted">Harga normal: <span class="text-decoration-line-through">Rp {{ number_format($originalPrice, 0, ',', '.') }}</span></small><br>
                                <strong class="text-success">Harga setelah diskon: Rp {{ number_format($finalPrice, 0, ',', '.') }}</strong><br>
                                <small class="text-success">Anda hemat: Rp {{ number_format($discountAmount, 0, ',', '.') }}</small>
                            </div>
                        </p>
                    @endif
                    <p class="card-text">{{ $discount->description }}</p>
                    <small class="text-muted">Berlaku sampai: {{ $discount->expires_at->format('d M Y') }}</small>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('booking.create', ['service_id' => $discount->service_id, 'discount_id' => $discount->id]) }}" class="btn btn-success w-100">
                        <i class="bi bi-cart-plus"></i> Pilih Diskon Ini
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
    @endisset
</div>
@endsection
