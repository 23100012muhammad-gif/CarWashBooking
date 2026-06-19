@extends('layouts.admin_master')

@section('title', 'Tambah Diskon')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Diskon</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.discounts.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama Diskon</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Layanan</label>
            <select name="service_id" class="form-select" required>
                <option value="" disabled selected>Pilih layanan</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                        {{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Kode</label>
            <input type="text" name="code" class="form-control" value="{{ old('code') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Persentase (%)</label>
            <input type="number" name="percent" class="form-control" value="{{ old('percent') }}" min="1" max="100" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <input type="text" name="description" class="form-control" value="{{ old('description') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Tanggal Berakhir</label>
            <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}" required>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection


