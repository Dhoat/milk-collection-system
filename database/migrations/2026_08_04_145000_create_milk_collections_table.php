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
        Schema::create('milk_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('farmers')->cascadeOnDelete();
            $table->date('collection_date');
            $table->string('shift'); // morning, evening
            $table->decimal('milk_quantity', 8, 2);
            $table->decimal('fat', 4, 2)->nullable();
            $table->decimal('snf', 4, 2)->nullable();
            $table->decimal('rate', 8, 2);
            $table->decimal('amount', 12, 2);
            $table->text('notes')->nullable();
            
            // Unique constraint to prevent duplicate entries for same farmer, date, and shift
            $table->unique(['farmer_id', 'collection_date', 'shift']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milk_collections');
    }
};
