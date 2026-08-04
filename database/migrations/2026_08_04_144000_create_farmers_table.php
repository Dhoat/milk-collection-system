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
        Schema::create('farmers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->string('farmer_code')->unique();
            $table->string('name');
            $table->string('father_name')->nullable();
            $table->string('mobile');
            $table->string('alternate_mobile')->nullable();
            $table->text('address')->nullable();
            $table->string('gender')->nullable(); // e.g. male, female, other
            $table->date('joining_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->boolean('status')->default(true); // true = active, false = inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmers');
    }
};
