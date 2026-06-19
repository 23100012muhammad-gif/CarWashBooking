@extends('layouts.admin_master')

@section('title', 'Kelola Jadwal & Slot')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-calendar-check"></i> Kelola Jadwal & Slot</h2>
            </div>

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="operational-tab" data-bs-toggle="tab" data-bs-target="#operational" type="button" role="tab">
                        <i class="bi bi-clock"></i> Hari Operasional
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="slots-tab" data-bs-toggle="tab" data-bs-target="#slots" type="button" role="tab">
                        <i class="bi bi-calendar-plus"></i> Kelola Slot
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="adminTabsContent">
                <!-- Hari Operasional Tab -->
                <div class="tab-pane fade show active" id="operational" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Pengaturan Hari Operasional</h5>
                        </div>
                        <div class="card-body">
                            <form id="operational-form" method="POST" action="{{ route('admin.operational-days.update') }}">
                                @csrf
                                <div class="row">
                                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0">{{ $day }}</h6>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="active_{{ $day }}" 
                                                               name="days[{{ $day }}][status_operasional]" 
                                                               value="1">
                                                        <label class="form-check-label" for="active_{{ $day }}">Aktif</label>
                                                    </div>
                                                </div>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <label class="form-label small">Buka</label>
                                                        <input type="time" class="form-control form-control-sm" 
                                                               name="days[{{ $day }}][jam_buka]" value="08:00">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small">Tutup</label>
                                                        <input type="time" class="form-control form-control-sm" 
                                                               name="days[{{ $day }}][jam_tutup]" value="18:00">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Simpan Jadwal
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="generateSlots()">
                                        <i class="bi bi-magic"></i> Generate Slot Otomatis
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Kelola Slot Tab -->
                <div class="tab-pane fade" id="slots" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Daftar Slot Booking</h5>
                            <div class="d-flex gap-2">
                                <input type="date" id="filter-date" class="form-control" value="{{ date('Y-m-d') }}">
                                <button class="btn btn-primary" onclick="showAddSlotModal()">
                                    <i class="bi bi-plus"></i> Tambah Slot
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="slots-container">
                                <!-- Slots akan dimuat di sini -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Slot -->
