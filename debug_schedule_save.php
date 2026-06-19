<?php
// Debug penyimpanan jadwal

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "🔍 Debug penyimpanan jadwal...\n\n";
    
    // Cek data terbaru di booking_slots
    echo "1. Data terbaru di booking_slots:\n";
    $slots = DB::table('booking_slots')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
        
    if ($slots->isEmpty()) {
        echo "❌ Tidak ada data di booking_slots\n";
    } else {
        foreach ($slots as $slot) {
            echo "  - ID: {$slot->id}, Tanggal: {$slot->tanggal}, Waktu: {$slot->jam_mulai}-{$slot->jam_selesai}, Status: {$slot->status}\n";
        }
    }
    
    echo "\n2. Test API list jadwal:\n";
    $controller = new \App\Http\Controllers\Admin\SimpleScheduleController();
    $response = $controller->list();
    
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>