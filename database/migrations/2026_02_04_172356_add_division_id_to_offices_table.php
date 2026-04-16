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
            $table->foreignId('division_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            // Make existing campus and division nullable so we can migrate data smoothly
            $table->string('campus')->nullable()->change();
            $table->string('division')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropColumn('division_id');
            $table->string('campus')->nullable(false)->change();
            $table->string('division')->nullable(false)->change();
        });
    }
};
