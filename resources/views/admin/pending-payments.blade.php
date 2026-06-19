@extends('layouts.admin_master')

@section('title', 'Verifikasi Pembayaran - Admin Panel')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    <i class="bi bi-credit-card-check"></i> Verifikasi Pembayaran
                </h4>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($pendingOrders->isEmpty() && $refundRequests->isEmpty())
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> Tidak ada pembayaran yang menunggu verifikasi atau pengajuan refund.
        </div>
    @else
        @if(!$pendingOrders->isEmpty())
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-credit-card-check"></i> Daftar Pembayaran Tertunda
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Layanan</th>
                                <th>Tanggal Booking</th>
                                <th>Plat Kendaraan</th>
                                <th>Total</th>
                                <th>Metode</th>
                                <th>Bukti</th>
                                <th>Dikirim</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingOrders as $order)
                            <tr>
                                <td><strong>#{{ $order->id }}</strong></td>
                                <td>{{ $order->service_type }}</td>
                                <td>{{ \Carbon\Carbon::parse($order->booking_date)->format('d M Y, H:i') }}</td>
                                <td>{{ $order->license_plate }}</td>
                                <td class="text-primary"><strong>Rp {{ number_format($order->final_price, 0, ',', '.') }}</strong></td>
                                <td>
                                    @if($order->payment_method === 'bank_transfer')
                                        <span class="badge bg-primary">
                                            <i class="bi bi-bank"></i> Bank
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="bi bi-phone"></i> E-Wallet
                                        </span>
                                    @endif
                                </td>
                                <td>
                                @if($order->hasPaymentProof())
    <a href="#" data-bs-toggle="modal" data-bs-target="#paymentProofModal{{ $order->id }}" 
       class="btn btn-outline-primary btn-sm">
       <i class="bi bi-eye"></i> Lihat
    </a>

    <div class="modal fade" id="paymentProofModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bukti Pembayaran #{{ $order->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ Storage::url($order->payment_proof) }}" alt="Bukti Pembayaran" class="img-fluid" />
                </div>
            </div>
        </div>
    </div>
@else
    <span class="text-muted">-</span>
@endif

                                </td>
                                <td>
                                    <small>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-success btn-sm" 
                                                data-bs-toggle="modal" data-bs-target="#verifyModal{{ $order->id }}">
                                            <i class="bi bi-check-circle"></i> Verifikasi
                                        </button>

                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        
        @if(!$refundRequests->isEmpty())
        <div class="card shadow mt-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="bi bi-arrow-return-left"></i> Pengajuan Refund
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Layanan</th>
                                <th>Plat Kendaraan</th>
                                <th>Total</th>
                                <th>Alasan Refund</th>
                                <th>Diajukan</th>
                                <th>Bukti Transfer</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($refundRequests as $order)
                            <tr>
                                <td><strong>#{{ $order->id }}</strong></td>
                                <td>{{ $order->service_type }}</td>
                                <td>{{ $order->license_plate }}</td>
                                <td class="text-primary"><strong>Rp {{ number_format($order->final_price, 0, ',', '.') }}</strong></td>
                                <td>
                                    <small>{{ strlen($order->refund_reason) > 50 ? substr($order->refund_reason, 0, 50) . '...' : $order->refund_reason }}</small>
                                </td>
                                <td>
                                    <small>{{ \Carbon\Carbon::parse($order->refund_requested_at)->format('d M Y, H:i') }}</small>
                                </td>
                                <td>
                                @if($order->hasPaymentProof())
    <a href="#" data-bs-toggle="modal" data-bs-target="#refundProofModal{{ $order->id }}" 
       class="btn btn-outline-primary btn-sm">
       <i class="bi bi-eye"></i> Lihat
    </a>

    <div class="modal fade" id="refundProofModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bukti Refund #{{ $order->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ Storage::url($order->payment_proof) }}" alt="Bukti Refund" class="img-fluid" />
                </div>
            </div>
        </div>
    </div>
@else
    <span class="text-muted">-</span>
@endif

                                </td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" 
                                            data-bs-toggle="modal" data-bs-target="#refundModal{{ $order->id }}">
                                        <i class="bi bi-check-circle"></i> Proses
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    @endif
</div>

<!-- Verification Modals -->
@foreach($pendingOrders as $order)
<div class="modal fade" id="verifyModal{{ $order->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-credit-card-check"></i> Verifikasi Pembayaran #{{ $order->id }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.payment.verify', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Layanan:</strong><br>
                            {{ $order->service_type }}
                        </div>
                        <div class="col-md-6">
                            <strong>Total:</strong><br>
                            <span class="text-primary">Rp {{ number_format($order->final_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Plat Kendaraan:</strong><br>
                            {{ $order->license_plate }}
                        </div>
                        <div class="col-md-6">
                            <strong>Tanggal Booking:</strong><br>
                            {{ \Carbon\Carbon::parse($order->booking_date)->format('d M Y, H:i') }}
                        </div>
                    </div>

                    @if($order->payment_reference)
                    <div class="mb-3">
                        <strong>Referensi Pembayaran:</strong><br>
                        {{ $order->payment_reference }}
                    </div>
                    @endif

                    @if($order->payment_notes)
                    <div class="mb-3">
                        <strong>Catatan Customer:</strong><br>
                        <span class="text-muted">{{ $order->payment_notes }}</span>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label for="notes_{{ $order->id }}" class="form-label">Catatan Verifikasi</label>
                        <textarea class="form-control" id="notes_{{ $order->id }}" 
                                  name="notes" rows="3" 
                                  placeholder="Catatan untuk customer (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="action" value="approve" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Setujui Pembayaran
                    </button>
                    <button type="submit" name="action" value="reject" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Tolak Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Refund Processing Modals -->
@foreach($refundRequests as $order)
<div class="modal fade" id="refundModal{{ $order->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-return-left"></i> Proses Refund #{{ $order->id }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.refund.process', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Layanan:</strong><br>
                            {{ $order->service_type }}
                        </div>
                        <div class="col-md-6">
                            <strong>Total:</strong><br>
                            <span class="text-primary">Rp {{ number_format($order->final_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Plat Kendaraan:</strong><br>
                            {{ $order->license_plate }}
                        </div>
                        <div class="col-md-6">
                            <strong>Diajukan:</strong><br>
                            {{ \Carbon\Carbon::parse($order->refund_requested_at)->format('d M Y, H:i') }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Alasan Refund:</strong><br>
                        <div class="border p-2 bg-light">
                            {{ $order->refund_reason }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="refund_notes_{{ $order->id }}" class="form-label">Catatan Admin</label>
                        <textarea class="form-control" id="refund_notes_{{ $order->id }}" 
                                  name="refund_notes" rows="3" 
                                  placeholder="Catatan untuk customer (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="action" value="approve" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Setujui Refund
                    </button>
                    <button type="submit" name="action" value="reject" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Tolak Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
function deleteOrder(orderId) {
    if (confirm('Apakah Anda yakin ingin menghapus pesanan ini? Tindakan ini tidak dapat dibatalkan.')) {
        // Create a form to submit DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.orders.delete", ":id") }}'.replace(':id', orderId);
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add method spoofing
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection

