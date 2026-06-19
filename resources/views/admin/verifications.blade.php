@extends('layouts.admin_master')

@section('title', 'Verifikasi Pembayaran & Refund - Admin Panel')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-shield-check"></i> Verifikasi Pembayaran & Refund
    </h2>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-4" id="verificationTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab">
                <i class="bi bi-credit-card-2-back"></i> Verifikasi Pembayaran 
                @if($pendingOrders->count() > 0)
                    <span class="badge bg-danger">{{ $pendingOrders->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="refund-tab" data-bs-toggle="tab" data-bs-target="#refund" type="button" role="tab">
                <i class="bi bi-arrow-return-left"></i> Pengajuan Refund
                @if($refundRequests->count() > 0)
                    <span class="badge bg-warning">{{ $refundRequests->count() }}</span>
                @endif
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="verificationTabsContent">
        <!-- Verifikasi Pembayaran -->
        <div class="tab-pane fade show active" id="payment" role="tabpanel">
            @if($pendingOrders->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Tidak ada pembayaran yang perlu diverifikasi.
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No. Pesanan</th>
                                        <th>Layanan</th>
                                        <th>Plat Nomor</th>
                                        <th>Total Bayar</th>
                                        <th>Bukti Bayar</th>
                                        <th>Tanggal Upload</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingOrders as $order)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">#{{ $order->id }}</strong>
                                            @if($order->refund_requested_at)
                                                <br><small class="badge bg-warning text-dark">Mengajukan Refund</small>
                                            @endif
                                        </td>
                                        <td>{{ $order->service_type }}</td>
                                        <td>{{ $order->license_plate }}</td>
                                        <td>
                                            @if($order->discount_percent > 0)
                                                <small class="text-muted text-decoration-line-through">Rp {{ number_format($order->original_price, 0, ',', '.') }}</small><br>
                                                <strong class="text-success">Rp {{ number_format($order->final_price, 0, ',', '.') }}</strong>
                                                <small class="text-success">({{ $order->discount_percent }}% OFF)</small>
                                            @else
                                                <strong>Rp {{ number_format($order->final_price, 0, ',', '.') }}</strong>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="showPaymentProof('{{ route('admin.payment-proof', basename($order->payment_proof)) }}')">
                                                <i class="bi bi-eye"></i> Lihat Bukti
                                            </button>
                                        </td>
                                        <td>{{ $order->updated_at->format('d M Y H:i') }}</td>
                                        <td>
                                            @if($order->refund_requested_at)
                                                <span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Lihat Tab Refund</span>
                                            @else
                                                <form action="{{ route('admin.payment.verify', $order->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Verifikasi pembayaran ini?')">
                                                        <i class="bi bi-check-lg"></i> Verifikasi
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.payment.verify', $order->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tolak pembayaran ini?')">
                                                        <i class="bi bi-x-lg"></i> Tolak
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Pengajuan Refund -->
        <div class="tab-pane fade" id="refund" role="tabpanel">
            @if($refundRequests->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Tidak ada pengajuan refund.
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No. Pesanan</th>
                                        <th>Layanan</th>
                                        <th>Total Bayar</th>
                                        <th>Alasan Refund</th>
                                        <th>Bukti Bayar</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($refundRequests as $order)
                                    <tr>
                                        <td><strong class="text-primary">#{{ $order->id }}</strong></td>
                                        <td>{{ $order->service_type }}</td>
                                        <td><strong>Rp {{ number_format($order->final_price, 0, ',', '.') }}</strong></td>
                                        <td>{{ $order->refund_reason }}</td>
                                        <td>
                                            @if($order->payment_proof)
                                                <button class="btn btn-sm btn-outline-primary" onclick="showPaymentProof('{{ route('admin.payment-proof', basename($order->payment_proof)) }}')">
                                                    <i class="bi bi-eye"></i> Lihat Bukti
                                                </button>
                                            @else
                                                <span class="text-muted">Tidak ada</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->refund_requested_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <form action="{{ route('admin.refund.process', $order->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Setujui refund ini?')">
                                                    <i class="bi bi-check-lg"></i> Setujui
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.refund.process', $order->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tolak refund ini?')">
                                                    <i class="bi bi-x-lg"></i> Tolak
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Bukti Pembayaran -->
<div class="modal fade" id="paymentProofModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="paymentProofImage" src="" class="img-fluid" style="max-height: 500px;">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showPaymentProof(imageUrl) {
    document.getElementById('paymentProofImage').src = imageUrl;
    new bootstrap.Modal(document.getElementById('paymentProofModal')).show();
}
</script>
@endpush

@endsection