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
        Schema::create('collections', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key (bigint unsigned)
            $table->foreignId('user_id')->constrained('pkmn.users')->onDelete('cascade'); // Foreign key to the 'users' table
            $table->integer('normal_count')->default(0); // Column for normal count
            $table->integer('holo_count')->default(0); // Column for holo count
            $table->integer('reverse_holo_count')->default(0); // Column for reverse holo count
            $table->integer('count')->default(1); // Column for general count
            $table->timestamps(); // Created at & Updated at timestamps
            $table->string('card_id')->nullable(); // Column for card_id
            $table->foreign('card_id')->references('card_id')->on('pkmn.cards')->onDelete('cascade'); // Foreign key to the 'cards' table
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pkmn_collections');
    }
};