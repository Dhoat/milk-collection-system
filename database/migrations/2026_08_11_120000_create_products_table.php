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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->unique();
            $table->string('name');
            $table->string('category')->default('raw_milk');
            $table->string('unit')->default('Litre'); // Litre, Kg, Packet, Box
            $table->decimal('unit_price', 10, 2);
            $table->decimal('stock_quantity', 10, 2)->default(0.00);
            $table->boolean('status')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
