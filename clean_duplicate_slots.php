<?php
// Script untuk membersihkan slot duplikat/overlap

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Hapus semua slot untuk tanggal 5 November 2025
    $deleted = DB::table('booking_slots')
        ->where('tanggal', '2025-11-05')
        ->delete();
    
    echo "✅ Berhasil menghapus {$deleted} slot duplikat untuk tanggal 2025-11-05\n";
    
    // Hapus semua slot yang overlap (jam_mulai sama tapi jam_selesai berbeda)
    $duplicates = DB::table('booking_slots')
        ->select('tanggal', 'jam_mulai', DB::raw('COUNT(*) as count'))
        ->groupBy('tanggal', 'jam_mulai')
        ->having('count', '>', 1)
        ->get();
    
    foreach ($duplicates as $dup) {
        // Keep only the first one, delete the rest
        $slots = DB::table('booking_slots')
            ->where('tanggal', $dup->tanggal)
            ->where('jam_mulai', $dup->jam_mulai)
            ->orderBy('id')
            ->get();
            
        for ($i = 1; $i < $slots->count(); $i++) {
            DB::table('booking_slots')->where('id', $slots[$i]->id)->delete();
        }
        
        echo "✅ Cleaned duplicates for {$dup->tanggal} {$dup->jam_mulai}\n";
    }
    
    echo "\n🎉 Database sudah bersih dari slot duplikat!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>