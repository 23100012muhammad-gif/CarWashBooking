@extends('layouts.admin_master')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Manajemen Slot Booking</h1>
        
        <button onclick="openModal('addSlotModal')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Tambah Slot
        </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Filter -->
        <div class="p-4 border-b">
            <form method="GET" class="flex gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <input type="date" 
                           name="start_date" 
                           value="{{ $startDate->format('Y-m-d') }}"
                           class="mt-1 block rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                    <input type="date" 
                           name="end_date" 
                           value="{{ $endDate->format('Y-m-d') }}"
                           class="mt-1 block rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="self-end">
                    <button type="submit" class="bg-gray-100 px-4 py-2 rounded hover:bg-gray-200">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Slots List -->
        <div class="p-4">
            @forelse($slots as $date => $daySlots)
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">{{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM Y') }}</h3>
                        <div class="flex gap-2">
                            <button onclick="toggleAllSlots('{{ $date }}', 'tersedia')" class="bg-green-100 text-green-800 px-3 py-1 rounded-md text-sm hover:bg-green-200">
                                Aktifkan Semua
                            </button>
                            <button onclick="toggleAllSlots('{{ $date }}', 'nonaktif')" class="bg-gray-100 text-gray-800 px-3 py-1 rounded-md text-sm hover:bg-gray-200">
                                Nonaktifkan Semua
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($daySlots as $slot)
                            <div class="border rounded p-3 {{ $slot->status === 'nonaktif' ? 'bg-gray-50' : '' }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium">{{ $slot->jam_mulai->format('H:i') }} - {{ $slot->jam_selesai->format('H:i') }}</p>
                                        <p class="text-sm text-gray-600">Terisi: {{ $slot->terisi }}/{{ $slot->kapasitas }}</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <button onclick="toggleSlotStatus('{{ $slot->id }}')" 
                                                class="px-2 py-1 rounded text-sm {{ $slot->status === 'tersedia' ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                                            {{ $slot->status === 'tersedia' ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $slot->status === 'tersedia' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($slot->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    Tidak ada slot booking dalam rentang tanggal ini
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Add Slot Modal -->
<div id="addSlotModal" class="fixed inset-0 bg-black bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="p-6">
                <h3 class="text-lg font-medium mb-4">Tambah Slot Booking</h3>
                
                <form action="{{ route('admin.booking-slots.store') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                            <input type="date" 
                                   name="tanggal" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                   min="{{ date('Y-m-d') }}"
                                   required>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jam Mulai</label>
                                <input type="time" 
                                       name="jam_mulai" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jam Selesai</label>
                                <input type="time" 
                                       name="jam_selesai" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                       required>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kapasitas</label>
                            <input type="number" 
                                   name="kapasitas" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                   min="1"
                                   max="10"
                                   value="4"
                                   required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="tersedia">Tersedia</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" 
                                onclick="closeModal('addSlotModal')"
                                class="px-4 py-2 border rounded-md hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('fixed')) {
        event.target.classList.add('hidden');
    }
}

// Toggle status satu slot
async function toggleSlotStatus(slotId) {
    try {
        const response = await fetch(`/admin/booking-slots/${slotId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            // Reload halaman untuk menampilkan perubahan
            window.location.reload();
        } else {
            alert('Gagal mengubah status slot');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengubah status slot');
    }
}

// Toggle status semua slot dalam satu hari
async function toggleAllSlots(date, status) {
    try {
        const response = await fetch(`/admin/booking-slots/toggle-all`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                tanggal: date,
                status: status
            })
        });
        
        if (response.ok) {
            // Reload halaman untuk menampilkan perubahan
            window.location.reload();
        } else {
            alert('Gagal mengubah status slot');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengubah status slot');
    }
}
</script>
@endpush
@endsection