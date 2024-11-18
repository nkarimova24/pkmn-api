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
            $table->id(); // This will create an auto-incrementing primary key
            $table->json('tcgplayer')->nullable();
            $table->timestamps(); // Creates `created_at` and `updated_at` columns
            $table->json('cardmarket')->nullable();
            $table->unique('id'); // Ensure uniqueness constraint on the `id` column
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
