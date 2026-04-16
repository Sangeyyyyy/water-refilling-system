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
        Schema::create('ppmps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->onDelete('cascade');
            $table->integer('fiscal_year');
            $table->decimal('total_budget', 15, 2);
            $table->decimal('remaining_budget', 15, 2);
            $table->string('status')->default('draft'); // draft, approved
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppmps');
    }
};
