<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('original_price')->nullable()->after('status');
            $table->integer('discount_percent')->nullable()->after('original_price');
            $table->integer('final_price')->nullable()->after('discount_percent');
            $table->string('discount_name')->nullable()->after('final_price');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['original_price', 'discount_percent', 'final_price', 'discount_name']);
        });
    }
};
