@extends('layouts.admin_master')

@section('title', 'Manajemen Slot & Jadwal')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-calendar-check"></i> Manajemen Slot & Jadwal
        </h2>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-primary" onclick="showOperationalDaysModal()">
                <i class="bi bi-clock"></i> Atur Hari Operasional
            </button>
            <button onclick="showAddSlotModal()" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Slot
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Calendar Section -->
    <div class="card shadow">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col-auto">
                    <input type="month" id="month-picker" class="form-control" 
                           value="{{ date('Y-m') }}">
                </div>
                <div class="col d-flex justify-content-end gap-3">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-1">&nbsp;</span>
                        <small>Tersedia</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-danger me-1">&nbsp;</span>
                        <small>Penuh</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-secondary me-1">&nbsp;</span>
                        <small>Non-aktif</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr class="text-center table-light">
                            <th style="width: 14.28%">Min</th>
                            <th style="width: 14.28%">Sen</th>
                            <th style="width: 14.28%">Sel</th>
                            <th style="width: 14.28%">Rab</th>
                            <th style="width: 14.28%">Kam</th>
                            <th style="width: 14.28%">Jum</th>
                            <th style="width: 14.28%">Sab</th>
                        </tr>
                    </thead>
                    <tbody id="slot-calendar">
                        <!-- Calendar akan diisi oleh JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Help Text -->
    <div class="alert alert-info mt-4">
        <i class="bi bi-info-circle me-2"></i>
        <span>Klik tombol <i class="bi bi-plus-lg"></i> di setiap tanggal untuk menambah slot, atau gunakan tombol "Atur Hari Operasional" untuk mengatur jadwal mingguan.</span>
    </div>
    
    <!-- Operational Days Modal -->
    <div class="modal fade" id="operational-days-modal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-clock me-2"></i>Pengaturan Hari Operasional</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="operational-days-form">
                    <div class="modal-body">
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="card-title mb-0">{{ $day }}</h6>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" 
                                                   name="days[{{ $day }}][status_operasional]" 
                                                   id="status_{{ $day }}" checked>
                                            <label class="form-check-label" for="status_{{ $day }}">
                                                Aktif
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label class="form-label">Jam Buka</label>
                                            <input type="time" class="form-control" 
                                                   name="days[{{ $day }}][jam_buka]" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label">Jam Tutup</label>
                                            <input type="time" class="form-control"
                                                   name="days[{{ $day }}][jam_tutup]" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Slot Modal -->
    <div class="modal fade" id="add-slot-modal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Slot Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="add-slot-form">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="form-control" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kapasitas</label>
                            <div class="input-group">
                                <input type="number" name="kapasitas" class="form-control" 
                                       min="1" value="4" required>
                                <span class="input-group-text">kendaraan</span>
                            </div>
                            <div class="form-text">Jumlah kendaraan maksimal yang dapat dilayani pada slot ini</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Tambah Slot
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Calendar styles */
.slot-cell { 
    height: 120px;
    overflow-y: auto;
    padding: 0.5rem;
    position: relative;
}

.slot-item {
    font-size: 0.75rem;
    padding: 0.5rem;
    margin-bottom: 0.25rem;
    border-radius: 0.25rem;
    background: white;
}

.slot-item.slot-available {
    background-color: rgba(var(--bs-success-rgb), 0.1);
    border: 1px solid var(--bs-success);
    color: var(--bs-success);
}

.slot-item.slot-full {
    background-color: rgba(var(--bs-danger-rgb), 0.1);
    border: 1px solid var(--bs-danger);
    color: var(--bs-danger);
}

.slot-item.slot-disabled {
    background-color: rgba(var(--bs-secondary-rgb), 0.1);
    border: 1px solid var(--bs-secondary);
    color: var(--bs-secondary);
}

.calendar-today {
    background-color: rgba(var(--bs-primary-rgb), 0.05);
}

.calendar-other-month {
    background-color: var(--bs-gray-100);
}

.add-slot-btn {
    padding: 0.125rem 0.375rem;
    font-size: 0.75rem;
    line-height: 1.5;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/admin_slots.js') }}"></script>
@endpush