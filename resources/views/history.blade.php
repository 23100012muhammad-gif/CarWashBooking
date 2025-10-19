@extends('layouts.app')

@section('title', 'Riwayat Pesanan - Car Wash Booking')

@section('content')
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-journal-text"></i> Riwayat Pesanan Selesai
    </h2>
    
    @if($completedOrders->isEmpty())
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> Belum ada riwayat pesanan yang selesai.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nomor Antrean</th>
                        <th>Jenis Layanan</th>
                        <th>Plat Nomor</th>
                        <th>Tanggal Booking</th>
                        <th>Status</th>
                        <th>Selesai Pada</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($completedOrders as $order)
                    <tr>
                        <td><strong>{{ $order->queue_number }}</strong></td>
                        <td>{{ $order->service_type }}</td>
                        <td>{{ $order->license_plate }}</td>
                        <td>{{ $order->booking_date->format('d M Y H:i') }}</td>
                        <td><span class="badge bg-success">{{ $order->status }}</span></td>
                        <td>{{ $order->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
