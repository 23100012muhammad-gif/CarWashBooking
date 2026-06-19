<?php $__env->startSection('title', 'Kelola Jadwal & Slot'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-calendar-check"></i> Kelola Jadwal & Slot</h2>
            </div>

            <!-- Buat Jadwal Baru -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Buat Jadwal Booking Baru</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/jadwal-slot" onsubmit="debugForm(event)">
                        <?php echo csrf_field(); ?>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control" required min="<?php echo e(date('Y-m-d')); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-control" required min="<?php echo e(date('Y-m-d')); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kapasitas per Slot</label>
                                <input type="number" name="kapasitas" class="form-control" value="4" min="1" max="10" required>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-success" onclick="addTimeSlot()" style="margin-top: 32px;">
                                    <i class="bi bi-plus"></i> Tambah Waktu
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Waktu Slot</label>
                            <small class="text-muted d-block mb-2">Format: 08:00-10:00 (tanpa spasi)</small>
                            <div id="time-slots-container">
                                <div class="input-group mb-2">
                                    <input type="text" name="waktu_slot[]" class="form-control" placeholder="08:00-10:00" required>
                                    <button type="button" class="btn btn-outline-danger" onclick="removeTimeSlot(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Buat Jadwal
                        </button>
                    </form>
                </div>
            </div>

            <!-- Daftar Jadwal yang Sudah Dibuat -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Jadwal Booking</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Hari</th>
                                    <th>Jumlah Slot</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="schedules-list">
                                <!-- Will be loaded by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadSchedules();
});

function debugForm(event) {
    const formData = new FormData(event.target);
    console.log('Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    // Allow form to continue
    return true;
}

function addTimeSlot() {
    const container = document.getElementById('time-slots-container');
    const newSlot = document.createElement('div');
    newSlot.className = 'input-group mb-2';
    newSlot.innerHTML = `
        <input type="text" name="waktu_slot[]" class="form-control" placeholder="10:00-12:00" required>
        <button type="button" class="btn btn-outline-danger" onclick="removeTimeSlot(this)">
            <i class="bi bi-trash"></i>
        </button>
    `;
    container.appendChild(newSlot);
}

function removeTimeSlot(button) {
    const container = document.getElementById('time-slots-container');
    if (container.children.length > 1) {
        button.parentElement.remove();
    } else {
        alert('Minimal harus ada 1 slot waktu');
    }
}

async function loadSchedules() {
    try {
        const response = await fetch('/admin/jadwal-slot/list');
        const data = await response.json();
        
        if (data.status === 'success') {
            const tbody = document.getElementById('schedules-list');
            
            if (data.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">Belum ada jadwal booking</td></tr>';
                return;
            }
            
            tbody.innerHTML = data.data.map(schedule => `
                <tr>
                    <td>${schedule.tanggal}</td>
                    <td>${schedule.hari}</td>
                    <td>${schedule.slot_count} slot</td>
                    <td>
                        <span class="badge bg-${schedule.active ? 'success' : 'secondary'}">
                            ${schedule.active ? 'Aktif' : 'Nonaktif'}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="viewSlots('${schedule.tanggal}')">
                            <i class="bi bi-eye"></i> Lihat Slot
                        </button>
                        <button class="btn btn-sm btn-${schedule.active ? 'warning' : 'success'}" 
                                onclick="toggleSchedule('${schedule.tanggal}', ${!schedule.active})">
                            <i class="bi bi-${schedule.active ? 'pause' : 'play'}"></i> 
                            ${schedule.active ? 'Nonaktifkan' : 'Aktifkan'}
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteSchedule('${schedule.tanggal}')">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </td>
                </tr>
            `).join('');
        }
    } catch (error) {
        console.error('Error loading schedules:', error);
    }
}

function viewSlots(date) {
    alert('Fitur lihat detail slot untuk tanggal ' + date);
}

async function toggleSchedule(date, activate) {
    try {
        const response = await fetch('/admin/jadwal-slot/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                tanggal: date,
                active: activate
            })
        });
        
        if (response.ok) {
            loadSchedules();
            showAlert('success', `Jadwal ${activate ? 'diaktifkan' : 'dinonaktifkan'}`);
        }
    } catch (error) {
        console.error('Error toggling schedule:', error);
    }
}

async function deleteSchedule(date) {
    if (!confirm('Hapus semua slot untuk tanggal ' + date + '?')) return;
    
    try {
        const response = await fetch('/admin/jadwal-slot/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ tanggal: date })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            loadSchedules();
            showAlert('success', data.message);
        } else {
            showAlert('error', data.message);
        }
    } catch (error) {
        console.error('Error deleting schedule:', error);
        showAlert('error', 'Terjadi kesalahan saat menghapus jadwal');
    }
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.row'));
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin_master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\CarWashConnect\resources\views/admin/jadwal-slot-simple.blade.php ENDPATH**/ ?>