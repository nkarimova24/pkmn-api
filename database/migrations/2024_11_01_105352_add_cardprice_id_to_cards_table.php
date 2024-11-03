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
        Schema::table('cards', function (Blueprint $table) {
            // Add the cardprice_id column
            $table->string('cardprice_id')->nullable()->after('set_id');

            // Add foreign key constraint
            $table->foreign('cardprice_id')
                  ->references('id')
                  ->on('cardprices')
                  ->onDelete('set null'); // Set to null if the related cardprice is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['cardprice_id']);

            // Drop the cardprice_id column
            $table->dropColumn('cardprice_id');
        });
    }
};
