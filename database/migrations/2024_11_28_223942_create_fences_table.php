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
        Schema::create('fences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('row_id')->constrained()->onDelete('cascade');
            $table->integer('position'); // Position between houses (1 for between house 1 and 2)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fences');
    }
};
