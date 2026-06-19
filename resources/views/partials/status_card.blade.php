<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">
            <i class="bi bi-info-circle"></i> Status Pesanan
        </h5>
        <div class="row mt-3">
            <div class="col-md-6">
                <p class="mb-2"><strong>Nomor Antrean:</strong></p>
                <h2 class="text-primary">{{ $order->queue_number }}</h2>
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Status:</strong></p>
                @if($order->status == 'Menunggu')
                    <span class="badge bg-warning text-dark fs-6">{{ $order->status }}</span>
                @elseif($order->status == 'Proses')
                    <span class="badge bg-info fs-6">{{ $order->status }}</span>
                @else
                    <span class="badge bg-success fs-6">{{ $order->status }}</span>
                @endif
            </div>
        </div>
        <hr>
        <div class="mt-3">
            <p class="mb-1"><strong>Jenis Layanan:</strong> {{ $order->service_type }}</p>
            <p class="mb-1"><strong>Plat Nomor:</strong> {{ $order->license_plate }}</p>
            <p class="mb-1"><strong>Tanggal Booking:</strong> {{ $order->booking_date->format('d M Y H:i') }}</p>
            @if($order->final_price)
                <div class="mt-2 p-2 bg-light rounded">
                    <p class="mb-1"><strong>Harga:</strong></p>
                    @if($order->discount_percent > 0)
                        <p class="mb-1">
                            <small class="text-muted">Normal: <span class="text-decoration-line-through">Rp {{ number_format($order->original_price, 0, ',', '.') }}</span></small><br>
                            <strong class="text-success">Setelah diskon: Rp {{ number_format($order->final_price, 0, ',', '.') }}</strong><br>
                            <small class="text-success">Diskon: {{ $order->discount_percent }}% ({{ $order->discount_name }})</small>
                        </p>
                    @else
                        <p class="mb-1"><strong>Rp {{ number_format($order->final_price, 0, ',', '.') }}</strong></p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
