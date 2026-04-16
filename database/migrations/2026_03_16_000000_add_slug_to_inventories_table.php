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
        Schema::table('inventories', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('item_name');
        });

        // Seed initial slugs
        \Illuminate\Support\Facades\DB::table('inventories')->where('item_name', 'Cap')->update(['slug' => 'cap']);
        \Illuminate\Support\Facades\DB::table('inventories')->where('item_name', 'Gallon')->update(['slug' => 'gallon']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
