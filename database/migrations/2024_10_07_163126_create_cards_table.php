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
            $table->string('card_id')->unique(); //venusaur ex from stellar crown is: "id": "sv7-1",
            $table->string('name');
            $table->string('supertype');
            $table->json('subtypes')->nullable();
            $table->string('hp')->nullable();
            $table->json('types')->nullable();
            $table->string('evolves_from')->nullable();
            $table->json('rules')->nullable();
            $table->json('attacks')->nullable();
            $table->json('weakness')->nullable();
            $table->json('retreat_cost')->nullable();
            $table->integer('converted_retreat_cost')->nullable();
            $table->string('rarity')->nullable();
            $table->json('legalities')->nullable();
            $table->json('images')->nullable();
            $table->unsignedBigInteger('set_id')->nullable();
            $table->timestamps();

            $table->foreign('set_id')->references('id')->on('sets')->onDelete('cascade');
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
