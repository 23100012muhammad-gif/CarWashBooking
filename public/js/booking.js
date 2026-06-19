// booking.js
// Handles datepicker, available slots, booking submission and payment-proof upload
// // updated by Copilot for slot booking logic

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // --- Booking page logic ---
    const dateInput = document.getElementById('booking-date');
    const timeSlotsContainer = document.getElementById('time-slots');
    const bookingSlotInput = document.getElementById('booking_slot_id');
    const bookingForm = document.getElementById('booking-form');

    let availableDates = [];

    async function fetchAvailableDates(range = 7) {
        try {
            // include selected service if present to get dates relevant to that service
            const serviceSelect = document.querySelector('[name="layanan_id"]');
            const serviceId = serviceSelect ? serviceSelect.value : '';
            const res = await fetch(`/api/slot-availability?range_days=${range}${serviceId ? '&service_id=' + serviceId : ''}`);
            const json = await res.json();
            if (json.status === 'success') {
                availableDates = json.data.map(d => d);
            } else {
                console.warn('Failed to load available dates', json);
            }
        } catch (err) {
            console.error('Error fetching available dates', err);
        }
    }

    function isDateAvailable(date) {
        // date is a Date object
        const iso = date.toISOString().split('T')[0];
        return availableDates.includes(iso);
    }

    async function initDatepicker() {
        await fetchAvailableDates(parseInt(getConfig('range_days') || 7));

        if (!dateInput) return;

        flatpickr(dateInput, {
            dateFormat: 'Y-m-d',
            minDate: 'today',
            maxDate: new Date().fp_incr(parseInt(getConfig('max_days') || 14)),
            disable: [function(date) {
                return !isDateAvailable(date);
            }],
            onChange: function(selectedDates, dateStr) {
                if (dateStr) {
                    loadSlots(dateStr);
                }
            }
        });
    }

    async function loadSlots(date) {
        if (!timeSlotsContainer) return;
        timeSlotsContainer.innerHTML = '<div class="col-span-3 text-center">Memuat slot...</div>';

        try {
            const serviceSelect = document.querySelector('[name="layanan_id"]');
            const serviceId = serviceSelect ? serviceSelect.value : '';
            const res = await fetch(`/api/slots-for-date?date=${date}${serviceId ? '&service_id=' + serviceId : ''}`);
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            const json = await res.json();
            if (json.status === 'success') {
                // controller returns { status: 'success', data: [...] }
                renderTimeSlots(json.data || []);
            } else {
                console.warn('Server returned error:', json);
                timeSlotsContainer.innerHTML = `<div class="text-red-500">Gagal memuat slot: ${json.message || 'Unknown error'}</div>`;
            }
        } catch (err) {
            console.error('Failed to load slots:', err);
            timeSlotsContainer.innerHTML = `<div class="text-red-500">Gagal memuat slot: ${err.message}</div>`;
        }
    }

    function renderTimeSlots(slots) {
        timeSlotsContainer.innerHTML = '';
        if (!slots || slots.length === 0) {
            timeSlotsContainer.innerHTML = '<p class="text-center text-gray-500 col-span-3">Tidak ada slot tersedia untuk tanggal ini</p>';
            return;
        }

            slots.forEach(slot => {
            const available = slot.tersedia > 0 && slot.status === 'tersedia';
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'slot-btn p-2 border rounded hover:bg-gray-100 flex justify-between items-center';
            btn.dataset.slotId = slot.id;

            // status indicator
            const statusSpan = document.createElement('span');
            statusSpan.className = 'text-sm ml-2';
            statusSpan.textContent = `${slot.tersedia} tersedia`;

            // label
            const label = document.createElement('div');
            label.innerHTML = `<div>${slot.jam_mulai} - ${slot.jam_selesai}</div>`;            if (slot.status === 'nonaktif') {
                btn.classList.add('bg-gray-100', 'text-gray-500');
                btn.disabled = true;
                statusSpan.textContent = 'nonaktif';
            } else if (!available) {
                btn.classList.add('bg-red-100', 'text-red-800');
                btn.disabled = true;
                statusSpan.textContent = 'penuh';
                btn.addEventListener('click', () => {
                    showAlert('error', 'Maaf, slot ini sudah penuh. Silakan pilih jadwal lain.');
                });
            } else {
                btn.classList.add('bg-green-50', 'text-green-800');
                btn.addEventListener('click', () => selectTimeSlot(slot.id, btn));
            }

            btn.appendChild(label);
            btn.appendChild(statusSpan);
            timeSlotsContainer.appendChild(btn);
        });
    }

    function selectTimeSlot(slotId, btn) {
        if (bookingSlotInput) bookingSlotInput.value = slotId;
        // highlight
        document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('ring', 'ring-2', 'ring-blue-300'));
        btn.classList.add('ring', 'ring-2', 'ring-blue-300');
    }

    // Booking form submission
    if (bookingForm) {
        bookingForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const payload = {};
            new FormData(bookingForm).forEach((v,k) => payload[k]=v);

            // Basic client-side validation
            if (!payload.booking_slot_id) { showAlert('error','Silakan pilih slot terlebih dahulu'); return; }
            if (!payload.plat_nomor) { showAlert('error','Silakan masukkan nomor plat kendaraan'); return; }

            try {
                const res = await fetch('/pesan/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const json = await res.json();
                if (json.status === 'success') {
                    // redirect to payment confirmation or status
                    window.location.href = `/payment/confirmation/${json.data.id}`;
                } else {
                    showAlert('error', json.message || 'Gagal membuat pesanan');
                }
            } catch (err) {
                console.error('Booking failed', err);
                showAlert('error', 'Gagal membuat pesanan');
            }
        });
    }

    // --- Upload payment proof logic ---
    const uploadForm = document.getElementById('upload-form');
    if (uploadForm) {
        const feedbackWrap = document.getElementById('upload-feedback');
        const progressBar = document.getElementById('upload-progress');
        const uploadMessage = document.getElementById('upload-message');

        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const url = uploadForm.dataset.uploadUrl;
            if (!url) { showAlert('error', 'Upload URL tidak ditemukan'); return; }

            const fd = new FormData(uploadForm);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', url, true);
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

            xhr.upload.addEventListener('progress', function(evt) {
                if (evt.lengthComputable) {
                    const percent = Math.round((evt.loaded / evt.total) * 100);
                    feedbackWrap.classList.remove('hidden');
                    progressBar.style.width = percent + '%';
                    uploadMessage.textContent = `Mengupload: ${percent}%`;
                }
            });

            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    try {
                        const res = JSON.parse(xhr.responseText);
                        if (xhr.status >= 200 && xhr.status < 300 && res.status === 'success') {
                            uploadMessage.textContent = 'Bukti pembayaran berhasil diunggah, menunggu verifikasi admin.';
                            showAlert('success', 'Bukti pembayaran berhasil diunggah, menunggu verifikasi admin.');
                            // optionally redirect or refresh
                        } else {
                            uploadMessage.textContent = 'Gagal mengunggah bukti pembayaran.';
                            showAlert('error', res.message || 'Gagal mengunggah bukti pembayaran');
                        }
                    } catch (err) {
                        showAlert('error', 'Respons tidak valid dari server');
                    }
                }
            };

            xhr.send(fd);
        });
    }

    // --- Notifications small helper (optional) ---
    // fetch unread count for bell icon
    if (document.getElementById('notifications-bell')) {
        fetch('/api/notifications/unread-count')
            .then(r => r.json())
            .then(j => {
                if (j.status === 'success') {
                    const el = document.getElementById('notifications-count');
                    if (el) el.textContent = j.data.count || 0;
                }
            }).catch(()=>{});
    }

    // Helpers
    function showAlert(type, message) {
        // simple toast - append to body
        const id = 'copilot-alert-' + Date.now();
        const div = document.createElement('div');
        div.id = id;
        div.className = 'fixed right-4 top-4 z-50 p-3 rounded shadow';
        div.style.maxWidth = '320px';
        if (type === 'error') {
            div.style.background = '#fee2e2'; div.style.color = '#b91c1c';
        } else {
            div.style.background = '#ecfdf5'; div.style.color = '#065f46';
        }
        div.textContent = message;
        document.body.appendChild(div);
        setTimeout(() => document.getElementById(id)?.remove(), 5000);
    }

    function getConfig(key) {
        // placeholder to get config if needed from meta tags or global var
        return document.querySelector(`meta[name="booking-${key}"]`)?.content;
    }

    // Initialize datepicker on pages that have it
    if (dateInput) initDatepicker();
});