<div class="modal fade" id="addSlotModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Slot Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="add-slot-form" method="POST" action="{{ route('admin.slots.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kapasitas</label>
                        <input type="number" name="kapasitas" class="form-control" value="4" min="1" max="10" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Slot -->
<div class="modal fade" id="editSlotModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Slot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="edit-slot-form">
                @csrf
                @method('PATCH')
                <input type="hidden" id="edit-slot-id">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" id="edit-jam-mulai" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" id="edit-jam-selesai" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kapasitas</label>
                        <input type="number" id="edit-kapasitas" class="form-control" min="1" max="10" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Generate Slot -->
<div class="modal fade" id="generateSlotModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Slot Otomatis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="generate-slot-form" method="POST" action="{{ route('admin.operational-days.generate-slots') }}">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Durasi per Slot</label>
                        <select name="slot_duration" class="form-select" required>
                            <option value="30">30 menit</option>
                            <option value="45">45 menit</option>
                            <option value="60" selected>60 menit</option>
                            <option value="90">90 menit</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Slot akan dibuat berdasarkan hari operasional yang aktif.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadSlots();
    
    // Filter date change
    document.getElementById('filter-date').addEventListener('change', loadSlots);
    
    // Operational form submit - let it submit normally
    document.getElementById('operational-form').addEventListener('submit', function(e) {
        // Allow normal form submission
        return true;
    });
    
    // Add slot form submit - let it submit normally
    document.getElementById('add-slot-form').addEventListener('submit', function(e) {
        // Allow normal form submission
        return true;
    });
    
    // Generate slot form submit - let it submit normally
    document.getElementById('generate-slot-form').addEventListener('submit', function(e) {
        // Allow normal form submission
        return true;
    });
    
    // Edit slot form submit
    document.getElementById('edit-slot-form').addEventListener('submit', function(e) {
        e.preventDefault();
        updateSlot();
    });
});

async function loadSlots() {
    const date = document.getElementById('filter-date').value;
    const month = date.substring(0, 7); // YYYY-MM
    
    try {
        const response = await fetch(`/admin/jadwal-slot?month=${month}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.status === 'success') {
            const daySlots = data.data.filter(slot => {
                const slotDate = slot.tanggal.split(' ')[0]; // Remove time part if exists
                return slotDate === date;
            });
            
            if (daySlots.length > 0) {
                const slotsHtml = daySlots.map(slot => {
                    const jamMulai = slot.jam_mulai.substring(0, 5); // HH:MM
                    const jamSelesai = slot.jam_selesai.substring(0, 5); // HH:MM
                    const canDelete = (slot.terisi || 0) === 0;
                    return `
                        <div class="col-md-4 mb-2">
                            <div class="card">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-bold">${jamMulai} - ${jamSelesai}</div>
                                            <small>Kapasitas: ${slot.kapasitas}, Terisi: ${slot.terisi || 0}</small><br>
                                            <span class="badge bg-${slot.status === 'tersedia' ? 'success' : 'secondary'}">${slot.status}</span>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editSlot(${slot.id}, '${jamMulai}', '${jamSelesai}', ${slot.kapasitas})" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            ${canDelete ? `
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSlot(${slot.id})" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            ` : `
                                                <button class="btn btn-sm btn-outline-secondary" disabled title="Tidak dapat dihapus (ada booking)">
                                                    <i class="bi bi-lock"></i>
                                                </button>
                                            `}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
                
                document.getElementById('slots-container').innerHTML = `
                    <div class="row">${slotsHtml}</div>
                `;
            } else {
                document.getElementById('slots-container').innerHTML = `
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        Belum ada slot untuk tanggal ${date}. Silakan generate slot atau tambah manual.
                    </div>
                `;
            }
        } else {
            throw new Error(data.message || 'Unknown error');
        }
    } catch (error) {
        console.error('Error loading slots:', error);
        document.getElementById('slots-container').innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> 
                Error: ${error.message}
            </div>
        `;
    }
}

function showAddSlotModal() {
    const modal = new bootstrap.Modal(document.getElementById('addSlotModal'));
    modal.show();
}

function generateSlots() {
    const modal = new bootstrap.Modal(document.getElementById('generateSlotModal'));
    modal.show();
}

function editSlot(id, jamMulai, jamSelesai, kapasitas) {
    document.getElementById('edit-slot-id').value = id;
    document.getElementById('edit-jam-mulai').value = jamMulai;
    document.getElementById('edit-jam-selesai').value = jamSelesai;
    document.getElementById('edit-kapasitas').value = kapasitas;
    
    const modal = new bootstrap.Modal(document.getElementById('editSlotModal'));
    modal.show();
}

async function updateSlot() {
    const id = document.getElementById('edit-slot-id').value;
    const jamMulai = document.getElementById('edit-jam-mulai').value;
    const jamSelesai = document.getElementById('edit-jam-selesai').value;
    const kapasitas = document.getElementById('edit-kapasitas').value;
    
    try {
        const response = await fetch(`/admin/jadwal-slot/slot/${id}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                jam_mulai: jamMulai,
                jam_selesai: jamSelesai,
                kapasitas: parseInt(kapasitas)
            })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showAlert('success', data.message);
            bootstrap.Modal.getInstance(document.getElementById('editSlotModal')).hide();
            loadSlots();
        } else {
            showAlert('error', data.message);
        }
    } catch (error) {
        showAlert('error', 'Terjadi kesalahan saat mengupdate slot');
    }
}

async function deleteSlot(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus slot ini?')) {
        return;
    }
    
    try {
        const response = await fetch(`/admin/jadwal-slot/slot/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showAlert('success', data.message);
            loadSlots();
        } else {
            showAlert('error', data.message);
        }
    } catch (error) {
        showAlert('error', 'Terjadi kesalahan saat menghapus slot');
    }
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.row'));
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
@endpush
@endsection