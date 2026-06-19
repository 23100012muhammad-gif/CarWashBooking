@extends('layouts.carwash')

@section('title', 'Riwayat Booking - Car Wash Booking')

@section('content')
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-journal-text"></i> Riwayat Booking
    </h2>
    
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
    
    @if($orders->isEmpty())
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> Belum ada riwayat booking.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Jenis Layanan</th>
                        <th>Plat Nomor</th>
                        <th>Tanggal Booking</th>
                        <th>Status Pembayaran</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td><strong>#{{ $order->id }}</strong></td>
                        <td>{{ $order->service_type }}</td>
                        <td>{{ $order->license_plate }}</td>
                        <td>{{ \Carbon\Carbon::parse($order->booking_date)->format('d M Y H:i') }}</td>
                        <td>
                            <span class="badge bg-{{ $order->getPaymentStatusClass() }}">
                                {{ $order->getPaymentStatusLabel() }}
                            </span>
                        </td>
                        <td>Rp {{ number_format($order->final_price, 0, ',', '.') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                        data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                                @if($order->isPaymentVerified())
                                    <button type="button" class="btn btn-outline-success btn-sm" 
                                            onclick="printReceipt({{ $order->id }})">
                                        <i class="bi bi-printer"></i> Cetak
                                    </button>
                                @endif
                                @if($order->canRequestRefund())
                                    <button type="button" class="btn btn-outline-warning btn-sm" 
                                            data-bs-toggle="modal" data-bs-target="#refundModal{{ $order->id }}">
                                        <i class="bi bi-arrow-return-left"></i> Ajukan Refund
                                    </button>
                                @endif
                                @if($order->canDeleteFromHistory())
                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                            onclick="deleteOrder({{ $order->id }})" title="Hapus dari riwayat">
                                        <i class="bi bi-trash"></i> Hapus Riwayat
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Order Detail Modals -->
@foreach($orders as $order)
<div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-receipt"></i> Detail Pesanan #{{ $order->id }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Informasi Pesanan</h6>
                        <table class="table table-borderless table-sm">
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
                                <td><strong>Plat Kendaraan:</strong></td>
                                <td>{{ $order->license_plate }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status Pesanan:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $order->status === 'Selesai' ? 'success' : ($order->status === 'Terkonfirmasi' ? 'primary' : 'warning') }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Informasi Pembayaran</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td><strong>Harga Awal:</strong></td>
                                <td>Rp {{ number_format($order->original_price, 0, ',', '.') }}</td>
                            </tr>
                            @if($order->discount_percent > 0)
                            <tr>
                                <td><strong>Diskon:</strong></td>
                                <td class="text-success">{{ $order->discount_percent }}% ({{ $order->discount_name }})</td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Total:</strong></td>
                                <td class="h6 text-primary">Rp {{ number_format($order->final_price, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status Pembayaran:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $order->getPaymentStatusClass() }}">
                                        {{ $order->getPaymentStatusLabel() }}
                                    </span>
                                </td>
                            </tr>
                            @if($order->payment_method)
                            <tr>
                                <td><strong>Metode Pembayaran:</strong></td>
                                <td>
                                    @if($order->payment_method === 'bank_transfer')
                                        <i class="bi bi-bank text-primary"></i> Transfer Bank
                                    @else
                                        <i class="bi bi-phone text-success"></i> E-Wallet
                                    @endif
                                </td>
                            </tr>
                            @endif
                            @if($order->payment_verified_at)
                            <tr>
                                <td><strong>Waktu Verifikasi:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($order->payment_verified_at)->format('d M Y, H:i') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                @if($order->hasPaymentProof())
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-primary">Bukti Pembayaran</h6>
                        <button class="btn btn-outline-primary btn-sm" 
                                onclick="showPaymentProof('{{ route('payment-proof', basename($order->payment_proof)) }}')">
                            <i class="bi bi-eye"></i> Lihat Bukti Transfer
                        </button>
                    </div>
                </div>
                @endif
                
                @if($order->payment_notes)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-primary">Catatan</h6>
                        <p class="mb-0">{{ $order->payment_notes }}</p>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                @if($order->isPaymentVerified())
                    <button type="button" class="btn btn-success" onclick="printReceipt({{ $order->id }})">
                        <i class="bi bi-printer"></i> Cetak Bukti
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Refund Request Modals -->
@foreach($orders as $order)
@if($order->canRequestRefund())
<div class="modal fade" id="refundModal{{ $order->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-return-left"></i> Ajukan Refund - Pesanan #{{ $order->id }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('booking.refund', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <p><strong>Layanan:</strong> {{ $order->service_type }}</p>
                        <p><strong>Total:</strong> Rp {{ number_format($order->final_price, 0, ',', '.') }}</p>
                        <p><strong>Plat Kendaraan:</strong> {{ $order->license_plate }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="refund_reason_{{ $order->id }}" class="form-label">Alasan Refund <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="refund_reason_{{ $order->id }}" 
                                  name="refund_reason" rows="4" required
                                  placeholder="Jelaskan alasan Anda mengajukan refund..."></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        Bukti transfer yang sudah Anda upload akan digunakan untuk proses refund.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-arrow-return-left"></i> Ajukan Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

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

<script>
function showPaymentProof(imageUrl) {
    document.getElementById('paymentProofImage').src = imageUrl;
    new bootstrap.Modal(document.getElementById('paymentProofModal')).show();
}

function printReceipt(orderId) {
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    const order = @json($orders->keyBy('id'));
    const orderData = order[orderId];
    
    if (!orderData) return;
    
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Bukti Pembayaran - Pesanan #${orderData.id}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
                .content { margin-bottom: 20px; }
                .row { display: flex; justify-content: space-between; margin-bottom: 5px; }
                .label { font-weight: bold; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>CarWash Connect</h2>
                <h3>Bukti Pembayaran</h3>
            </div>
            
            <div class="content">
                <div class="row">
                    <span class="label">Nomor Pesanan:</span>
                    <span>#${orderData.id}</span>
                </div>
                <div class="row">
                    <span class="label">Layanan:</span>
                    <span>${orderData.service_type}</span>
                </div>
                <div class="row">
                    <span class="label">Tanggal:</span>
                    <span>${new Date(orderData.booking_date).toLocaleDateString('id-ID')}</span>
                </div>
                <div class="row">
                    <span class="label">Waktu:</span>
                    <span>${new Date(orderData.booking_date).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}</span>
                </div>
                <div class="row">
                    <span class="label">Nomor Antrian:</span>
                    <span>${orderData.queue_number}</span>
                </div>
                <div class="row">
                    <span class="label">Plat Kendaraan:</span>
                    <span>${orderData.license_plate}</span>
                </div>
                <div class="row">
                    <span class="label">Harga:</span>
                    <span>Rp ${orderData.final_price.toLocaleString('id-ID')}</span>
                </div>
                <div class="row">
                    <span class="label">Status Pembayaran:</span>
                    <span>Lunas</span>
                </div>
                <div class="row">
                    <span class="label">Waktu Verifikasi:</span>
                    <span>${orderData.payment_verified_at ? new Date(orderData.payment_verified_at).toLocaleString('id-ID') : '-'}</span>
                </div>
            </div>
            
            <div class="footer">
                <p>Terima kasih telah menggunakan layanan CarWash Connect</p>
                <p>Dicetak pada: ${new Date().toLocaleString('id-ID')}</p>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}

function deleteOrder(orderId) {
    if (confirm('Apakah Anda yakin ingin menghapus pesanan ini dari riwayat? Pesanan akan disembunyikan dari tampilan Anda.')) {
        // Create a form to submit DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("booking.delete", ":id") }}'.replace(':id', orderId);
        
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
