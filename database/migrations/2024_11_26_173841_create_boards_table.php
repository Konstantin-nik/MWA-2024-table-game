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
        Schema::create('boards', function (Blueprint $table) {
            $table->id();

            $table->foreignId('participation_id')->constrained()->cascadeOnDelete();

            $table->json('pool_values');
            $table->integer('number_of_pools')->default(0);

            $table->integer('number_of_agencies')->default(0);

            $table->json('estates_values');

            $table->json('bis_values');
            $table->integer('number_of_bises')->default(0);

            $table->json('skip_penalties');
            $table->integer('number_of_skips')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boards');
    }
};
