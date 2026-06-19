<?php
// Script untuk membersihkan slot yang salah

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "🧹 Membersihkan slot yang salah...\n";
    
    // Hapus slot yang salah (durasi bukan 60 menit)
    $wrongSlots = DB::table('booking_slots')
        ->whereRaw('TIME_TO_SEC(jam_selesai) - TIME_TO_SEC(jam_mulai) != 3600') // bukan 60 menit
        ->get();
    
    foreach ($wrongSlots as $slot) {
        $duration = (strtotime($slot->jam_selesai) - strtotime($slot->jam_mulai)) / 60;
        echo "❌ Hapus slot salah: {$slot->tanggal} {$slot->jam_mulai}-{$slot->jam_selesai} (durasi: {$duration} menit)\n";
        DB::table('booking_slots')->where('id', $slot->id)->delete();
    }
    
    echo "\n✅ Selesai membersihkan!\n";
    echo "📋 Slot yang tersisa:\n";
    
    $remainingSlots = DB::table('booking_slots')
        ->orderBy('tanggal')
        ->orderBy('jam_mulai')
        ->get();
        
    $groupedSlots = $remainingSlots->groupBy('tanggal');
    foreach ($groupedSlots as $date => $daySlots) {
        echo "\n📅 {$date}: {$daySlots->count()} slot\n";
        foreach ($daySlots as $slot) {
            echo "  - {$slot->jam_mulai} - {$slot->jam_selesai}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>