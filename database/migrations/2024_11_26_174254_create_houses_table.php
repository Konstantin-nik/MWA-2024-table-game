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

            $table->foreignId('row_id')->constrained()->cascadeOnDelete();
            $table->integer('position'); // Position in row: 0 to N
            $table->boolean('has_pool')->default(false);
            $table->boolean('is_pool_constructed')->default(false);
            $table->integer('number')->nullable();

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
