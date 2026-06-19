@extends('layouts.carwash')

@section('title', 'Notifikasi - CarWash Connect')

@section('content')
<div class="container">
    <h2 class="mb-4">
        <i class="bi bi-bell"></i> Notifikasi
    </h2>
    
    @if($notifications->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Belum ada notifikasi.
        </div>
    @else
        <div class="row">
            <div class="col-md-8">
                @foreach($notifications as $notification)
                <div class="card mb-3 {{ $notification->is_read ? '' : 'border-primary' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="card-title {{ $notification->is_read ? '' : 'fw-bold' }}">
                                    {{ $notification->title }}
                                    @if(!$notification->is_read)
                                        <span class="badge bg-primary ms-2">Baru</span>
                                    @endif
                                </h6>
                                <p class="card-text">{{ $notification->message }}</p>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="btn-group">
                                @if(!$notification->is_read)
                                    <button class="btn btn-sm btn-outline-primary" onclick="markAsRead({{ $notification->id }})">
                                        Tandai Dibaca
                                    </button>
                                @endif
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteNotification({{ $notification->id }})">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
async function markAsRead(notificationId) {
    try {
        await fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        location.reload();
    } catch (error) {
        console.error('Error:', error);
    }
}

async function deleteNotification(notificationId) {
    if (!confirm('Hapus notifikasi ini?')) return;
    
    try {
        await fetch(`/notifications/${notificationId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        location.reload();
    } catch (error) {
        console.error('Error:', error);
    }
}
</script>
@endsection