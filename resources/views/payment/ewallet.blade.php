@extends('layouts.carwash')

@section('title', 'E-Wallet Payment - Car Wash Booking')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-phone"></i> Pembayaran E-Wallet
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Order Summary -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle"></i> Rincian Pembayaran
                        </h6>
                        <p class="mb-0">
                            <strong>Nomor Pesanan:</strong> #{{ $order->id }} | 
                            <strong>Total:</strong> Rp {{ number_format($order->final_price, 0, ',', '.') }}
                        </p>
                    </div>

                    <!-- QR Code Payment -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-success mb-3">
                                <i class="bi bi-qr-code"></i> Scan QR Code
                            </h5>
                            <div class="text-center">
                                <div class="card border-success">
                                    <div class="card-body">
                                        <img src="{{ $paymentData['qr_code'] }}" alt="QR Code Payment" 
                                             class="img-fluid" style="max-width: 200px;">
                                        <p class="mt-2 mb-0 text-muted">Scan dengan aplikasi e-wallet Anda</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="text-success mb-3">
                                <i class="bi bi-link-45deg"></i> Link Pembayaran
                            </h5>
                            <div class="card border-success">
                                <div class="card-body">
                                    <p class="mb-3">Klik link di bawah untuk membuka halaman pembayaran:</p>
                                    <a href="{{ $paymentData['payment_url'] }}" 
                                       class="btn btn-success btn-lg w-100" target="_blank">
                                        <i class="bi bi-wallet2"></i> Bayar Sekarang
                                    </a>
                                    <p class="mt-2 mb-0 text-muted small">
                                        Link akan terbuka di tab baru
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Instructions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-success mb-3">
                                <i class="bi bi-list-ol"></i> Cara Pembayaran
                            </h5>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-success">Via QR Code:</h6>
                                            <ol class="mb-0">
                                                <li>Buka aplikasi e-wallet (GoPay/OVO/Dana)</li>
                                                <li>Pilih menu "Scan QR"</li>
                                                <li>Scan QR code di atas</li>
                                                <li>Konfirmasi pembayaran</li>
                                            </ol>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-success">Via Link:</h6>
                                            <ol class="mb-0">
                                                <li>Klik tombol "Bayar Sekarang"</li>
                                                <li>Pilih metode e-wallet</li>
                                                <li>Masukkan PIN/Password</li>
                                                <li>Konfirmasi pembayaran</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <h6 class="alert-heading">
                                    <i class="bi bi-clock"></i> Status Pembayaran
                                </h6>
                                <p class="mb-2">
                                    <strong>Batas Waktu:</strong> {{ $paymentData['expires_at'] }}
                                </p>
                                <p class="mb-0">
                                    Pembayaran akan diverifikasi secara otomatis setelah transaksi berhasil.
                                    Jika pembayaran gagal, silakan coba lagi atau gunakan metode lain.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                        <a href="{{ route('payment.confirmation', $order->id) }}" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('payment.status', $order->id) }}" 
                           class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Cek Status Pembayaran
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-refresh payment status every 30 seconds
setInterval(function() {
    fetch('{{ route("payment.status", $order->id) }}')
        .then(response => response.text())
        .then(data => {
            // Check if payment is completed
            if (data.includes('verified') || data.includes('Lunas')) {
                window.location.reload();
            }
        })
        .catch(error => console.log('Error checking payment status:', error));
}, 30000);
</script>
@endsection

