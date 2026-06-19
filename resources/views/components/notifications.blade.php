{{-- // updated by Copilot for in-app notifications component --}}
<div class="relative" id="notifications-component">
    <button id="notifications-bell" class="p-2 rounded-full hover:bg-gray-100 relative">
        <!-- bell icon -->
        <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span id="notifications-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1">0</span>
    </button>

    <!-- Dropdown (simple) -->
    <div id="notifications-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white border rounded shadow z-50">
        <div class="p-3 text-sm text-gray-600">Memuat notifikasi...</div>
    </div>

    <script>
    // Minimal client-side behavior for notification bell
    (function(){
        const bell = document.getElementById('notifications-bell');
        const dropdown = document.getElementById('notifications-dropdown');
        const countEl = document.getElementById('notifications-count');

        async function loadNotifications() {
            try {
                const res = await fetch('/notifications');
                const html = await res.text();
                dropdown.innerHTML = '<div class="p-2">' + html + '</div>';
            } catch (e) {
                dropdown.innerHTML = '<div class="p-2 text-sm text-gray-500">Gagal memuat notifikasi</div>';
            }
        }

        bell.addEventListener('click', function(e){
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
            if (!dropdown.classList.contains('hidden')) {
                loadNotifications();
            }
        });

        // click outside to close
        document.addEventListener('click', function(){
            dropdown.classList.add('hidden');
        });

        // update unread count periodically
        async function updateCount() {
            try {
                const res = await fetch('/api/notifications/unread-count');
                const json = await res.json();
                if (json.status === 'success') {
                    countEl.textContent = json.data.count || 0;
                }
            } catch (e) {}
        }
        updateCount();
        setInterval(updateCount, 30000);
    })();
    </script>
</div>
