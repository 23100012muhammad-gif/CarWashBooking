@extends('layouts.carwash')

@section('title', 'Transfer Bank - Car Wash Booking')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-bank"></i> Instruksi Transfer Bank
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

                    <!-- Bank Account Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-credit-card-2-front"></i> Rekening Tujuan
                            </h5>
                        </div>
                        
                        @foreach($bankAccounts as $bank)
                        <div class="col-md-4 mb-3">
                            <div class="card border-primary">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">{{ $bank['bank'] }}</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1"><strong>No. Rekening:</strong></p>
                                    <p class="h6 text-primary">{{ $bank['account_number'] }}</p>
                                    
                                    <p class="mb-1"><strong>Atas Nama:</strong></p>
                                    <p class="mb-1">{{ $bank['account_name'] }}</p>
                                    
                                    <p class="mb-1"><strong>Cabang:</strong></p>
                                    <p class="mb-0">{{ $bank['branch'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Transfer Instructions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-list-ol"></i> Langkah-langkah Transfer
                            </h5>
                            <div class="card">
                                <div class="card-body">
                                    <ol class="mb-0">
                                        <li>Transfer sejumlah <strong>Rp {{ number_format($order->final_price, 0, ',', '.') }}</strong> ke salah satu rekening di atas</li>
                                        <li>Gunakan nomor pesanan <strong>#{{ $order->id }}</strong> sebagai keterangan transfer</li>
                                        <li>Screenshot atau foto bukti transfer</li>
                                        <li>Upload bukti transfer di form di bawah ini</li>
                                        <li>Tunggu konfirmasi dari tim kami (maksimal 1x24 jam)</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Payment Proof -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-cloud-upload"></i> Upload Bukti Transfer
                            </h5>
                            
                            <form action="{{ route('payment.upload-proof', $order->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="payment_proof" class="form-label">
                                                <strong>Bukti Transfer</strong> <span class="text-danger">*</span>
                                            </label>
                                            <input type="file" class="form-control @error('payment_proof') is-invalid @enderror" 
                                                   id="payment_proof" name="payment_proof" 
                                                   accept="image/*,.pdf" required>
                                            <div class="form-text">
                                                Format yang diterima: JPG, PNG, PDF (Maksimal 2MB)
                                            </div>
                                            @error('payment_proof')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="payment_notes" class="form-label">
                                                <strong>Catatan Tambahan</strong>
                                            </label>
                                            <textarea class="form-control @error('payment_notes') is-invalid @enderror" 
                                                      id="payment_notes" name="payment_notes" rows="3" 
                                                      placeholder="Contoh: Transfer dari rekening sendiri, jam transfer, dll."></textarea>
                                            @error('payment_notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <a href="{{ route('payment.confirmation', $order->id) }}" 
                                               class="btn btn-outline-secondary me-md-2">
                                                <i class="bi bi-arrow-left"></i> Kembali
                                            </a>
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-cloud-upload"></i> Upload Bukti Transfer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

