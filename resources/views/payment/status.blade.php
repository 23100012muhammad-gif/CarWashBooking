@extends('layouts.carwash')

@section('title', 'Status Pembayaran - Car Wash Booking')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-check-circle"></i> Status Pembayaran
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Order Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-receipt"></i> Rincian Pesanan
                            </h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nomor Pesanan:</strong></td>
                                    <td>#{{ $order->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Layanan:</strong></td>
                                    <td>{{ $order->service_type }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal & Waktu:</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($order->booking_date)->format('d M Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nomor Antrian:</strong></td>
                                    <td>{{ $order->queue_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Pembayaran:</strong></td>
                                    <td class="h6 text-primary">Rp {{ number_format($order->final_price, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-credit-card"></i> Status Pembayaran
                            </h5>
                            
                            @if($order->isPaymentVerified())
                                <div class="alert alert-success">
                                    <h6 class="alert-heading">
                                        <i class="bi bi-check-circle-fill"></i> Pembayaran Berhasil
                                    </h6>
                                    <p class="mb-0">
                                        Pembayaran Anda telah diverifikasi dan pesanan telah dikonfirmasi.
                                        Silakan datang sesuai jadwal yang telah ditentukan.
                                    </p>
                                </div>
                                
                                @if($order->payment_verified_at)
                                <p class="mb-1"><strong>Waktu Verifikasi:</strong></p>
                                <p class="mb-1">{{ \Carbon\Carbon::parse($order->payment_verified_at)->format('d M Y, H:i') }}</p>
                                @endif
                                
                                @if($order->verifier)
                                <p class="mb-1"><strong>Diverifikasi oleh:</strong></p>
                                <p class="mb-0">{{ $order->verifier->name }}</p>
                                @endif
                                
                            @elseif($order->isPaymentFailed())
                                <div class="alert alert-danger">
                                    <h6 class="alert-heading">
                                        <i class="bi bi-x-circle-fill"></i> Pembayaran Gagal
                                    </h6>
                                    <p class="mb-0">
                                        Pembayaran Anda tidak dapat diproses. Silakan coba lagi atau gunakan metode pembayaran lain.
                                    </p>
                                </div>
                                
                            @else
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading">
                                        <i class="bi bi-clock-fill"></i> Menunggu Pembayaran
                                    </h6>
                                    <p class="mb-0">
                                        Pembayaran Anda sedang diproses. Silakan tunggu konfirmasi dari tim kami.
                                    </p>
                                </div>
                            @endif
                            
                            <div class="mt-3">
                                <span class="badge bg-{{ $order->getPaymentStatusClass() }} fs-6">
                                    {{ $order->getPaymentStatusLabel() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Details -->
                    @if($order->payment_method)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-wallet2"></i> Metode Pembayaran
                            </h5>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Metode:</strong></p>
                                            <p class="mb-1">
                                                @if($order->payment_method === 'bank_transfer')
                                                    <i class="bi bi-bank text-primary"></i> Transfer Bank
                                                @else
                                                    <i class="bi bi-phone text-success"></i> E-Wallet
                                                @endif
                                            </p>
                                        </div>
                                        @if($order->payment_reference)
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Referensi:</strong></p>
                                            <p class="mb-1">{{ $order->payment_reference }}</p>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    @if($order->hasPaymentProof())
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <p class="mb-1"><strong>Bukti Pembayaran:</strong></p>
                                            <a href="{{ Storage::url($order->payment_proof) }}" 
                                               class="btn btn-outline-primary btn-sm" target="_blank">
                                                <i class="bi bi-eye"></i> Lihat Bukti
                                            </a>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if($order->payment_notes)
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <p class="mb-1"><strong>Catatan:</strong></p>
                                            <p class="mb-0">{{ $order->payment_notes }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                        <a href="{{ route('booking.history') }}" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-clock-history"></i> Lihat Riwayat
                        </a>
                        
                        @if($order->isPaymentVerified())
                            <a href="{{ route('booking.status') }}" 
                               class="btn btn-primary">
                                <i class="bi bi-list-check"></i> Status Antrian
                            </a>
                        @else
                            <a href="{{ route('payment.status', $order->id) }}" 
                               class="btn btn-primary">
                                <i class="bi bi-arrow-clockwise"></i> Refresh Status
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!$order->isPaymentVerified() && !$order->isPaymentFailed())
<script>
// Auto-refresh every 30 seconds for pending payments
setTimeout(function() {
    window.location.reload();
}, 30000);
</script>
@endif
@endsection

