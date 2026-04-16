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
        Schema::table('offices', function (Blueprint $table) {
            $table->integer('gallon_count')->default(0)->after('division_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('gallon_count')->default(0)->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn('gallon_count');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gallon_count');
        });
    }
};
