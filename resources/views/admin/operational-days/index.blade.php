@extends('layouts.admin_master')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Manajemen Hari Operasional</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.operational-days.update') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                @foreach($days as $day)
                    <div class="border p-4 rounded">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium">{{ $day }}</h3>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       name="days[{{ $day }}][status]" 
                                       class="sr-only peer"
                                       value="1"
                                       {{ isset($operationalDays[$day]) && $operationalDays[$day]->status_operasional ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                <span class="ml-3 text-sm font-medium">Aktif</span>
                            </label>
                        </div>
                        
                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jam Buka</label>
                                <input type="time" 
                                       name="days[{{ $day }}][jam_buka]" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                       value="{{ isset($operationalDays[$day]) ? $operationalDays[$day]->jam_buka->format('H:i') : '08:00' }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jam Tutup</label>
                                <input type="time" 
                                       name="days[{{ $day }}][jam_tutup]" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                       value="{{ isset($operationalDays[$day]) ? $operationalDays[$day]->jam_tutup->format('H:i') : '18:00' }}">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Generate Slot Booking</h2>
        
        <form action="{{ route('admin.operational-days.generate-slots') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <input type="date" 
                           name="start_date" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                           min="{{ date('Y-m-d') }}"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                    <input type="date" 
                           name="end_date" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                           min="{{ date('Y-m-d') }}"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Durasi per Slot (menit)</label>
                    <select name="slot_duration" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="30">30 menit</option>
                        <option value="45">45 menit</option>
                        <option value="60">60 menit</option>
                        <option value="90">90 menit</option>
                        <option value="120">120 menit</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Generate Slot
                </button>
            </div>
        </form>
    </div>
</div>
@endsection