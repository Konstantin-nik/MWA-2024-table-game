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
        Schema::create('houses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('board_id')->constrained()->cascadeOnDelete();
            $table->integer('row_index'); // Row index: 0, 1, or 2
            $table->integer('position'); // Position in row: 0 to N
            $table->integer('number')->nullable(); // Assigned number
            $table->boolean('has_pool')->default(false); // Whether the house has a pool

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('houses');
    }
};
