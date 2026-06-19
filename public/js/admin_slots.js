// admin_slots.js
// Handles admin calendar rendering, adding slots and operational days
// // updated by Copilot for admin slot management UI

let currentSlots = [];

// Load slots for selected month
async function loadSlots(monthYear) {
    try {
        const response = await fetch(`/admin/slots?month=${monthYear}`);
        const data = await response.json();
        if (data.status === 'success') {
            currentSlots = data.data;
            renderCalendar(monthYear);
        }
    } catch (error) {
        console.error('Failed to load slots:', error);
        showAlert('error', 'Gagal memuat data slot');
    }
}

// Render calendar with slots
function renderCalendar(monthYear) {
    const [year, month] = monthYear.split('-');
    const firstDay = new Date(year, month - 1, 1);
    const lastDay = new Date(year, month, 0);
    const tbody = document.getElementById('slot-calendar');

    // Ensure we are populating tbody only (thead is defined in blade)
    tbody.innerHTML = '';

    let currentRow = document.createElement('tr');
    let currentCol = 0;

    // Add empty cells for days before first day of month
    for (let i = 0; i < firstDay.getDay(); i++) {
        currentRow.appendChild(createEmptyCell());
        currentCol++;
    }

    // Add days with slots
    for (let date = 1; date <= lastDay.getDate(); date++) {
        const currentDate = `${year}-${month.padStart(2, '0')}-${date.toString().padStart(2, '0')}`;
        const isToday = new Date().toISOString().split('T')[0] === currentDate;

        const cell = document.createElement('td');
        cell.className = 'slot-cell position-relative' + (isToday ? ' calendar-today' : '');

        // Header with date and add button
        const header = document.createElement('div');
        header.className = 'd-flex justify-content-between align-items-center mb-2';
        header.innerHTML = `
            <span class="fw-bold">${date}</span>
            <button type="button" onclick="showAddSlotModal('${currentDate}')" 
                    class="btn btn-outline-primary btn-sm add-slot-btn">
                <i class="bi bi-plus-lg"></i>
            </button>
        `;
        cell.appendChild(header);

        // Slots container
        const slotsContainer = document.createElement('div');
        slotsContainer.className = 'd-flex flex-column gap-1';

        const daySlots = currentSlots.filter(slot => slot.tanggal === currentDate || slot.tanggal === currentDate + ' 00:00:00');
        if (daySlots.length > 0) {
            daySlots.forEach(slot => {
                const slotElement = document.createElement('div');
                slotElement.className = `slot-item ${getSlotStatusClass(slot.status)}`;
                slotElement.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <span>${slot.jam_mulai}-${slot.jam_selesai}</span>
                        <small class="badge ${getBadgeClass ? getBadgeClass(slot.status) : ''}">
                            ${slot.terisi}/${slot.kapasitas}
                        </small>
                    </div>
                `;
                slotsContainer.appendChild(slotElement);
            });
        }

        cell.appendChild(slotsContainer);
        currentRow.appendChild(cell);
        currentCol++;

        // Start new row after Saturday (7 columns)
        if (currentCol === 7) {
            tbody.appendChild(currentRow);
            currentRow = document.createElement('tr');
            currentCol = 0;
        }
    }

    // Fill remaining cells in last week
    if (currentCol > 0) {
        for (let i = currentCol; i < 7; i++) {
            currentRow.appendChild(createEmptyCell());
        }
        tbody.appendChild(currentRow);
    }
}

// Helper to create empty calendar cell (td)
function createEmptyCell() {
    const cell = document.createElement('td');
    cell.className = 'slot-cell calendar-other-month';
    return cell;
}
// Helper function for slot status classes
function getSlotStatusClass(status) {
    switch (status) {
        case 'tersedia':
            return 'slot-available';
        case 'penuh':
            return 'slot-full';
        case 'nonaktif':
            return 'slot-disabled';
        default:
            return '';
    }
}

// Helper to return bootstrap badge class for status
function getBadgeClass(status) {
    switch (status) {
        case 'tersedia':
            return 'bg-success';
        case 'penuh':
            return 'bg-danger';
        case 'nonaktif':
            return 'bg-secondary';
        default:
            return 'bg-light text-dark';
    }
}

// Modal handling
function showOperationalDaysModal() {
    const modal = document.getElementById('operational-days-modal');
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

function hideOperationalDaysModal() {
    const modal = document.getElementById('operational-days-modal');
    const bsModal = bootstrap.Modal.getInstance(modal);
    if (bsModal) bsModal.hide();
}

function showAddSlotModal(date = null) {
    const modal = document.getElementById('add-slot-modal');
    if (date) {
        modal.querySelector('[name="tanggal"]').value = date;
    }
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

function hideAddSlotModal() {
    const modal = document.getElementById('add-slot-modal');
    const bsModal = bootstrap.Modal.getInstance(modal);
    if (bsModal) bsModal.hide();
}

// Form submissions
document.addEventListener('DOMContentLoaded', function() {
    // Operational days form
    const operationalDaysForm = document.getElementById('operational-days-form');
    if (operationalDaysForm) {
        operationalDaysForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                // Send as FormData so Laravel correctly parses nested keys like days[Senin][jam_buka]
                const fd = new FormData(this);
                const response = await fetch('/admin/operational-days', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: fd
                });

                let data = {};
                try { data = await response.json(); } catch (err) { console.error('Failed to parse operational-days response', err); }

                if (response.ok && data.status === 'success') {
                    hideOperationalDaysModal();
                    showAlert('success', data.message || 'Jadwal operasional berhasil diupdate');
                    loadSlots(document.getElementById('month-picker').value);
                    return;
                }

                if (data && data.message) {
                    showAlert('error', data.message);
                } else if (data && data.errors) {
                    const msgs = Object.values(data.errors).flat().join('; ');
                    showAlert('error', msgs || 'Gagal mengupdate jadwal operasional');
                } else {
                    showAlert('error', 'Gagal mengupdate jadwal operasional');
                }
            } catch (error) {
                console.error('Failed to update operational days:', error);
                showAlert('error', 'Gagal mengupdate jadwal operasional');
            }
        });
    }

    // Add slot form
    const addSlotForm = document.getElementById('add-slot-form');
    if (addSlotForm) {
        addSlotForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            // Get raw time values from the form (HH:mm format)
            const jamMulai = formData.get('jam_mulai');
            const jamSelesai = formData.get('jam_selesai');
            
            // Convert to proper time format if needed
            if (jamMulai && jamMulai.includes('T')) {
                formData.set('jam_mulai', jamMulai.split('T')[1].substr(0, 5));
            }
            if (jamSelesai && jamSelesai.includes('T')) {
                formData.set('jam_selesai', jamSelesai.split('T')[1].substr(0, 5));
            }
            
            // Debug log
            console.log('Submitting slot with times:', {
                original_mulai: jamMulai,
                original_selesai: jamSelesai,
                normalized_mulai: formData.get('jam_mulai'),
                normalized_selesai: formData.get('jam_selesai')
            });

            try {
                const response = await fetch('/admin/slots', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                });

                // parse JSON safely
                let data = {};
                try {
                    data = await response.json();
                } catch (err) {
                    console.error('Failed to parse JSON response for add-slot:', err);
                }

                if (response.ok && data.status === 'success') {
                    hideAddSlotModal();
                    loadSlots(document.getElementById('month-picker').value);
                    showAlert('success', data.message || 'Slot berhasil ditambahkan');
                    return;
                }

                // If here, request failed - show server message or validation errors
                if (data && data.message) {
                    showAlert('error', data.message);
                } else if (data && data.errors) {
                    // concatenate validation errors
                    const msgs = Object.values(data.errors).flat().join('; ');
                    showAlert('error', msgs || 'Gagal menambahkan slot');
                } else {
                    showAlert('error', 'Gagal menambahkan slot');
                }
            } catch (error) {
                console.error('Failed to add slot:', error);
                showAlert('error', 'Gagal menambahkan slot');
            }
        });
    }

    // Month picker change event
    const monthPicker = document.getElementById('month-picker');
    if (monthPicker) {
        monthPicker.addEventListener('change', function() {
            loadSlots(this.value);
        });

        // Initial load
        loadSlots(monthPicker.value);
    }
});

// Alert helper with Bootstrap styling
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.setAttribute('role', 'alert');
    alertDiv.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    // Insert at the top of the container
    const container = document.querySelector('.container');
    if (container.firstChild) {
        container.insertBefore(alertDiv, container.firstChild);
    } else {
        container.appendChild(alertDiv);
    }

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alertDiv);
        bsAlert.close();
    }, 5000);
}
