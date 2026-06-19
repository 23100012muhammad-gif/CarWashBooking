<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Hapus semua data slot yang ada (karena mungkin ada data test/fallback)
        DB::table('booking_slots')->truncate();
    }

    public function down()
    {
        // Tidak ada rollback untuk data cleaning
    }
};