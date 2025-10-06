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
        Schema::create('joke_reactions', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('joke_id')->constrained()->onDelete('cascade');

            // Reaction (true = positive, false = negative)
            $table->boolean('is_positive');

            $table->timestamps();

            // Ensure one user can react once per joke
            $table->unique(['user_id', 'joke_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('joke_reactions');
    }
};
