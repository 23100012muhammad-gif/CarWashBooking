<?php
// Test direct insert ke database

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "🧪 Test direct insert...\n\n";
    
    // Test insert langsung
    $result = DB::table('booking_slots')->insert([
        'tanggal' => '2025-11-10',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'kapasitas' => 4,
        'terisi' => 0,
        'status' => 'tersedia',
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    if ($result) {
        echo "✅ Insert berhasil\n";
    } else {
        echo "❌ Insert gagal\n";
    }
    
    // Cek data
    $count = DB::table('booking_slots')->count();
    echo "📊 Total slots di database: {$count}\n\n";
    
    // Test API list
    echo "🔍 Test API list:\n";
    $schedules = DB::table('booking_slots')
        ->select('tanggal', DB::raw('COUNT(*) as slot_count'))
        ->groupBy('tanggal')
        ->orderBy('tanggal')
        ->get();
        
    foreach ($schedules as $schedule) {
        echo "  - {$schedule->tanggal}: {$schedule->slot_count} slot\n";
    }
    
    // Test route
    echo "\n🌐 Test route admin.slots.store:\n";
    $routes = app('router')->getRoutes();
    foreach ($routes as $route) {
        if ($route->getName() === 'admin.slots.store') {
            echo "✅ Route found: " . $route->uri() . " -> " . $route->getActionName() . "\n";
            break;
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>