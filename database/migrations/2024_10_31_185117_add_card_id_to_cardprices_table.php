<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cardprices', function (Blueprint $table) {
            $table->foreign('id', 'cardprices_card_id_foreign')  // Foreign key on 'id'
                  ->references('card_id')                     // References 'card_id' in 'cards'
                  ->on('cards')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('cardprices', function (Blueprint $table) {
            $table->dropForeign('cardprices_card_id_foreign'); // Drop by constraint name
        });
    }
};