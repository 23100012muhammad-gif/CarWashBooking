<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status')->default('pending')->after('status');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->text('payment_proof')->nullable()->after('payment_reference');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_proof');
            $table->integer('verified_by')->nullable()->after('payment_verified_at');
            $table->text('payment_notes')->nullable()->after('verified_by');
            $table->string('transaction_id')->nullable()->after('payment_notes');
            $table->string('gateway_response')->nullable()->after('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_method',
                'payment_reference',
                'payment_proof',
                'payment_verified_at',
                'verified_by',
                'payment_notes',
                'transaction_id',
                'gateway_response'
            ]);
        });
    }
}

