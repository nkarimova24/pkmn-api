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
        Schema::create('sets', function (Blueprint $table) {
            $table->id();
            $table->string('set_name');
            $table->string('ptcgo_code')->nullable();
            $table->string('release_date');
            $table->integer('printed_total')->nullable();
            $table->integer('total')->nullable();
        
            $table->json('legalities')->nullable(); 
            $table->json('images')->nullable();

            $table->unsignedBigInteger('series_id');
            $table->foreign('series_id')->references('id')->on('series')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sets');
    }
};
