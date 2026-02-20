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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();

            // One preference row per user
            $table->uuid('user_id')->unique();

            /*
            |--------------------------------------------------------------------------
            | Multiple Preferences (JSON Arrays)
            |--------------------------------------------------------------------------
            | Example:
            | sources: ["guardian", "newsapi"]
            | categories: ["technology", "business"]
            | authors: ["John Doe"]
            */
            $table->json('sources')->nullable();
            $table->json('categories')->nullable();
            $table->json('authors')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Foreign Key
            |--------------------------------------------------------------------------
            */
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};