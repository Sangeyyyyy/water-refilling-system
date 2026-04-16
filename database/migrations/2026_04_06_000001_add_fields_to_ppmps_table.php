<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppmps', function (Blueprint $table) {
            $table->string('budget_code')->nullable()->after('id');
            $table->enum('ppmp_type', ['DBM', 'NON-DBM', 'LIB'])->nullable()->after('budget_code');
            $table->text('description')->nullable()->after('ppmp_type');
            $table->dateTime('president_approved_date')->nullable()->after('description');
            $table->string('fund_manager')->nullable()->after('president_approved_date');
            $table->string('fund_manager_email')->nullable()->after('fund_manager');
        });
    }

    public function down(): void
    {
        Schema::table('ppmps', function (Blueprint $table) {
            $table->dropColumn([
                'budget_code',
                'ppmp_type',
                'description',
                'president_approved_date',
                'fund_manager',
                'fund_manager_email',
            ]);
        });
    }
};
