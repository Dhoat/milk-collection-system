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
        Schema::create('milk_stocks', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->enum('type', ['in', 'out']);
            $table->string('item_type')->default('raw_milk');
            $table->decimal('quantity', 10, 2);
            $table->decimal('fat', 4, 2)->nullable();
            $table->decimal('snf', 4, 2)->nullable();
            $table->foreignId('milk_receiving_id')->nullable()->unique()->constrained('milk_receivings')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('source_or_reason');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['transaction_date', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milk_stocks');
    }
};
