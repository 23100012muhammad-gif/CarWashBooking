<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('judul');
            $table->text('pesan');
            $table->enum('jenis', ['email', 'system']);
            $table->enum('status', ['terkirim', 'belum', 'gagal'])->default('belum');
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            // Index untuk mencari notifikasi yang belum terkirim
            $table->index(['status', 'jenis']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};