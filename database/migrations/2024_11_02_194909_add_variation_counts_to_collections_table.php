<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVariationCountsToCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('collections', function (Blueprint $table) {
            // $table->integer('normal_count')->default(0)->after('card_id');
            // $table->integer('holo_count')->default(0)->after('normal_count');
            // $table->integer('reverse_holo_count')->default(0)->after('holo_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn(['normal_count', 'holo_count', 'reverse_holo_count']);
        });
    }
}
