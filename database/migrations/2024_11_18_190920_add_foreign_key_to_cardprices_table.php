<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToCardPricesTable extends Migration
{
    public function up()
{
    Schema::table('cardprices', function (Blueprint $table) {
        $table->foreign('card_id') // Ensure this column exists
              ->references('id') // Make sure `cards` table has `id`, not `card_id`
              ->on('cards')
              ->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('cardprices', function (Blueprint $table) {
        $table->dropForeign(['card_id']);
        $table->dropColumn('card_id'); // Drop the column in rollback
    });
}

}