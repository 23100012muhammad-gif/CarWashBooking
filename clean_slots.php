<?php
// Script untuk membersihkan data slot yang tidak diinginkan

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Hapus semua data booking slots
    DB::table('booking_slots')->truncate();
    echo "✅ Berhasil membersihkan data booking slots\n";
    
    // Hapus semua data operational days
    DB::table('operational_days')->truncate();
    echo "✅ Berhasil membersihkan data operational days\n";
    
    echo "\n🎉 Database sudah bersih! Sekarang admin harus:\n";
    echo "1. Set hari operasional di menu Jadwal & Slot\n";
    echo "2. Generate slot atau tambah slot manual\n";
    echo "3. Baru user bisa booking\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>