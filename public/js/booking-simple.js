// Simple booking form handler
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('booking-form');
    const serviceSelect = document.getElementById('service_id');
    const bookingDate = document.getElementById('booking_date');
    const selectedSlotTime = document.getElementById('selected_slot_time');
    
    // Form validation before submit
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!selectedSlotTime.value) {
                e.preventDefault();
                alert('Silakan pilih waktu slot terlebih dahulu');
                return false;
            }
        });
    }
    
    // Auto-load slots if both service and date are selected
    function checkAndLoadSlots() {
        if (serviceSelect.value && bookingDate.value) {
            // Trigger the existing loadTimeSlots function
            if (typeof loadTimeSlots === 'function') {
                loadTimeSlots(bookingDate.value);
            }
        }
    }
    
    // Listen for changes
    if (serviceSelect) {
        serviceSelect.addEventListener('change', checkAndLoadSlots);
    }
    
    if (bookingDate) {
        bookingDate.addEventListener('change', checkAndLoadSlots);
    }
});