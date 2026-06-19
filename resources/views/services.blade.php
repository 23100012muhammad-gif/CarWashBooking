@extends('layouts.carwash')

@section('title', 'Layanan - Car Wash Booking')

@section('content')
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-list-check"></i> Daftar Layanan Kami
    </h2>

    @if(isset($activeDiscounts) && $activeDiscounts->count())
    <div class="alert alert-success">
        <strong>Promo Aktif:</strong>
        @foreach($activeDiscounts as $disc)
            <span class="badge bg-success me-2">{{ $disc->code }} - {{ $disc->percent }}% (s.d. {{ $disc->expires_at->format('d M Y') }})</span>
        @endforeach
    </div>
    @endif

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
                    
                    @php
                        $serviceDiscounts = $activeDiscounts->where('service_id', $service->id);
                    @endphp
                    
                    @if($serviceDiscounts->count())
                        <div class="mt-3">
                            <h6 class="text-warning">
                                <i class="bi bi-percent"></i> Diskon Tersedia:
                            </h6>
                            @foreach($serviceDiscounts as $discount)
                                @php
                                    $originalPrice = $service->price;
                                    $discountAmount = floor($originalPrice * ($discount->percent / 100));
                                    $finalPrice = max(0, $originalPrice - $discountAmount);
                                @endphp
                                <div class="alert alert-warning py-2 mb-2">
                                    <strong>{{ $discount->name }}</strong> - {{ $discount->percent }}% OFF<br>
                                    <small>{{ $discount->description }}</small><br>
                                    <div class="mt-1">
                                        <small class="text-muted">Normal: <span class="text-decoration-line-through">Rp {{ number_format($originalPrice, 0, ',', '.') }}</span></small><br>
                                        <strong class="text-success">Setelah diskon: Rp {{ number_format($finalPrice, 0, ',', '.') }}</strong><br>
                                        <small class="text-success">Hemat: Rp {{ number_format($discountAmount, 0, ',', '.') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('booking.create', ['service_id' => $service->id]) }}" class="btn btn-primary">
                        <i class="bi bi-cart-plus"></i> Pesan Layanan Ini
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection


