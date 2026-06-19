<?php
// Script untuk test API available-schedules

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "🔍 Testing API available-schedules...\n\n";
    
    // Test 1: Cek data di database
    echo "1. Checking database slots:\n";
    $slots = DB::table('booking_slots')
        ->where('status', 'tersedia')
        ->where('tanggal', '>=', now()->toDateString())
        ->orderBy('tanggal')
        ->orderBy('jam_mulai')
        ->get();
        
    if ($slots->isEmpty()) {
        echo "❌ No slots found in database\n";
        echo "Run: php create_test_schedule.php\n\n";
    } else {
        echo "✅ Found {$slots->count()} slots:\n";
        foreach ($slots as $slot) {
            echo "  - {$slot->tanggal} {$slot->jam_mulai}-{$slot->jam_selesai} (kapasitas: {$slot->kapasitas})\n";
        }
        echo "\n";
    }
    
    // Test 2: Test groupBy logic
    echo "2. Testing groupBy logic:\n";
    $schedules = $slots->groupBy('tanggal')
        ->map(function ($daySlots, $date) {
            return [
                'tanggal' => $date,
                'tanggal_formatted' => Carbon::parse($date)->format('d M Y'),
                'hari' => Carbon::parse($date)->locale('id')->isoFormat('dddd'),
                'slots' => $daySlots->map(function ($slot) {
                    return [
                        'id' => $slot->id,
                        'jam_mulai' => Carbon::parse($slot->jam_mulai)->format('H:i'),
                        'jam_selesai' => Carbon::parse($slot->jam_selesai)->format('H:i'),
                        'kapasitas' => $slot->kapasitas,
                        'terisi' => $slot->terisi ?? 0,
                        'tersedia' => $slot->kapasitas - ($slot->terisi ?? 0)
                    ];
                })->values()->toArray()
            ];
        })->values()->toArray();
    
    echo "✅ Processed schedules:\n";
    echo json_encode($schedules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    
    // Test 3: Test actual API call
    echo "3. Testing actual API response:\n";
    $controller = new \App\Http\Controllers\BookingController();
    $request = new \Illuminate\Http\Request();
    $response = $controller->getAvailableSchedules($request);
    
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>