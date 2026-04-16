<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('budget_deducted')->default(false)->after('inventory_deducted');
        });

        // Update existing orders: set budget_deducted = true for active office orders with PPMP
        Order::where('customer_type', 'Office')
            ->whereIn('status', ['confirmed', 'out_for_delivery', 'completed'])
            ->whereNotNull('ppmp_id')
            ->update(['budget_deducted' => true]);
            
        // Also handle cases where ppmp_id might be null but budget_code is present (older orders)
        Order::where('customer_type', 'Office')
            ->whereIn('status', ['confirmed', 'out_for_delivery', 'completed'])
            ->whereNull('ppmp_id')
            ->whereNotNull('budget_code')
            ->update(['budget_deducted' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('budget_deducted');
        });
    }
};
