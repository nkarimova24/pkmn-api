<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key (bigint unsigned)
            $table->string('card_id')->unique(); // Unique card_id column
            $table->string('name'); // Name of the card
            $table->string('supertype'); // Supertype of the card
            $table->json('subtypes')->nullable(); // Subtypes as a JSON column
            $table->string('hp')->nullable(); // HP of the card
            $table->json('types')->nullable(); // Types as a JSON column
            $table->string('evolves_from')->nullable(); // Evolution line (nullable)
            $table->json('rules')->nullable(); // Rules as a JSON column
            $table->json('attacks')->nullable(); // Attacks as a JSON column
            $table->json('weakness')->nullable(); // Weaknesses as a JSON column
            $table->json('retreat_cost')->nullable(); // Retreat cost as a JSON column
            $table->integer('converted_retreat_cost')->nullable(); // Converted retreat cost
            $table->string('rarity')->nullable(); // Rarity of the card
            $table->json('legalities')->nullable(); // Legalities as a JSON column
            $table->json('images')->nullable(); // Images as a JSON column
            $table->string('local_images')->nullable(); // Local image path
            $table->foreignId('set_id')->nullable()->constrained('sets')->onDelete('cascade'); // Foreign key to the 'sets' table
            $table->timestamps(); // Created at & Updated at timestamps
            $table->string('cardprice_id')->nullable(); // Foreign key to 'cardprices'
            $table->foreign('cardprice_id')->references('id')->on('cardprices'); // Foreign key constraint on 'cardprice_id'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards');
    }
};
