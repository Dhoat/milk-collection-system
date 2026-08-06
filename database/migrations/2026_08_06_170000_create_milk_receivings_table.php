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
        Schema::create('milk_receivings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->cascadeOnDelete();
            $table->date('receiving_date');
            $table->string('shift'); // 'morning' or 'evening'
            $table->decimal('expected_quantity', 8, 2);
            $table->decimal('received_quantity', 8, 2);
            $table->decimal('expected_fat', 4, 2)->nullable();
            $table->decimal('received_fat', 4, 2)->nullable();
            $table->decimal('expected_snf', 4, 2)->nullable();
            $table->decimal('received_snf', 4, 2)->nullable();
            $table->string('status')->default('received'); // 'received', 'discrepancy'
            $table->foreignId('verified_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['village_id', 'receiving_date', 'shift']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milk_receivings');
    }
};
