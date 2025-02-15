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
            $table->id(); // Auto-increment primary key
            $table->unsignedBigInteger('card_id'); // Add card_id column
            $table->json('tcgplayer')->nullable();
            $table->json('cardmarket')->nullable();
            $table->timestamps(); // created_at & updated_at
    
            $table->unique('id'); // Ensure uniqueness constraint on `id`
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
