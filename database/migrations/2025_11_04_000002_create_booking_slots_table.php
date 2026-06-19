<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('booking_slots', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('kapasitas');
            $table->integer('terisi')->default(0);
            $table->enum('status', ['tersedia', 'penuh', 'nonaktif'])->default('tersedia');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            // Composite index untuk pencarian slot berdasarkan tanggal
            $table->index(['tanggal', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_slots');
    }
};