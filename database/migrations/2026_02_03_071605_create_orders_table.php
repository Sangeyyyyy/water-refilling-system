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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->unsignedBigInteger('office_id');
            $table->string('contact_number')->nullable();
            $table->integer('quantity');
            $table->decimal('total_amount', 10, 2);
            $table->string('order_type')->default('online'); // 'online', 'walk-in'
            $table->string('status')->default('pending'); // pending, confirmed, completed, cancelled
            $table->date('delivery_date');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
