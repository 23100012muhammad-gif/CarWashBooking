<?php
// Script untuk membersihkan file dan data lama

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "🧹 Membersihkan sistem lama...\n";
    
    // Hapus semua slot yang salah format atau duplikat
    $wrongSlots = DB::table('booking_slots')
        ->where(function($query) {
            $query->whereRaw('TIME_TO_SEC(jam_selesai) - TIME_TO_SEC(jam_mulai) < 3600') // kurang dari 1 jam
                  ->orWhereRaw('TIME_TO_SEC(jam_selesai) - TIME_TO_SEC(jam_mulai) > 10800'); // lebih dari 3 jam
        })
        ->get();
    
    foreach ($wrongSlots as $slot) {
        $duration = (strtotime($slot->jam_selesai) - strtotime($slot->jam_mulai)) / 60;
        echo "❌ Hapus slot salah: {$slot->tanggal} {$slot->jam_mulai}-{$slot->jam_selesai} (durasi: {$duration} menit)\n";
        DB::table('booking_slots')->where('id', $slot->id)->delete();
    }
    
    // Hapus operational days (tidak diperlukan lagi)
    $deletedOpDays = DB::table('operational_days')->count();
    DB::table('operational_days')->truncate();
    echo "✅ Hapus {$deletedOpDays} data hari operasional lama\n";
    
    echo "\n📋 Slot yang tersisa (sistem baru):\n";
    $remainingSlots = DB::table('booking_slots')
        ->orderBy('tanggal')
        ->orderBy('jam_mulai')
        ->get();
        
    if ($remainingSlots->isEmpty()) {
        echo "✅ Database bersih! Siap untuk sistem baru.\n";
    } else {
        $groupedSlots = $remainingSlots->groupBy('tanggal');
        foreach ($groupedSlots as $date => $daySlots) {
            echo "\n📅 {$date}: {$daySlots->count()} slot\n";
            foreach ($daySlots as $slot) {
                $duration = (strtotime($slot->jam_selesai) - strtotime($slot->jam_mulai)) / 60;
                echo "  - {$slot->jam_mulai} - {$slot->jam_selesai} (durasi: {$duration} menit)\n";
            }
        }
    }
    
    echo "\n🎉 Sistem sudah bersih dan siap digunakan!\n";
    echo "📝 Langkah selanjutnya:\n";
    echo "1. Login sebagai admin\n";
    echo "2. Buka menu 'Jadwal & Slot'\n";
    echo "3. Buat jadwal baru (pilih tanggal, jumlah slot, jam mulai)\n";
    echo "4. User bisa booking dari jadwal yang sudah dibuat\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>