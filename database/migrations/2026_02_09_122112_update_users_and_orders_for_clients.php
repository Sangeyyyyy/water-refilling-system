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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->foreignId('office_id')->nullable()->after('last_name')->constrained('offices')->onDelete('set null');
            $table->string('contact_number')->nullable()->after('office_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['office_id']);
            $table->dropColumn(['first_name', 'last_name', 'office_id', 'contact_number']);
        });
    }
};
