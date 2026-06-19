<?php
// Bersihkan database dan perbaiki sistem

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "🧹 Membersihkan dan memperbaiki database...\n\n";
    
    // 1. Hapus tabel operational_days (tidak berguna)
    if (Schema::hasTable('operational_days')) {
        Schema::drop('operational_days');
        echo "✅ Tabel operational_days dihapus\n";
    }
    
    // 2. Bersihkan booking_slots
    DB::table('booking_slots')->truncate();
    echo "✅ Tabel booking_slots dibersihkan\n";
    
    // 3. Buat jadwal test langsung ke database
    echo "\n📅 Membuat jadwal test...\n";
    
    $testSchedules = [
        [
            'tanggal' => '2025-11-05',
            'slots' => [
                ['08:00:00', '10:00:00'],
                ['10:00:00', '12:00:00'],
                ['13:00:00', '15:00:00'],
                ['15:00:00', '17:00:00']
            ]
        ],
        [
            'tanggal' => '2025-11-06',
            'slots' => [
                ['08:00:00', '10:00:00'],
                ['13:00:00', '15:00:00'],
                ['15:00:00', '17:00:00']
            ]
        ],
        [
            'tanggal' => '2025-11-07',
            'slots' => [
                ['09:00:00', '11:00:00'],
                ['14:00:00', '16:00:00']
            ]
        ]
    ];
    
    foreach ($testSchedules as $schedule) {
        foreach ($schedule['slots'] as $slot) {
            DB::table('booking_slots')->insert([
                'tanggal' => $schedule['tanggal'],
                'jam_mulai' => $slot[0],
                'jam_selesai' => $slot[1],
                'kapasitas' => 4,
                'terisi' => 0,
                'status' => 'tersedia',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        echo "✅ Jadwal {$schedule['tanggal']}: " . count($schedule['slots']) . " slot\n";
    }
    
    echo "\n🎉 Database berhasil diperbaiki!\n";
    echo "📋 Total slot: " . DB::table('booking_slots')->count() . "\n";
    
    // 4. Test list jadwal
    echo "\n🔍 Test list jadwal:\n";
    $schedules = DB::table('booking_slots')
        ->select('tanggal', DB::raw('COUNT(*) as slot_count'))
        ->groupBy('tanggal')
        ->orderBy('tanggal')
        ->get();
        
    foreach ($schedules as $schedule) {
        echo "  - {$schedule->tanggal}: {$schedule->slot_count} slot\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>