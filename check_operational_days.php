<?php
// Script untuk cek data operational days

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== OPERATIONAL DAYS ===\n";
    $operationalDays = DB::table('operational_days')->get();
    
    if ($operationalDays->isEmpty()) {
        echo "❌ Tidak ada data hari operasional!\n";
        echo "Silakan set hari operasional dulu di admin.\n\n";
    } else {
        foreach ($operationalDays as $day) {
            $status = $day->status_operasional ? '✅ AKTIF' : '❌ NONAKTIF';
            echo "{$day->hari}: {$status} ({$day->jam_buka} - {$day->jam_tutup})\n";
        }
    }
    
    echo "\n=== BOOKING SLOTS ===\n";
    $slots = DB::table('booking_slots')
        ->orderBy('tanggal')
        ->orderBy('jam_mulai')
        ->get();
        
    if ($slots->isEmpty()) {
        echo "❌ Tidak ada slot booking!\n";
    } else {
        $groupedSlots = $slots->groupBy('tanggal');
        foreach ($groupedSlots as $date => $daySlots) {
            echo "\n📅 {$date}: {$daySlots->count()} slot\n";
            foreach ($daySlots as $slot) {
                echo "  - {$slot->jam_mulai} - {$slot->jam_selesai} (kapasitas: {$slot->kapasitas})\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>