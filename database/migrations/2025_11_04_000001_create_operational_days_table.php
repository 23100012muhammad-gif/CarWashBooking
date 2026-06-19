<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('operational_days', function (Blueprint $table) {
            $table->id();
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']);
            $table->boolean('status_operasional')->default(true);
            $table->time('jam_buka');
            $table->time('jam_tutup');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->unique('hari');
        });
    }

    public function down()
    {
        Schema::dropIfExists('operational_days');
    }
};