// Datepicker initialization
document.addEventListener('DOMContentLoaded', function() {
    const datepickerElement = document.getElementById('booking-date');
    if (!datepickerElement) return;

    // Initialize flatpickr with custom configuration
    const picker = flatpickr(datepickerElement, {
        enableTime: false,
        dateFormat: "Y-m-d",
        minDate: "today",
        maxDate: new Date().fp_incr(14), // Max 14 days ahead
        disable: [function(date) {
            // Will be populated with API response
            return !availableDates.includes(date.toISOString().split('T')[0]);
        }],
        onChange: function(selectedDates, dateStr) {
            if (selectedDates.length > 0) {
                loadAvailableSlots(dateStr);
            }
        }
    });

    // Load available dates from API
    async function loadAvailableDates() {
        try {
            const response = await fetch('/booking/available-dates?range_days=14');
            const data = await response.json();
            
            if (data.status === 'success') {
                availableDates = data.data;
                picker.redraw(); // Refresh calendar with new available dates
            }
        } catch (error) {
            console.error('Failed to load available dates:', error);
            showError('Gagal memuat tanggal yang tersedia');
        }
    }

    // Load available slots for selected date
    async function loadAvailableSlots(date) {
        try {
            const response = await fetch(`/booking/slots?date=${date}`);
            const data = await response.json();
            
            if (data.status === 'success') {
                renderTimeSlots(data.data);
            }
        } catch (error) {
            console.error('Failed to load time slots:', error);
            showError('Gagal memuat slot waktu yang tersedia');
        }
    }

    // Render time slots in the UI
    function renderTimeSlots(slots) {
        const container = document.getElementById('time-slots');
        container.innerHTML = ''; // Clear existing slots

        if (slots.length === 0) {
            container.innerHTML = '<p class="text-center text-gray-500">Tidak ada slot tersedia untuk tanggal ini</p>';
            return;
        }

        slots.forEach(slot => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'slot-btn p-2 border rounded hover:bg-gray-100';
            button.dataset.slotId = slot.id;
            button.innerHTML = `
                ${slot.jam_mulai} - ${slot.jam_selesai}
                <span class="ml-2 text-sm text-gray-500">(${slot.tersedia} slot tersedia)</span>
            `;
            
            if (slot.tersedia === 0) {
                button.disabled = true;
                button.className += ' opacity-50 cursor-not-allowed';
            } else {
                button.onclick = () => selectTimeSlot(slot.id);
            }

            container.appendChild(button);
        });
    }

    // Handle time slot selection
    function selectTimeSlot(slotId) {
        document.getElementById('booking_slot_id').value = slotId;
        
        // Highlight selected slot
        document.querySelectorAll('.slot-btn').forEach(btn => {
            btn.classList.remove('bg-blue-100');
            if (btn.dataset.slotId === slotId.toString()) {
                btn.classList.add('bg-blue-100');
            }
        });
    }

    // Show error message
    function showError(message) {
        const alert = document.createElement('div');
        alert.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4';
        alert.role = 'alert';
        alert.innerHTML = message;

        const container = document.querySelector('.booking-container');
        container.insertBefore(alert, container.firstChild);

        setTimeout(() => alert.remove(), 5000);
    }

    // Initial load of available dates
    loadAvailableDates();
});