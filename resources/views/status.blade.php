@extends('layouts.app')

@section('title', 'Status Pesanan - Car Wash Booking')

@section('content')
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-clock-history"></i> Status Pesanan Aktif
    </h2>
    
    @if($activeOrders->isEmpty())
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> Tidak ada pesanan aktif saat ini.
        </div>
        <a href="{{ route('booking.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Buat Pesanan Baru
        </a>
    @else
        <div class="row">
            @foreach($activeOrders as $order)
            <div class="col-md-6 mb-4">
                @include('partials.status_card', ['order' => $order])
            </div>
            @endforeach
        </div>
        
        <div class="alert alert-warning mt-4" role="alert">
            <i class="bi bi-exclamation-triangle"></i> 
            <strong>Catatan:</strong> Halaman ini akan diperbarui otomatis. Silakan refresh untuk melihat status terkini.
        </div>
    @endif
</div>
@endsection
