<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('delivery_date');
            $table->index('created_at');
            $table->index('customer_type');
            $table->index('order_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['delivery_date']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['customer_type']);
            $table->dropIndex(['order_type']);
        });
    }
};
