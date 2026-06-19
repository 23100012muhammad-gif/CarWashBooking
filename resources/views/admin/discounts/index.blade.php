@extends('layouts.admin_master')

@section('title', 'Admin - Diskon')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Manajemen Diskon</h2>
        <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary">Tambah Diskon</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Layanan</th>
                    <th>Kode</th>
                    <th>Persentase</th>
                    <th>Deskripsi</th>
                    <th>Berlaku Sampai</th>
                    <th>Status</th>
                    <th>Harga Akhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($discounts as $disc)
                <tr>
                    <td>{{ $disc->name }}</td>
                    <td>
                        @if($disc->service)
                            <span class="badge bg-primary">{{ $disc->service->name }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $disc->code }}</td>
                    <td>{{ $disc->percent }}%</td>
                    <td>{{ $disc->description }}</td>
                    <td>{{ $disc->expires_at->format('d M Y H:i') }}</td>
                    <td>
                        @if($disc->active && $disc->expires_at->isFuture())
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Tidak Aktif</span>
                        @endif
                    </td>
                    <td>
                        @php($price = optional($disc->service)->price ?? 0)
                        @php($final = max(0, $price - floor($price * ($disc->percent / 100))))
                        Rp {{ number_format($final, 0, ',', '.') }}
                    </td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.discounts.edit', $disc) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.discounts.destroy', $disc) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus diskon ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Belum ada diskon</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


