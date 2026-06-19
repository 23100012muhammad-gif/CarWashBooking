<?php
// Script untuk membuat jadwal test

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "📅 Membuat jadwal test untuk 3 hari ke depan...\n";
    
    // Hapus slot lama
    DB::table('booking_slots')->truncate();
    
    // Buat jadwal untuk 3 hari ke depan
    for ($i = 0; $i < 3; $i++) {
        $tanggal = Carbon::now()->addDays($i)->format('Y-m-d');
        
        // Buat 4 slot per hari (08:00-10:00, 10:00-12:00, 13:00-15:00, 15:00-17:00)
        $slots = [
            ['08:00:00', '10:00:00'],
            ['10:00:00', '12:00:00'], 
            ['13:00:00', '15:00:00'],
            ['15:00:00', '17:00:00']
        ];
        
        foreach ($slots as $slot) {
            DB::table('booking_slots')->insert([
                'tanggal' => $tanggal,
                'jam_mulai' => $slot[0],
                'jam_selesai' => $slot[1],
                'kapasitas' => 4,
                'terisi' => 0,
                'status' => 'tersedia',
                'created_by' => 1, // Assuming admin user ID is 1
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        echo "✅ Jadwal untuk {$tanggal}: 4 slot\n";
    }
    
    echo "\n🎉 Jadwal test berhasil dibuat!\n";
    echo "📋 Sekarang user bisa pilih jadwal di form booking.\n";
    
    // Tampilkan jadwal yang dibuat
    $schedules = DB::table('booking_slots')
        ->orderBy('tanggal')
        ->orderBy('jam_mulai')
        ->get();
        
    $groupedSchedules = $schedules->groupBy('tanggal');
    foreach ($groupedSchedules as $date => $daySlots) {
        echo "\n📅 {$date}:\n";
        foreach ($daySlots as $slot) {
            echo "  - {$slot->jam_mulai} - {$slot->jam_selesai} (kapasitas: {$slot->kapasitas})\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>