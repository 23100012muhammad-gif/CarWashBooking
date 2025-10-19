@extends('layouts.admin_master')

@section('title', 'Kelola Pesanan - Admin Panel')

@section('content')
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-clipboard-check"></i> Kelola Pesanan
    </h2>
    
    @if($orders->isEmpty())
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> Belum ada pesanan yang masuk.
        </div>
    @else
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No. Antrean</th>
                                <th>Layanan</th>
                                <th>Plat Nomor</th>
                                <th>Tanggal Booking</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td><strong class="text-primary">{{ $order->queue_number }}</strong></td>
                                <td>{{ $order->service_type }}</td>
                                <td>{{ $order->license_plate }}</td>
                                <td>{{ $order->booking_date->format('d M Y H:i') }}</td>
                                <td>
                                    @if($order->status == 'Menunggu')
                                        <span class="badge bg-warning text-dark">{{ $order->status }}</span>
                                    @elseif($order->status == 'Proses')
                                        <span class="badge bg-info">{{ $order->status }}</span>
                                    @else
                                        <span class="badge bg-success">{{ $order->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                            <option value="Menunggu" {{ $order->status == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                                            <option value="Proses" {{ $order->status == 'Proses' ? 'selected' : '' }}>Proses</option>
                                            <option value="Selesai" {{ $order->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info mt-4" role="alert">
            <i class="bi bi-info-circle"></i> 
            <strong>Petunjuk:</strong> Pilih status baru pada dropdown untuk mengubah status pesanan secara otomatis.
        </div>
    @endif
</div>
@endsection
