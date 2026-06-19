@extends('layouts.admin_master')

@section('title', 'Tambah Layanan')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Layanan</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.services.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama Layanan</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Harga (Rp)</label>
            <input type="number" name="price" class="form-control" value="{{ old('price') }}" min="0" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Durasi (menit) - opsional</label>
            <input type="number" name="duration" class="form-control" value="{{ old('duration') }}" min="0">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection


