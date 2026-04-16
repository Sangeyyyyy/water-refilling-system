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
            $table->string('customer_type')->default('Individual')->after('client_name');
            $table->string('pr_number')->nullable()->after('customer_type');
            $table->string('budget_code')->nullable()->after('pr_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['customer_type', 'pr_number', 'budget_code']);
        });
    }
};
