<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefundFieldsToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('refund_reason')->nullable()->after('payment_notes');
            $table->timestamp('refund_requested_at')->nullable()->after('refund_reason');
            $table->timestamp('refund_processed_at')->nullable()->after('refund_requested_at');
            $table->integer('refund_processed_by')->nullable()->after('refund_processed_at');
            $table->text('refund_notes')->nullable()->after('refund_processed_by');
            $table->boolean('hidden_from_user')->default(false)->after('refund_notes');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'refund_reason',
                'refund_requested_at', 
                'refund_processed_at',
                'refund_processed_by',
                'refund_notes',
                'hidden_from_user'
            ]);
        });
    }
}