@extends('layouts.admin_master')

@section('title', 'Kelola Pesanan - Admin Panel')

@section('content')
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-clipboard-check"></i> Kelola Pesanan
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
            <i class="bi bi-info-circle"></i> Belum ada pesanan yang masuk.
        </div>
    @else
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Layanan</th>
                                <th>Plat Nomor</th>
                                <th>Tanggal Booking</th>
                                <th>Status Pesanan</th>
                                <th>Status Proses</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td><strong class="text-primary">#{{ $order->id }}</strong></td>
                                <td>{{ $order->service_type }}</td>
                                <td>{{ $order->license_plate }}</td>
                                <td>{{ \Carbon\Carbon::parse($order->booking_date)->format('d M Y H:i') }}</td>
                                <td>
                                    @if($order->payment_status == 'verified')
                                        <span class="badge bg-success">Terkonfirmasi</span>
                                    @elseif($order->payment_status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending Pembayaran</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->status == 'Menunggu')
                                        <span class="badge bg-warning text-dark">{{ $order->status }}</span>
                                    @elseif($order->status == 'Proses')
                                        <span class="badge bg-info">{{ $order->status }}</span>
                                    @elseif($order->status == 'Selesai')
                                        <span class="badge bg-success">{{ $order->status }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $order->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->final_price)
                                        <span class="text-primary"><strong>Rp {{ number_format($order->final_price, 0, ',', '.') }}</strong></span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($order->payment_status == 'verified')
                                            <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 120px;">
                                                    <option value="Menunggu" {{ $order->status == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                                                    <option value="Proses" {{ $order->status == 'Proses' ? 'selected' : '' }}>Proses</option>
                                                    <option value="Selesai" {{ $order->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                                </select>
                                            </form>
                                        @else
                                            <span class="text-muted">Menunggu Verifikasi</span>
                                        @endif
                                        
                                        @if(in_array($order->status, ['Terkonfirmasi', 'Selesai', 'Refund', 'Batal']))
                                            <button type="button" class="btn btn-danger btn-sm ms-2" 
                                                    onclick="deleteOrder({{ $order->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
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
