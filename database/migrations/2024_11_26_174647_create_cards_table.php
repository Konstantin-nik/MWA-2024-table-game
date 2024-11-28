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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();

            $table->foreignId('deck_id')->constrained()->cascadeOnDelete();
            $table->integer('number'); // 1-15 (House Number)
            $table->string('action'); // One of 6 actions
            $table->integer('position')->nullable(); // Position in deck (for order tracking)

            $table->boolean('is_drawn')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
