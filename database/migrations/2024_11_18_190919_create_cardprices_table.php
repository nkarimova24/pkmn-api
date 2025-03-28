<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardpricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cardprices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_id'); 
            $table->json('tcgplayer')->nullable();
            $table->json('cardmarket')->nullable();
            $table->timestamps(); 
    
            $table->unique('id'); 
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cardprices');
    }
}
