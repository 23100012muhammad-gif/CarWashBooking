@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Upload Bukti Pembayaran</h1>

        <div class="bg-white rounded-lg shadow p-6">
            <!-- Order Summary -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-4">Detail Pesanan</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Nomor Pesanan:</p>
                        <p class="font-medium">#{{ $order->id }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Tanggal:</p>
                        <p class="font-medium">{{ $order->bookingSlot->tanggal->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Waktu:</p>
                        <p class="font-medium">
                            {{ $order->bookingSlot->jam_mulai }} - {{ $order->bookingSlot->jam_selesai }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Layanan:</p>
                        <p class="font-medium">{{ $order->service->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Total Pembayaran:</p>
                        <p class="font-medium">Rp {{ number_format($order->final_price, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Metode Pembayaran:</p>
                        <p class="font-medium">{{ $order->payment_method }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Instructions -->
            <div class="mb-6 p-4 bg-blue-50 rounded">
                <h3 class="font-medium mb-2">Instruksi Pembayaran</h3>
                <ol class="list-decimal list-inside space-y-2 text-sm">
                    <li>Transfer ke rekening yang tertera sesuai nominal</li>
                    <li>Simpan bukti transfer</li>
                    <li>Upload bukti transfer menggunakan form di bawah</li>
                    <li>Tunggu konfirmasi dari admin (maksimal 1x24 jam)</li>
                </ol>
            </div>

            <!-- Upload Form -->
            {{-- // updated by Copilot for upload/payment UI and progress handling --}}
            <form id="upload-form" class="space-y-4" data-upload-url="{{ route('payment.upload-proof', $order) }}">
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Upload Bukti Pembayaran
                    </label>
                    <input type="file" name="bukti_pembayaran" accept="image/*"
                           class="w-full border rounded p-2">
                    <p class="text-sm text-gray-500 mt-1">
                        Format yang diterima: JPG, PNG. Maksimal 2MB
                    </p>
                </div>

                <button type="submit" 
                        class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                    Upload Bukti Pembayaran
                </button>
            </form>
            <!-- Progress / Feedback -->
            <div id="upload-feedback" class="mt-4 hidden">
                <div class="text-sm mb-2" id="upload-message"></div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div id="upload-progress" class="bg-green-500 h-3 rounded-full" style="width:0%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- booking.js will handle upload logic and UI feedback -->
<script src="{{ asset('js/booking.js') }}"></script>
@endpush