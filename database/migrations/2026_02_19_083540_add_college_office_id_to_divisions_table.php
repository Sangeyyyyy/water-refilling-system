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
        Schema::table('divisions', function (Blueprint $table) {
            $table->foreignId('college_office_id')->nullable()->after('campus_id')->constrained('college_offices')->onDelete('cascade');
            $table->unsignedBigInteger('campus_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropForeign(['college_office_id']);
            $table->dropColumn('college_office_id');
            $table->unsignedBigInteger('campus_id')->nullable(false)->change();
        });
    }
};
