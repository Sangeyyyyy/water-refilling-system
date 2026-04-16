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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('office_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contact_number')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            // We keep user_id for staff/admin actions, but make it nullable if not already
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
            // Reverting user_id change might be tricky if data exists, skipping strict revert for now to avoid issues
        });

        Schema::dropIfExists('clients');
    }
};
