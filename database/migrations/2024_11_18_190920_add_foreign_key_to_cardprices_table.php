<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToCardPricesTable extends Migration
{
    public function up()
    {
        Schema::table('cardprices', function (Blueprint $table) {
            // Assuming cardprices has a 'card_id' column, not 'id',
            // that should reference the 'card_id' in the 'cards' table.
            $table->foreign('card_id')  // <-- Corrected column name
                  ->references('card_id')
                  ->on('cards')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('cardprices', function (Blueprint $table) {
            // Drop the foreign key constraint.  Best practice is to name the constraint.
            $table->dropForeign(['card_id']); // <-- Corrected column name
        });
    }
}