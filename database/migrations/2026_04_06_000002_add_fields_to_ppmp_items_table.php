<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppmp_items', function (Blueprint $table) {
            $table->string('unit')->nullable()->after('description');
            $table->string('mode_of_procurement')->nullable()->after('unit');
            $table->integer('q1')->default(0)->after('total_price');
            $table->integer('q2')->default(0)->after('q1');
            $table->integer('q3')->default(0)->after('q2');
            $table->integer('q4')->default(0)->after('q3');
        });
    }

    public function down(): void
    {
        Schema::table('ppmp_items', function (Blueprint $table) {
            $table->dropColumn(['unit', 'mode_of_procurement', 'q1', 'q2', 'q3', 'q4']);
        });
    }
};
